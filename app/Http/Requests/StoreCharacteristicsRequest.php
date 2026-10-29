<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCharacteristicsRequest extends FormRequest
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
            'instrument_version_id' => ['required', 'integer', 'exists:instrument_versions,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('characteristics', 'name')
                    ->where(fn ($query) => $query->where('instrument_version_id', $this->input('instrument_version_id'))),
            ],
            'description' => ['nullable', 'string'],
            'position' => ['required', 'integer', 'between:1,65535'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
