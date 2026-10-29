<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstrumentVersionRequest extends FormRequest
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
            'evaluation_instrument_id' => ['required', 'integer', 'exists:evaluation_instruments,id'],
            'version_number' => [
                'required',
                'integer',
                'between:1,65535',
                Rule::unique('instrument_versions', 'version_number')
                    ->where(fn ($query) => $query->where('evaluation_instrument_id', $this->input('evaluation_instrument_id'))),
            ],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
