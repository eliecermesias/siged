<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePeriodsRequest extends FormRequest
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
            'year' => ['required', 'integer', 'between:1900,9999'],
            'period' => [
                'required',
                'string',
                'size:5',
                Rule::in([
                    $this->integer('year').'A',
                    $this->integer('year').'B',
                ]),
                Rule::unique('periods', 'period')->ignore($this->route('periods')),
            ],
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
