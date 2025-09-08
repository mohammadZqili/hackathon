<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with(['hackathon'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('SystemAdmin/News/Index', [
            'news' => $news
        ]);
    }

    public function create()
    {
        return Inertia::render('SystemAdmin/News/Create');
    }

    public function store(Request $request)
    {
        // TODO: Implement store functionality
        return redirect()->route('system-admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    public function show(News $news)
    {
        return Inertia::render('SystemAdmin/News/Show', [
            'news' => $news
        ]);
    }

    public function edit(News $news)
    {
        return Inertia::render('SystemAdmin/News/Edit', [
            'news' => $news
        ]);
    }

    public function update(Request $request, News $news)
    {
        // TODO: Implement update functionality
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
        // TODO: Implement publish functionality
        return response()->json(['message' => 'Publish functionality to be implemented']);
    }

    public function unpublish(News $news)
    {
        // TODO: Implement unpublish functionality
        return response()->json(['message' => 'Unpublish functionality to be implemented']);
    }
}
