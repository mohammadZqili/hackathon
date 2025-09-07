<?php

namespace App\Http\Requests\SystemAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHackathonEditionRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer|min:2020|max:2030',
            'theme' => 'sometimes|required|string|max:500',
            'description' => 'sometimes|required|string|max:2000',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'registration_start' => 'sometimes|required|date|before:start_date',
            'registration_end' => 'sometimes|required|date|after:registration_start|before:start_date',
            'max_teams' => 'sometimes|required|integer|min:1|max:500',
            'max_team_size' => 'sometimes|required|integer|min:1|max:6',
            'is_active' => 'boolean',
            'banner_image' => 'nullable|image|max:5120',
            'prize_pool' => 'nullable|numeric|min:0',
            'venue' => 'sometimes|required|string|max:500',
            'rules' => 'nullable|string|max:5000',
        ];
    }
}
