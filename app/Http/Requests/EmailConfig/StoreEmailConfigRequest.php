<?php

namespace App\Http\Requests\EmailConfig;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/',
            ],
            'department_id' => [
                'required',
                'uuid',
                'exists:department,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'address.required' => 'O campo E-mail é obrigatório.',
            'address.regex' => 'O campo E-mail deve ser um e-mail válido.',
            'department_id.required' => 'O campo Departamento é obrigatório.',
            'department_id.uuid' => 'O campo Departamento deve ser um UUID válido.',
            'department_id.exists' => 'O departamento selecionado é inválido.',
        ];
    }
}

