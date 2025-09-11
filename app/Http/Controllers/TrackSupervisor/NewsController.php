<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with(['author'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('TrackSupervisor/News/Index', [
            'news' => $news
        ]);
    }

    public function create()
    {
        // Define static categories for now
        $categories = collect([
            ['id' => 1, 'name' => 'Announcements'],
            ['id' => 2, 'name' => 'Updates'],
            ['id' => 3, 'name' => 'Events'],
            ['id' => 4, 'name' => 'Workshops'],
            ['id' => 5, 'name' => 'Winners'],
            ['id' => 6, 'name' => 'General']
        ]);

        return Inertia::render('TrackSupervisor/News/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|integer|in:1,2,3,4,5,6',
            'video_url' => 'nullable|url',
            'twitter_message' => 'nullable|string|max:280',
            'publish_to_twitter' => 'boolean',
            'keywords' => 'nullable|string',
            'main_image' => 'nullable|string', // Now expects path from FilePond
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|string' // Now expects paths from FilePond
        ]);

        // Handle main image - move from temp to permanent storage
        $featuredImagePath = null;
        if (!empty($validated['main_image'])) {
            $tempPath = $validated['main_image'];
            if (Storage::disk('public')->exists($tempPath)) {
                $filename = basename($tempPath);
                $newPath = 'news/featured/' . $filename;
                Storage::disk('public')->move($tempPath, $newPath);
                $featuredImagePath = $newPath;
            }
        }

        // Handle gallery images - move from temp to permanent storage
        $galleryImages = [];
        if (!empty($validated['gallery_images'])) {
            foreach ($validated['gallery_images'] as $tempPath) {
                if (Storage::disk('public')->exists($tempPath)) {
                    $filename = basename($tempPath);
                    $newPath = 'news/gallery/' . $filename;
                    Storage::disk('public')->move($tempPath, $newPath);
                    $galleryImages[] = $newPath;
                }
            }
        }

        // Process keywords into tags array
        $tags = [];
        if (!empty($validated['keywords'])) {
            $tags = array_map('trim', explode(',', $validated['keywords']));
        }

        // Create the news article
        $news = News::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'excerpt' => Str::limit(strip_tags($validated['content']), 200),
            'featured_image_path' => $featuredImagePath,
            'status' => 'draft',
            'author_id' => auth()->id(),
            'tags' => $tags,
            'auto_post_twitter' => $validated['publish_to_twitter'] ?? false,
            'seo_data' => [
                'keywords' => $validated['keywords'] ?? '',
                'video_url' => $validated['video_url'] ?? '',
                'twitter_message' => $validated['twitter_message'] ?? '',
                'gallery_images' => $galleryImages
            ]
        ]);

        return redirect()->route('system-admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    public function show(News $news)
    {
        return Inertia::render('TrackSupervisor/News/Show', [
            'news' => $news
        ]);
    }

    public function edit(News $news)
    {
        // Define static categories for now
        $categories = collect([
            ['id' => 1, 'name' => 'Announcements'],
            ['id' => 2, 'name' => 'Updates'],
            ['id' => 3, 'name' => 'Events'],
            ['id' => 4, 'name' => 'Workshops'],
            ['id' => 5, 'name' => 'Winners'],
            ['id' => 6, 'name' => 'General']
        ]);

        // Load additional data for the news article
        $news->load('author');

        // Add computed attributes
        $news->main_image_url = $news->featured_image_url;
        $news->gallery_images = $news->seo_data['gallery_images'] ?? [];
        $news->video_url = $news->seo_data['video_url'] ?? '';
        $news->twitter_message = $news->seo_data['twitter_message'] ?? '';
        $news->keywords = is_array($news->tags) ? implode(', ', $news->tags) : '';
        $news->publish_to_twitter = $news->auto_post_twitter;

        return Inertia::render('TrackSupervisor/News/Edit', [
            'article' => $news,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, News $news)
    {
        // Check if this is a partial update (e.g., just toggling Twitter publish)
        if ($request->has('publish_to_twitter') && count($request->all()) === 1) {
            $news->update([
                'auto_post_twitter' => $request->boolean('publish_to_twitter')
            ]);

            return redirect()->route('system-admin.news.index')
                ->with('success', 'Twitter publishing setting updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|integer|in:1,2,3,4,5,6',
            'video_url' => 'nullable|url',
            'twitter_message' => 'nullable|string|max:280',
            'publish_to_twitter' => 'boolean',
            'keywords' => 'nullable|string',
            'main_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|string'
        ]);

        // Handle main image - move from temp to permanent storage
        $featuredImagePath = $news->featured_image_path;
        if (!empty($validated['main_image'])) {
            $tempPath = $validated['main_image'];
            if (Storage::disk('public')->exists($tempPath)) {
                // Delete old image if exists
                if ($featuredImagePath) {
                    Storage::disk('public')->delete($featuredImagePath);
                }
                $filename = basename($tempPath);
                $newPath = 'news/featured/' . $filename;
                Storage::disk('public')->move($tempPath, $newPath);
                $featuredImagePath = $newPath;
            }
        }

        // Handle gallery images - merge with existing
        $existingGallery = $news->seo_data['gallery_images'] ?? [];
        $galleryImages = $existingGallery;

        if (!empty($validated['gallery_images'])) {
            foreach ($validated['gallery_images'] as $tempPath) {
                if (Storage::disk('public')->exists($tempPath)) {
                    $filename = basename($tempPath);
                    $newPath = 'news/gallery/' . $filename;
                    Storage::disk('public')->move($tempPath, $newPath);
                    $galleryImages[] = $newPath;
                }
            }
        }

        // Process keywords into tags array
        $tags = [];
        if (!empty($validated['keywords'])) {
            $tags = array_map('trim', explode(',', $validated['keywords']));
        }

        // Update the news article
        $news->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'excerpt' => Str::limit(strip_tags($validated['content']), 200),
            'featured_image_path' => $featuredImagePath,
            'tags' => $tags,
            'auto_post_twitter' => $validated['publish_to_twitter'] ?? false,
            'seo_data' => [
                'keywords' => $validated['keywords'] ?? '',
                'video_url' => $validated['video_url'] ?? '',
                'twitter_message' => $validated['twitter_message'] ?? '',
                'gallery_images' => $galleryImages
            ]
        ]);

        return redirect()->route('system-admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('system-admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    public function publish(News $news)
    {
        $news->publish();

        return redirect()->route('system-admin.news.index')
            ->with('success', 'News article published successfully.');
    }

    public function unpublish(News $news)
    {
        $news->update(['status' => 'draft']);

        return redirect()->route('system-admin.news.index')
            ->with('success', 'News article unpublished successfully.');
    }

    /**
     * Handle temporary file uploads for FilePond
     */
    public function uploadTemp(Request $request)
    {
        // Get the first uploaded file regardless of field name
        $file = null;
        $fieldName = null;

        foreach ($request->files->keys() as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $fieldName = $key;
                break;
            }
        }

        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 422);
        }

        // Validate the file
        $validator = Validator::make(
            [$fieldName => $file],
            [$fieldName => 'required|file|image|max:5120'] // 5MB max
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('temp/news', $filename, 'public');

        // Store in session for later retrieval
        $tempFiles = session('temp_news_files', []);
        $tempFiles[] = $path;
        session(['temp_news_files' => $tempFiles]);

        return response()->json([
            'path' => $path,
            'filename' => $filename
        ]);
    }

    /**
     * Delete temporary uploaded files
     */
    public function deleteTemp(Request $request)
    {
        $path = $request->input('path');

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);

            // Remove from session
            $tempFiles = session('temp_news_files', []);
            $tempFiles = array_diff($tempFiles, [$path]);
            session(['temp_news_files' => $tempFiles]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Clean up old temporary files
     */
    public function cleanupTemp()
    {
        $tempPath = 'temp/news';
        $files = Storage::disk('public')->files($tempPath);

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            // Delete files older than 24 hours
            if (now()->timestamp - $lastModified > 86400) {
                Storage::disk('public')->delete($file);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Display the media center
     */
    public function mediaCenter()
    {
        // Get all news articles with their media
        $articles = News::select('id', 'title')->orderBy('created_at', 'desc')->get();

        // Collect all media files from news articles
        $media = [];
        $newsItems = News::whereNotNull('featured_image_path')
            ->orWhereNotNull('seo_data->gallery_images')
            ->get();

        foreach ($newsItems as $news) {
            // Add featured image
            if ($news->featured_image_path) {
                $media[] = [
                    'id' => 'featured_' . $news->id,
                    'url' => asset('storage/' . $news->featured_image_path),
                    'type' => 'image',
                    'title' => $news->title . ' - Featured Image',
                    'alt' => $news->title,
                    'article' => $news->title,
                    'articleId' => $news->id,
                    'uploadedAt' => $news->created_at,
                    'path' => $news->featured_image_path
                ];
            }

            // Add gallery images
            $galleryImages = $news->seo_data['gallery_images'] ?? [];
            foreach ($galleryImages as $index => $imagePath) {
                $media[] = [
                    'id' => 'gallery_' . $news->id . '_' . $index,
                    'url' => asset('storage/' . $imagePath),
                    'type' => 'image',
                    'title' => $news->title . ' - Gallery Image ' . ($index + 1),
                    'alt' => $news->title,
                    'article' => $news->title,
                    'articleId' => $news->id,
                    'uploadedAt' => $news->created_at,
                    'path' => $imagePath
                ];
            }

            // Add videos if any
            $videoUrl = $news->seo_data['video_url'] ?? null;
            if ($videoUrl) {
                $media[] = [
                    'id' => 'video_' . $news->id,
                    'url' => $videoUrl,
                    'type' => 'video',
                    'title' => $news->title . ' - Video',
                    'alt' => $news->title,
                    'article' => $news->title,
                    'articleId' => $news->id,
                    'uploadedAt' => $news->created_at,
                    'path' => null
                ];
            }
        }

        return Inertia::render('TrackSupervisor/News/MediaCenter', [
            'media' => $media,
            'articles' => $articles
        ]);
    }

    /**
     * Get single media item
     */
    public function getMedia($mediaId)
    {
        // Parse media ID to find the source
        $parts = explode('_', $mediaId);
        $type = $parts[0]; // 'featured', 'gallery', or 'video'
        $newsId = $parts[1];

        $news = News::findOrFail($newsId);

        if ($type === 'featured') {
            return response()->json([
                'url' => asset('storage/' . $news->featured_image_path),
                'type' => 'image',
                'title' => $news->title
            ]);
        }

        // Handle other types...
        return response()->json(['error' => 'Media not found'], 404);
    }

    /**
     * Delete media item
     */
    public function deleteMedia($mediaId)
    {
        // Parse media ID to find the source
        $parts = explode('_', $mediaId);
        $type = $parts[0]; // 'featured', 'gallery', or 'video'
        $newsId = $parts[1];

        $news = News::findOrFail($newsId);

        if ($type === 'featured') {
            // Delete the featured image
            if ($news->featured_image_path && Storage::disk('public')->exists($news->featured_image_path)) {
                Storage::disk('public')->delete($news->featured_image_path);
                $news->update(['featured_image_path' => null]);
            }
        } elseif ($type === 'gallery' && isset($parts[2])) {
            // Delete gallery image
            $index = (int)$parts[2];
            $galleryImages = $news->seo_data['gallery_images'] ?? [];

            if (isset($galleryImages[$index])) {
                $imagePath = $galleryImages[$index];
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                // Remove from array and reindex
                array_splice($galleryImages, $index, 1);

                $seoData = $news->seo_data;
                $seoData['gallery_images'] = $galleryImages;
                $news->update(['seo_data' => $seoData]);
            }
        }

        return response()->json(['success' => true]);
    }
}
