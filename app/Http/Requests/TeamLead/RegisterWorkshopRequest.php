<?php

namespace App\Http\Requests\TeamLead;

use Illuminate\Foundation\Http\FormRequest;

class RegisterWorkshopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['team_leader', 'team_member']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'workshop_id' => 'required|exists:workshops,id',
            'team_members' => 'required|array|min:1',
            'team_members.*' => 'exists:users,id',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
