<?php

namespace App\Http\Requests\HackathonAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkshopRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'speaker_id' => 'nullable|exists:speakers,id',
            'location' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_participants' => 'required|integer|min:5|max:500',
            'requirements' => 'nullable|string|max:1000',
            'materials_link' => 'nullable|url|max:255',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|required_if:is_online,true|url|max:255',
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
            'title.required' => 'Workshop title is required.',
            'end_time.after' => 'Workshop end time must be after the start time.',
            'max_participants.min' => 'Workshop must allow at least 5 participants.',
            'meeting_link.required_if' => 'Meeting link is required for online workshops.',
        ];
    }
}