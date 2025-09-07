<?php

namespace App\Http\Requests\TrackSupervisor;

use Illuminate\Foundation\Http\FormRequest;

class ExportTrackDataRequest extends FormRequest
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
            'track_id' => 'required|exists:tracks,id',
            'format' => 'required|in:csv,xlsx,pdf',
            'include_teams' => 'boolean',
            'include_ideas' => 'boolean',
            'include_scores' => 'boolean',
            'include_feedback' => 'boolean',
            'date_range' => 'nullable|array',
            'date_range.from' => 'nullable|date',
            'date_range.to' => 'nullable|date|after_or_equal:date_range.from',
        ];
    }
}
