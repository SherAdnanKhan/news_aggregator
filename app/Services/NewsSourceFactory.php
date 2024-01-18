<?php

namespace App\Services;

use App\Contracts\NewsSourceInterface;
use App\Models\Source;
use App\Services\NewsSources\NewYorkTimes;
use App\Services\NewsSources\TheGuardian;
use App\Services\NewsSources\NewsAPI;

class NewsSourceFactory
{
    /**
     * Create a NewsSourceInterface instance based on the given Source object.
     *
     * @param Source $source The Source object containing the name of the news source.
     * @return NewsSourceInterface|null The created NewsSourceInterface instance or null if no matching source is found.
     */
    public static function create(Source $source): ?NewsSourceInterface
    {
        // Check the name of the source and create the corresponding NewsSourceInterface instance
        if ($source->name === 'NewYork Times') {
            return new NewYorkTimes();
        } elseif ($source->name === 'The Guardian') {
            return new TheGuardian();
        } elseif ($source->name === 'News API') {
            return new NewsAPI();
        } else {
            return null;
        }
    }
}
