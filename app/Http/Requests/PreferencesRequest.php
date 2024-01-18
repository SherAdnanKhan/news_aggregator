<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow authenticated users to update preferences
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // 'preferred_sources' => 'array',
            // 'preferred_sources.*' => 'exists:sources,id',
            // 'preferred_categories' => 'array',
            // 'preferred_categories.*' => 'exists:categories,id',
            // 'preferred_authors' => 'array',
            // 'preferred_authors.*' => 'exists:authors,id',
        ];
    }
}

