<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCoordinatorsRequest extends FormRequest
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
            'person_id' => ['required', 'integer', 'exists:people,id', Rule::unique('coordinators', 'person_id')->ignore($this->route('coordinators'))],
            'code' => ['required', 'string', 'max:10', Rule::unique('coordinators', 'code')->ignore($this->route('coordinators'))],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
