<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'api_endpoint'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function scopeFilter($query, $term)
    {
        if (isset($term['search']) && $term['search']) {
            return $query->where('name', 'like', '%' . $term['search'] . '%');
        }

        return $query;
    }
}

