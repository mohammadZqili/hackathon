<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class PublishNewsRequest extends FormRequest
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
            'publish_at' => 'nullable|date|after:now',
            'post_to_twitter' => 'boolean',
            'twitter_message' => 'nullable|string|max:280',
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
            'publish_at.after' => 'Publish date must be in the future for scheduled publishing.',
            'twitter_message.max' => 'Twitter message cannot exceed 280 characters.',
        ];
    }
}