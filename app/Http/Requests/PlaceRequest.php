<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // only logged in users reach this (auth middleware), owner checks come with the policy
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function attributes(): array
    {
        // nicer field names in the error messages ("The category field is required.")
        return [
            'category_id' => 'category',
        ];
    }
}
