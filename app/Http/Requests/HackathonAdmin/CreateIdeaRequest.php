<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CreateIdeaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('hackathon_admin');
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
            'description' => 'required|string|min:100|max:5000',
            'team_id' => 'required|exists:teams,id',
            'track_id' => 'required|exists:tracks,id',
            'problem_statement' => 'required|string|max:2000',
            'proposed_solution' => 'required|string|max:3000',
            'target_audience' => 'required|string|max:1000',
            'technologies' => 'required|array|min:1',
            'technologies.*' => 'string|max:50',
            'pitch_deck' => 'nullable|file|mimes:pdf,pptx|max:10240',
            'demo_url' => 'nullable|url|max:255',
            'repository_url' => 'nullable|url|max:255',
        ];
    }
}
