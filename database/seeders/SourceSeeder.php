<?php

// database/seeders/SourceSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    public function run()
    {
        $sources = [
            ['name' => 'News API', 'api_endpoint' => 'https://newsapi.org/v2/everything'],
            ['name' => 'NewYork Times', 'api_endpoint' => 'https://api.nytimes.com/svc/mostpopular/v2/'],
            ['name' => 'The Guardian', 'api_endpoint' => 'https://content.guardianapis.com/'],
        ];

        foreach ($sources as $source) {
            Source::updateOrCreate(
                ['name' => $source['name']], 
                ['api_endpoint' => $source['api_endpoint']]
            );
        }
    }
}
