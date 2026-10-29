<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'characteristic_id' => ['required', 'integer', 'exists:characteristics,id'],
            'question_text' => ['required', 'string'],
            'response_type' => ['required', 'string', Rule::in(['likert', 'yes_no', 'open_text'])],
            'is_required' => ['required', 'boolean'],
            'position' => ['required', 'integer', 'between:1,65535'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
