<?php

namespace App\Http\Requests\ComissionedAgent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComissionedAgentRequest extends FormRequest
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
            'id'                => [
                'required',
                'uuid',
                Rule::exists('comissioned_agent', 'id'),
            ],
            'name'              => 'required|string|max:255',
            'default_comission' => 'required|numeric|min:0|max:100',
            'payment_days'      => 'required|integer|min:0',
            'observations'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'                => 'O identificador do comissionado é obrigatório.',
            'id.uuid'                    => 'O identificador do comissionado deve ser um UUID válido.',
            'id.exists'                  => 'Comissionado não encontrado.',
            'name.required'              => 'O campo Nome é obrigatório.',
            'name.string'                => 'O campo Nome deve ser um texto válido.',
            'name.max'                   => 'O campo Nome deve ter no máximo 255 caracteres.',
            'default_comission.required' => 'O campo Comissão Padrão é obrigatório.',
            'default_comission.numeric'  => 'O campo Comissão Padrão deve ser um número válido.',
            'default_comission.min'      => 'O campo Comissão Padrão deve ser no mínimo 0.',
            'default_comission.max'      => 'O campo Comissão Padrão deve ser no máximo 100.',
            'payment_days.required'      => 'O campo Dias de Pagamento é obrigatório.',
            'payment_days.integer'       => 'O campo Dias de Pagamento deve ser um número inteiro.',
            'payment_days.min'           => 'O campo Dias de Pagamento deve ser no mínimo 0.',
        ];
    }
}
