<?php

namespace App\Http\Requests\TeamLead;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['team_leader', 'team_member']) && 
               !$this->user()->teams()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:teams,name',
            'track_id' => 'required|exists:tracks,id',
            'hackathon_edition_id' => 'required|exists:hackathon_editions,id',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
