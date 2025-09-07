<?php

namespace App\Http\Requests\SystemAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTwitterSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('system_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'twitter_api_key' => 'nullable|string|max:255',
            'twitter_api_secret' => 'nullable|string|max:255',
            'twitter_access_token' => 'nullable|string|max:255',
            'twitter_access_token_secret' => 'nullable|string|max:255',
            'auto_tweet_news' => 'boolean',
            'auto_tweet_winners' => 'boolean',
            'hashtags' => 'nullable|string|max:500',
        ];
    }
}
