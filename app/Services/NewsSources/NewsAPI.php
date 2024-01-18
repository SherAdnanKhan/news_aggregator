<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsSourceInterface;
use App\Models\Source;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use DateTime;

class NewsAPI implements NewsSourceInterface
{
    /**
     * Fetches articles from a given source.
     *
     * @param Source $source The source object.
     * @return array The fetched articles.
     */
    public function fetchArticles(Source $source): array
    {
        // Get the API key from the configuration file
        $apiKey = config('services.newsapi.key');

        // Define the date for 'yesterday'
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Construct the URL with the API key, language parameters, and additional query parameters
        $url = $source->api_endpoint . '?apiKey=' . $apiKey . '&language=en&q=default query&from=' . $yesterday;

        try {
            // Create a new HTTP client
            $client = new Client();

            // Send a GET request to the API endpoint
            $response = $client->request('GET', $url);

            // Get the response body and decode it into an associative array
            $data = json_decode($response->getBody()->getContents(), true);

            // Format the articles and associate them with the source ID
            $articles = $this->formatArticles($data['articles'] ?? [], $source->id);

            // Return the fetched articles
            return $articles;
        } catch (GuzzleException $e) {
            // Handle the exception or log the error
            return [];
        }
    }

    /**
     * Formats an array of articles.
     *
     * @param array $articles The array of articles to be formatted.
     * @param mixed $sourceId The source ID to be used in the formatted articles.
     * @return array The formatted array of articles.
     */
    private function formatArticles(array $articles, $sourceId): array
    {
        return array_map(function ($article) use ($sourceId) {
            $categoryName = $article['source']['name'] ?? 'Uncategorized';
            $authorName = $article['author'] ?? 'Unknown';

            return [
                'title' => $article['title'] ?? 'Untitled',
                'content' => $article['description'] ?? '',
                'trail_text' => $article['content'] ?? '',
                'url' => $article['url'] ?? '#',
                'published_at' => isset($article['publishedAt']) ? (new DateTime($article['publishedAt']))->format('Y-m-d H:i:s') : now(),
                'photo' => $article['urlToImage'] ?? '',
                'author_name' => $authorName,
                'category_name' => $categoryName,
                'source_id' => $sourceId
            ];
        }, $articles);
    }
}
