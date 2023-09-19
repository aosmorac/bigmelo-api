<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'organization_id'   => 'required|numeric|exists:organizations,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string|max:500',
            'system_prompt'     => 'nullable|string|max:500',
            'phone_number'      => 'required|regex:/^\+[0-9]+$/|min:10|max:20',
        ];
    }
}
