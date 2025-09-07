<?php

namespace App\Http\Requests\SystemAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CreateHackathonEditionRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'hackathon_id' => 'required|exists:hackathons,id',
            'year' => 'required|integer|min:2020|max:2030',
            'theme' => 'required|string|max:500',
            'description' => 'required|string|max:2000',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'required|date|before:start_date',
            'registration_end' => 'required|date|after:registration_start|before:start_date',
            'max_teams' => 'required|integer|min:1|max:500',
            'max_team_size' => 'required|integer|min:1|max:6',
            'is_active' => 'boolean',
            'banner_image' => 'nullable|image|max:5120',
            'prize_pool' => 'nullable|numeric|min:0',
            'venue' => 'required|string|max:500',
            'rules' => 'nullable|string|max:5000',
        ];
    }
}
