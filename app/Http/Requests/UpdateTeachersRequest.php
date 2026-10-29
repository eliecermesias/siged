<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeachersRequest extends FormRequest
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
            'person_id' => ['required', 'integer', 'exists:people,id', Rule::unique('teachers', 'person_id')->ignore($this->route('teachers'))],
            'code' => ['required', 'string', 'max:10', Rule::unique('teachers', 'code')->ignore($this->route('teachers'))],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
