<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255|unique:teams,name,' . $this->route('team'),
            'leader_id' => 'sometimes|required|exists:users,id',
            'track_id' => 'sometimes|required|exists:tracks,id',
            'members' => 'sometimes|array|min:1|max:4',
            'members.*' => 'exists:users,id|distinct',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,approved,rejected',
        ];
    }
}
