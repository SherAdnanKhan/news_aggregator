<?php

namespace App\Http\Controllers\Backend;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Get a paginated list of filtered articles.
     *
     * @param  Request  $request  The HTTP request object.
     *
     * @return JsonResponse  The JSON response containing the paginated articles.
     */
    public function index(Request $request): JsonResponse
    {
        // Get the filtered articles query
        $query = $this->articleService->getFilteredArticles($request);

        // Paginate the articles
        $articles = $query->with('author:id,name')->orderBy("published_at", 'desc')->paginate(6);

        // Return the JSON response
        return response()->json($articles);
    }

    /**
     * Retrieves personalized feed for the authenticated user.
     *
     * @param Request $request The request object.
     * @return JsonResponse The JSON response containing the personalized feed.
     */
    public function personalizedFeed(Request $request): JsonResponse
    {
        // Get the authenticated user
        $user = $request->user();

        // Get the IDs of the preferred sources, categories, and authors for the user
        $preferredSources = $user->preferredSources()->pluck('source_id')->toArray();
        $preferredCategories = $user->preferredCategories()->pluck('category_id')->toArray();
        $preferredAuthors = $user->preferredAuthors()->pluck('author_id')->toArray();

        // Get the filtered articles based on the request
        $query = $this->articleService->getFilteredArticles($request);

        // Apply user preferences to the query
        $this->articleService->applyUserPreferences($query, $preferredSources, $preferredCategories, $preferredAuthors);

        // Paginate the articles
        $articles = $query->with('author:id,name')->orderBy("published_at", 'desc')->paginate(6);

        // Return the articles as a JSON response
        return response()->json($articles);
    }

    /**
     * Show the details of an article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Eager load the category, source, and author relationships
        $article = Article::with(['category', 'source', 'author'])->find($id);

        // If article not found, return 404 response
        if (!$article) {
            return response()->json(['message' => 'Article not found.'], 404);
        }

        // Get recommended articles for the current article
        $recommendedArticles = $this->articleService->getRecommendedArticles($article);
        $latestArticles = $this->articleService->getLatestArticles($article);


        // Format the response
        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'content' => $article->content,
            'url' => $article->url,
            'image' => $article->photo,
            'published_at' => $article->published_at,
            'category' => $article->category ? $article->category->name : null,
            'source' => $article->source ? $article->source->name : null,
            'author' => $article->author ? $article->author->name : null,
            'recommendedArticles' => $recommendedArticles,
            'latestArticles' => $latestArticles
        ]);
    }
}
