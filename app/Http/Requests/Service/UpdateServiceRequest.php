<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
                Rule::exists('services', 'id'),
            ],
            'service'     => 'required|string|max:255',
            'unity_id'    => 'required|uuid|exists:service_unity,id',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'       => 'O identificador do serviço é obrigatório.',
            'id.uuid'           => 'O identificador do serviço deve ser um UUID válido.',
            'id.exists'         => 'Serviço não encontrado.',
            'service.required'  => 'O campo serviço é obrigatório.',
            'service.max'       => 'O campo serviço deve ter no máximo 255 caracteres.',
            'unity_id.required' => 'O campo unidade é obrigatório.',
            'unity_id.uuid'     => 'O identificador da unidade deve ser um UUID válido.',
            'unity_id.exists'   => 'Unidade de serviço não encontrada.',
            'description.max'   => 'O campo descrição deve ter no máximo 500 caracteres.',
        ];
    }
}
