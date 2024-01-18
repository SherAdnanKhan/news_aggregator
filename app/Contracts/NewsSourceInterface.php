<?php

namespace App\Contracts;

use App\Models\Source;

interface NewsSourceInterface
{
    public function fetchArticles(Source $source): array;
}
