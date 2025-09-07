<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['hackathon_admin', 'system_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|in:announcement,update,workshop,deadline,winner,general',
            'featured_image' => 'nullable|image|max:5120', // 5MB max
            'is_featured' => 'boolean',
            'status' => 'in:draft,published,scheduled',
            'publish_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'News title is required.',
            'content.required' => 'News content is required.',
            'category.required' => 'Please select a news category.',
            'featured_image.max' => 'Featured image must not exceed 5MB.',
        ];
    }
}