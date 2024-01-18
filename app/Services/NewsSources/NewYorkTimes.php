<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsSourceInterface;
use App\Models\Source;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NewYorkTimes implements NewsSourceInterface
{
    /**
     * Fetch articles from a given source.
     *
     * @param Source $source The source to fetch articles from.
     * @return array The fetched articles.
     */
    public function fetchArticles(Source $source): array
    {
        // Get the API key from the configuration
        $apiKey = config('services.newyorktimes.key');

        // Construct the API endpoint URL
        $url = $source->api_endpoint . 'viewed/1.json?api-key=' . $apiKey;

        try {
            // Create a new HTTP client
            $client = new Client();

            // Send a GET request to the API endpoint
            $response = $client->request('GET', $url);

            // Decode the response body into an associative array
            $data = json_decode($response->getBody()->getContents(), true);

            // Format the articles and associate them with the source
            $articles = $this->formatArticles($data['results'] ?? [], $source->id);

            // Return the fetched articles
            return $articles;
        } catch (GuzzleException $e) {
            // Return an empty array if an error occurs
            return [];
        }
    }

    /**
     * Formats an array of articles.
     *
     * @param array $articles The array of articles to format.
     * @param mixed $sourceId The source ID to use for the formatted articles.
     * @return array The formatted array of articles.
     */
    private function formatArticles(array $articles, $sourceId): array
    {
        return array_map(function ($article) use ($sourceId) {
            $categoryName = $article['section'] ?? 'Uncategorized';
            $authorName = $article['byline'] ? str_replace('By ', '', $article['byline']) : 'Unknown';

            return [
                'title' => $article['title'] ?? 'Untitled',
                'content' => $article['abstract'] ?? '',
                'trail_text' => $article['abstract'] ?? '',
                'url' => $article['url'] ?? '#',
                'published_at' => isset($article['published_date']) ? (new DateTime($article['published_date']))->format('Y-m-d H:i:s') : now(),
                'photo' => $article['media'][0]['media-metadata'][0]['url'] ?? '',
                'author_name' => $authorName,
                'category_name' => $categoryName,
                'source_id' => $sourceId
            ];
        }, $articles);
    }
}
