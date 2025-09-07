<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class ReviewIdeaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['hackathon_admin', 'system_admin', 'track_supervisor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,under_review,approved,rejected,needs_revision',
            'supervisor_id' => 'nullable|exists:users,id',
            'feedback' => 'nullable|string|max:2000',
            'scores' => 'nullable|array',
            'scores.innovation' => 'nullable|integer|min:0|max:25',
            'scores.feasibility' => 'nullable|integer|min:0|max:25',
            'scores.impact' => 'nullable|integer|min:0|max:25',
            'scores.presentation' => 'nullable|integer|min:0|max:25',
            'notify_team' => 'boolean',
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
            'status.required' => 'Review status is required.',
            'status.in' => 'Invalid review status selected.',
            'scores.*.max' => 'Each scoring criterion has a maximum of 25 points.',
            'feedback.max' => 'Feedback cannot exceed 2000 characters.',
        ];
    }
}