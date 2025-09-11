<?php

namespace App\Http\Controllers\HackathonAdmin1;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\HackathonEdition;
use App\Http\Requests\HackathonAdmin\CreateNewsRequest;
use App\Http\Requests\HackathonAdmin\UpdateNewsRequest;
use App\Http\Requests\HackathonAdmin\PublishNewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Display a listing of news articles.
     */
    public function index(Request $request): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();

        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $query = News::with('author');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $news = $query->latest()->paginate(15)->withQueryString();

        // Get statistics (news is global, not edition-specific)
        $statistics = [
            'total' => News::count(),
            'draft' => News::where('status', 'draft')->count(),
            'published' => News::where('status', 'published')->count(),
            'archived' => News::where('status', 'archived')->count(),
            'featured' => News::where('is_featured', true)->count(),
        ];

        $categories = [
            'announcement' => 'Announcement',
            'update' => 'Update',
            'workshop' => 'Workshop',
            'deadline' => 'Deadline',
            'winner' => 'Winner',
            'general' => 'General',
        ];

        return Inertia::render('HackathonAdmin/News/Index', [
            'news' => $news,
            'statistics' => $statistics,
            'categories' => $categories,
            'filters' => $request->only(['search', 'status', 'is_featured', 'category']),
        ]);
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create(): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();

        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $categories = [
            'announcement' => 'Announcement',
            'update' => 'Update',
            'workshop' => 'Workshop',
            'deadline' => 'Deadline',
            'winner' => 'Winner',
            'general' => 'General',
        ];

        return Inertia::render('HackathonAdmin/News/Create', [
            'categories' => $categories,
            'currentEdition' => $currentEdition,
        ]);
    }

    /**
     * Store a newly created news article.
     */
    public function store(CreateNewsRequest $request)
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();

        if (!$currentEdition) {
            return back()->with('error', 'No current hackathon edition found.');
        }

        $validated = $request->validated();
        $validated['author_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        // Handle featured image upload if provided
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('news', 'public');
            $validated['featured_image'] = $path;
        }

        $news = News::create($validated);

        return redirect()->route('hackathon-admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    /**
     * Display the specified news article.
     */
    public function show(News $news): Response
    {
        $news->load('author');

        return Inertia::render('HackathonAdmin/News/Show', [
            'news' => $news,
        ]);
    }

    /**
     * Show the form for editing the news article.
     */
    public function edit(News $news): Response
    {
        $categories = [
            'announcement' => 'Announcement',
            'update' => 'Update',
            'workshop' => 'Workshop',
            'deadline' => 'Deadline',
            'winner' => 'Winner',
            'general' => 'General',
        ];

        return Inertia::render('HackathonAdmin/News/Edit', [
            'news' => $news,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified news article.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        $validated = $request->validated();

        // Update slug if title changed
        if (isset($validated['title']) && $validated['title'] !== $news->title) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image upload if provided
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($news->featured_image) {
                \Storage::disk('public')->delete($news->featured_image);
            }

            $path = $request->file('featured_image')->store('news', 'public');
            $validated['featured_image'] = $path;
        }

        $news->update($validated);

        return redirect()->route('hackathon-admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified news article.
     */
    public function destroy(News $news)
    {
        // Delete featured image if exists
        if ($news->featured_image) {
            \Storage::disk('public')->delete($news->featured_image);
        }

        $news->delete();

        return redirect()->route('hackathon-admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    /**
     * Publish a news article.
     */
    public function publish(PublishNewsRequest $request, News $news)
    {
        $validated = $request->validated();

        if (isset($validated['publish_at'])) {
            $news->publish_at = $validated['publish_at'];
            $news->status = 'scheduled';
        } else {
            $news->publish_at = now();
            $news->status = 'published';
        }

        $news->save();

        // Post to Twitter if requested
        if ($request->boolean('post_to_twitter')) {
            $this->postToTwitter($news);
        }

        return back()->with('success', 'News article published successfully.');
    }

    /**
     * Post news to Twitter.
     */
    public function tweet(News $news)
    {
        if ($news->tweeted_at) {
            return back()->with('error', 'This article has already been tweeted.');
        }

        $success = $this->postToTwitter($news);

        if ($success) {
            return back()->with('success', 'News article posted to Twitter successfully.');
        }

        return back()->with('error', 'Failed to post to Twitter. Please check your Twitter configuration.');
    }

    /**
     * Schedule news for publishing.
     */
    public function schedule(Request $request, News $news)
    {
        $request->validate([
            'publish_at' => 'required|date|after:now',
            'post_to_twitter' => 'boolean',
        ]);

        $news->publish_at = $request->publish_at;
        $news->status = 'scheduled';
        $news->twitter_scheduled = $request->boolean('post_to_twitter');
        $news->save();

        return back()->with('success', 'News article scheduled successfully.');
    }

    /**
     * Post to Twitter helper method.
     */
    private function postToTwitter(News $news): bool
    {
        try {
            // Here you would implement Twitter API integration
            // For now, we'll simulate success and update the timestamp

            $news->tweeted_at = now();
            $news->save();

            return true;
        } catch (\Exception $e) {
            \Log::error('Twitter posting failed: ' . $e->getMessage());
            return false;
        }
    }
}
