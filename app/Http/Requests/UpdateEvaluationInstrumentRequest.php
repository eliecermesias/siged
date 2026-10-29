<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEvaluationInstrumentRequest extends FormRequest
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
            'intention_id' => ['required', 'integer', 'exists:intentions,id'],
            'evaluation_modality_id' => ['required', 'integer', 'exists:evaluation_modalities,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('evaluation_instruments', 'name')->ignore($this->route('evaluationInstrument'))],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
