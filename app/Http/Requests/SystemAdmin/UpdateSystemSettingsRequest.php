<?php

namespace App\Http\Requests\SystemAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
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
            'app_name' => 'sometimes|required|string|max:255',
            'app_description' => 'nullable|string|max:1000',
            'timezone' => 'sometimes|required|string|max:50',
            'date_format' => 'sometimes|required|string|max:20',
            'time_format' => 'sometimes|required|string|max:20',
            'registration_enabled' => 'boolean',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'max_file_size' => 'sometimes|required|integer|min:1|max:100',
            'allowed_file_types' => 'sometimes|required|array',
            'allowed_file_types.*' => 'string|max:10',
        ];
    }
}
