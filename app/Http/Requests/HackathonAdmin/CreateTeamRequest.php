<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:teams,name',
            'leader_id' => 'required|exists:users,id',
            'track_id' => 'required|exists:tracks,id',
            'hackathon_edition_id' => 'required|exists:hackathon_editions,id',
            'members' => 'array|min:1|max:4',
            'members.*' => 'exists:users,id|distinct',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
