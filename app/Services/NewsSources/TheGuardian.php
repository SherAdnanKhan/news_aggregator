<?php

namespace App\Services\NewsSources;

use DateTime;
use App\Models\Source;
use GuzzleHttp\Client;
use App\Contracts\NewsSourceInterface;
use GuzzleHttp\Exception\GuzzleException;

class TheGuardian implements NewsSourceInterface
{
    /**
     * Fetches articles from a given source.
     *
     * @param Source $source The source from which to fetch articles.
     * @return array The fetched articles.
     */
    public function fetchArticles(Source $source): array
    {
        // Get the API key from the configuration file.
        $apiKey = config('services.theguardian.key');

        // Construct the API endpoint URL with the API key.

        $url = $source->api_endpoint . 'search?order-by=newest&show-fields=all&api-key=' . $apiKey;

        try {
            // Create a new HTTP client.
            $client = new Client();

            // Send a GET request to the API endpoint.
            $response = $client->request('GET', $url);

            // Decode the response body to an associative array.
            $data = json_decode($response->getBody()->getContents(), true);

            // Format the articles and associate them with the source ID.
            $articles = $this->formatArticles($data['response']['results'] ?? [], $source->id);
            // Return the fetched articles.
            return $articles;
        } catch (GuzzleException $e) {
            // Return an empty array in case of an error.
            return [];
        }
    }

    /**
     * Formats an array of articles.
     *
     * @param array $articles The array of articles to be formatted.
     * @param mixed $sourceId The ID of the source.
     * @return array The formatted array of articles.
     */
    private function formatArticles(array $articles, $sourceId): array
    {
        return array_map(function ($article) use ($sourceId) {
            $categoryName = $article['sectionName'] ?? 'Uncategorized';
            $authorName = $article['fields']['byline'] ?? 'Unknown';

            return [
                'title' => $article['webTitle'] ?? 'Untitled',
                'trail_text' => $article['fields']['trailText'] ?? '',
                'content' => $article['fields']['body'] ?? '',
                'url' => $article['webUrl'] ?? '#',
                'published_at' => (new DateTime($article['webPublicationDate']))->format('Y-m-d H:i:s'),
                'photo' => $article['fields']['thumbnail'] ?? '',
                'author_name' => $authorName,
                'category_name' => $categoryName,
                'source_id' => $sourceId
            ];
        }, $articles);
    }
}
