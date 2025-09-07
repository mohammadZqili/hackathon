<?php

namespace App\Http\Requests\TrackSupervisor;

use Illuminate\Foundation\Http\FormRequest;

class CreateFeedbackRequest extends FormRequest
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
            'idea_id' => 'required|exists:ideas,id',
            'feedback_text' => 'required|string|max:2000',
            'score' => 'required|integer|min:0|max:100',
            'criteria_scores' => 'required|array',
            'criteria_scores.innovation' => 'required|integer|min:0|max:25',
            'criteria_scores.technical' => 'required|integer|min:0|max:25',
            'criteria_scores.feasibility' => 'required|integer|min:0|max:25',
            'criteria_scores.presentation' => 'required|integer|min:0|max:25',
            'recommendations' => 'nullable|string|max:1000',
            'is_final' => 'boolean',
        ];
    }
}
