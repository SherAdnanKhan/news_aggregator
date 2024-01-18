<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    /**
     * Get a list of categories.
     * 
     * @param \Illuminate\Http\Request $request - The HTTP request object
     * @return \Illuminate\Http\JsonResponse - The JSON response
     */
    public function index(Request $request): JsonResponse
    {
        // Get all categories from the database
        $categories = Author::get();

        // Return the JSON response with the categories
        return response()->json($categories);
    }
}
