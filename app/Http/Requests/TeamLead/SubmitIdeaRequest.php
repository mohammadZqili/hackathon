<?php

namespace App\Http\Requests\TeamLead;

use Illuminate\Foundation\Http\FormRequest;

class SubmitIdeaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('team_leader') && 
               $this->user()->teams()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'final_pitch_deck' => 'required|file|mimes:pdf,pptx|max:15360', // 15MB max
            'demo_video' => 'nullable|file|mimes:mp4,avi,mov|max:102400', // 100MB max
            'source_code' => 'nullable|file|mimes:zip,tar,gz|max:51200', // 50MB max
            'submission_notes' => 'nullable|string|max:1000',
            'agree_to_terms' => 'required|accepted',
        ];
    }
}
