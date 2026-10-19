<?php

namespace App\Http\Requests\ComissionedAgent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteComissionedAgentRequest extends FormRequest
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
                Rule::exists('comissioned_agent', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do comissionado é obrigatório.',
            'id.uuid'     => 'O identificador do comissionado deve ser um UUID válido.',
            'id.exists'   => 'Comissionado não encontrado.',
        ];
    }
}
