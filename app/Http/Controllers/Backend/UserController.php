<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreferencesRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get the preferences of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the preferred sources and categories of the user.
     */
    public function getPreferences(): JsonResponse
    {
        // Get the authenticated user
        $user = Auth::user();

        // Load user's preferred sources and categories
        $preferredSources = $user->preferredSources()->pluck('source_id');

        $preferredCategories = Category::select('id', 'name')->whereIn(
            'id',
            $user->preferredCategories()->pluck('category_id')
        )->get();

        $preferredAuthors = Author::select("id", "name")->whereIn(
            'id',
            $user->preferredAuthors()->pluck('author_id')
        )->get();

        // Return the preferences as a JSON response
        return response()->json(
            [
                'preferred_sources' => $preferredSources,
                'preferred_categories' => $preferredCategories,
                'preferred_authors' => $preferredAuthors,

            ]
        );
    }

    /**
     * Update the preferences of the authenticated user.
     *
     * @param  \App\Http\Requests\PreferencesRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferences(PreferencesRequest $request): JsonResponse
    {
        // Get the authenticated user
        $user = Auth::user();

        $preferredSources = $request->input('preferred_sources', []);

        // Ensure 'preferred_categories' key exists and is an array
        $preferredCategories = isset($request['preferred_categories']) && is_array($request['preferred_categories'])
            ? array_column($request['preferred_categories'], 'value')
            : [];
        // Ensure 'preferred_authors' key exists and is an array
        $preferredAuthors = isset($request['preferred_authors']) && is_array($request['preferred_authors'])
            ?  array_column($request['preferred_authors'], 'value')
            : [];

        // Update user's preferred sources
        $user->preferredSources()->sync($preferredSources);
        $user->preferredCategories()->sync($preferredCategories);
        $user->preferredAuthors()->sync($preferredAuthors);

        // Return a JSON response with a success message
        return response()->json(['message' => 'Preferences updated successfully.']);
    }

    /**
     * Update the settings of the authenticated user.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSettings(UpdateSettingsRequest $request): JsonResponse
    {
        // Get the authenticated user
        $user = Auth::user();

        // Update the user settings with the validated request data
        $user->update($request->validated());

        // Return a JSON response with a success message
        return response()->json(['message' => 'Settings updated successfully.']);
    }
}
