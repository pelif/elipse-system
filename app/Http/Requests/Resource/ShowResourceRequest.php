<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'uuid',
                Rule::exists('resources', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do recurso é obrigatório.',
            'id.uuid' => 'O identificador do recurso deve ser um UUID válido.',
            'id.exists' => 'Recurso não encontrado.',
        ];
    }
}
