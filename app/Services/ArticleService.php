<?php

namespace App\Services;

use App\Models\Article;
use DateTime;

class ArticleService
{
    /**
     * Get filtered articles based on the provided request.
     *
     * @param Request $request The request object containing the filter parameters.
     * @return \Illuminate\Database\Eloquent\Builder The filtered articles query.
     */
    public function getFilteredArticles($request)
    {
        // Create the base query
        $query = Article::query();

        // Apply searchTerm filter
        $this->applySearchTermFilter($query, $request->searchTerm);

        // Apply date range filter
        $this->applyDateRangeFilter($query, $request->startDate, $request->endDate);

        // Apply category, source, and author filters
        $this->applyAdditionalFilters($query, $request->categories, 'category_id');
        $this->applyAdditionalFilters($query, $request->sources, 'source_id');
        $this->applyAdditionalFilters($query, $request->authors, 'author_id');

        // Return the filtered articles query
        return $query;
    }

    /**
     * Apply search term filter to the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query  The query object.
     * @param  string|null  $searchTerm  The search term to filter by.
     * @return void
     */
    private function applySearchTermFilter($query, $searchTerm)
    {
        // Check if search term is provided
        if ($searchTerm) {
            // Convert search term to lowercase
            $searchTerm = strtolower($searchTerm);

            // Apply search term filter to the query
            $query->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"]);
        }
    }

    /**
     * Apply date range filter to the query based on start and end dates.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string|null  $startDate
     * @param  string|null  $endDate
     * @return void
     */
    private function applyDateRangeFilter(&$query, $startDate, $endDate)
    {
        if ($startDate && $endDate && $startDate !== 'null' && $endDate !== 'null') {
            $startDateString = preg_replace('/\s+\(.+\)$/', '', $startDate);
            $endDateString = preg_replace('/\s+\(.+\)$/', '', $endDate);

            $startDate = new DateTime($startDateString);
            $endDate = new DateTime($endDateString);

            $query->whereDate('published_at', '>=', $startDate->format('Y-m-d'))
                ->whereDate('published_at', '<=', $endDate->format('Y-m-d'));
        }
    }

    /**
     * Apply additional filters to the query.
     *
     * @param \Illuminate\Database\Query\Builder $query The query builder instance.
     * @param string|null $filterData The filter data string.
     * @param string $column The column to filter on.
     * @return void
     */
    private function applyAdditionalFilters($query, $filterData, $column)
    {
        // Check if filter data exists
        if ($filterData) {
            // Split the filter data into an array
            $filterArray = explode(",", $filterData);

            // Apply the whereIn clause to the query using the column and filter array
            $query->whereIn($column, $filterArray);
        }
    }

    /**
     * Apply user preference filters to the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $preferredSources
     * @param array $preferredCategories
     * @param array $preferredAuthors
     * @return void
     */
    public function applyUserPreferences(&$query, array $preferredSources, array $preferredCategories, array $preferredAuthors)
    {
        if (!empty($preferredSources)) {
            $query->whereIn('source_id', $preferredSources);
        }

        if (!empty($preferredCategories)) {
            $query->orWhereIn('category_id', $preferredCategories);
        }

        if (!empty($preferredAuthors)) {
            $query->orWhereIn('author_id', $preferredAuthors);
        }
    }

    /**
     * Retrieve a collection of recommended articles based on the given article.
     *
     * @param Article $article The article to base the recommendations on.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecommendedArticles(Article $article)
    {
        // Retrieve articles that are not the same as the given article
        $query = Article::where('id', '!=', $article->id);

        // Retrieve articles that have the same category, author, or source as the given article
        $query->where(function ($query) use ($article) {
            $query->orWhere('category_id', $article->category_id)
                ->orWhere('author_id', $article->author_id)
                ->orWhere('source_id', $article->source_id);
        });

        // Load the category, source, and author relationships for the articles
        $query->with(['category', 'source', 'author']);

        // Limit the number of articles to 5
        $query->take(5);

        // Execute the query and retrieve the recommended articles
        return $query->get();
    }

       /**
     * Retrieve a collection of latest articles based on the given article.
     *
     * @param Article $article The article to base the recommendations on.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatestArticles(Article $article)
    {
        // Retrieve articles that are not the same as the given article
        $query = Article::where('id', '!=', $article->id);

        // Retrieve articles that have the same category, author, or source as the given article
        $query->where(function ($query) use ($article) {
            $query->orWhere('category_id', $article->category_id)
                ->orWhere('author_id', $article->author_id)
                ->orWhere('source_id', $article->source_id);
        });

        // Load the category, source, and author relationships for the articles
        $query->with(['category', 'source', 'author']);

        // Limit the number of articles to 5
        $query->orderBy('published_at', 'desc')->take(5);

        // Execute the query and retrieve the recommended articles
        return $query->get();
    }
}
