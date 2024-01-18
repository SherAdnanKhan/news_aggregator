<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\JsonResponse;

class SourceController extends Controller
{
    /**
     * Display a listing of sources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // Retrieve all sources from the database
        $sources = Source::all();

        // Return the sources as a JSON response
        return response()->json($sources);
    }
}
