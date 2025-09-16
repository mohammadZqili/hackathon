<?php

namespace App\Http\Controllers;

use App\Models\News;
use Inertia\Inertia;

class PublicNewsController extends Controller
{
    /**
     * Display the specified news article publicly
     */
    public function show($id)
    {
        $article = News::with(['author', 'edition'])->findOrFail($id);

        // Only show published articles
        if ($article->status !== 'published') {
            abort(404);
        }

        return Inertia::render('Public/News/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'author' => $article->author ? $article->author->name : 'GuacPanel Team',
                'created_at' => $article->created_at->format('F j, Y'),
                'edition' => $article->edition ? $article->edition->name : null,
                'image' => $article->featured_image_path,
            ],
            'meta' => [
                'title' => $article->title . ' - GuacPanel News',
                'description' => substr(strip_tags($article->content), 0, 160),
                'image' => $article->featured_image_path,
                'url' => url('/news/' . $article->id)
            ]
        ]);
    }

    /**
     * Display all published news articles
     */
    public function index()
    {
        $articles = News::with(['author', 'edition'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Public/News/Index', [
            'articles' => $articles,
            'meta' => [
                'title' => 'News - GuacPanel',
                'description' => 'Latest news and updates from GuacPanel Hackathon',
                'url' => url('/news')
            ]
        ]);
    }
}