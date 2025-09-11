<?php

namespace App\Http\Requests\TeamLead;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIdeaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('team_leader') && 
               $this->user()->teams()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|min:100|max:5000',
            'problem_statement' => 'sometimes|required|string|max:2000',
            'proposed_solution' => 'sometimes|required|string|max:3000',
            'target_audience' => 'sometimes|required|string|max:1000',
            'technologies' => 'sometimes|required|array|min:1',
            'technologies.*' => 'string|max:50',
            'pitch_deck' => 'nullable|file|mimes:pdf,pptx|max:10240',
            'demo_url' => 'nullable|url|max:255',
            'repository_url' => 'nullable|url|max:255',
        ];
    }
}
