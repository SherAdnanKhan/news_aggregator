<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = ['name'];

    /**
     * Get the articles associated with the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Scope a query to only include categories of a given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $term)
    {
        if (isset($term['search']) && $term['search']) {
            return $query->where('name', 'like', '%' . $term['search'] . '%');
        }

        return $query;
    }
}
