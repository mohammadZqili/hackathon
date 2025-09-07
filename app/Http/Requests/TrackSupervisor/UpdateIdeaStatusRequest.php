<?php

namespace App\Http\Requests\TrackSupervisor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIdeaStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['supervisor', 'hackathon_admin', 'system_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,winner,finalist',
            'status_reason' => 'nullable|string|max:500',
            'notify_team' => 'boolean',
            'internal_notes' => 'nullable|string|max:1000',
        ];
    }
}
