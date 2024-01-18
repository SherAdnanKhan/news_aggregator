<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Console\Command;
use App\Models\Source;
use App\Services\NewsSourceFactory;

class FetchNewsData extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news data from all sources';

    public function handle()
    {
        $sources = Source::get();

        foreach ($sources as $source) {
            try {
                $newsSource = NewsSourceFactory::create($source);
                $articles = $newsSource->fetchArticles($source);
                foreach ($articles as $articleData) {
                    $category = Category::firstOrCreate(['name' => $articleData['category_name']]);
                    $author = Author::firstOrCreate(['name' => $articleData['author_name']]);

                    Article::updateOrCreate(
                        [
                            'title' => $articleData['title'],
                            'url' => $articleData['url']
                        ],
                        [
                            'content' => $articleData['content'],
                            'trail_text' => $articleData['trail_text'],
                            'published_at' => $articleData['published_at'],
                            'photo' => $articleData['photo'],
                            'author_id' => $author->id,
                            'category_id' => $category->id,
                            'source_id' => $articleData['source_id']
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }

        $this->info("News data fetched successfully.");
    }
}
