<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $place = $this->route('place');

        // editing: only the owner (PlacePolicy), checked before validation so others get a 403
        if ($place) {
            return $this->user()->can('update', $place);
        }

        // adding: any logged in user (the auth middleware already checked that)
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
            // a photo is needed when adding a place, but optional when editing (keeps the old one)
            'photo' => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'max:8192'],
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
