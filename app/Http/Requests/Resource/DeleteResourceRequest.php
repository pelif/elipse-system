<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteResourceRequest extends FormRequest
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
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do recurso é obrigatório.',
            'id.uuid' => 'O identificador do recurso deve ser um UUID válido.',
            'id.exists' => 'Recurso não encontrado.',
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}
