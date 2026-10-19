<?php

namespace App\Http\Requests\Customer;

use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('comissionet_agent_id') && ! $this->has('comissioned_agent_id')) {
            $this->merge(['comissioned_agent_id' => $this->input('comissionet_agent_id')]);
        }
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'document' => ['required', 'string', new ValidCnpj],
            'code_customer' => 'required|string|max:10',
            'active' => 'sometimes|boolean',
            'comissioned_agent_id' => [
                'nullable',
                'uuid',
                Rule::exists('comissioned_agent', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'O nome fantasia é obrigatório.',
            'legal_name.required' => 'A razão social é obrigatória.',
            'document.required' => 'O CNPJ é obrigatório.',
            'code_customer.required' => 'O código do cliente é obrigatório.',
            'code_customer.max' => 'O código do cliente deve ter no máximo 10 caracteres.',
            'comissioned_agent_id.uuid' => 'O comissionado selecionado é inválido.',
            'comissioned_agent_id.exists' => 'O comissionado selecionado não foi encontrado.',
        ];
    }
}
