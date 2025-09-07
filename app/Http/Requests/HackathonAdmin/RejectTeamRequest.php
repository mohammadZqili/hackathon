<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class RejectTeamRequest extends FormRequest
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
            'reason' => 'required|string|min:10|max:500',
            'notify_leader' => 'boolean',
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
            'reason.required' => 'A reason for rejection is required.',
            'reason.min' => 'The rejection reason must be at least 10 characters.',
        ];
    }
}