<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the customer financial-info modal.
 */
class UpdateCustomerFinancialRequest extends FormRequest
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
                Rule::exists('customer', 'id'),
            ],
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'email_nf' => 'required|email|max:255',
            'deadline_payment' => 'required|integer|min:1',
            'antecipation' => 'nullable|boolean',
            'antecipation_instructions' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do cliente é obrigatório.',
            'id.uuid' => 'O identificador do cliente deve ser um UUID válido.',
            'id.exists' => 'Cliente não encontrado.',
            'name.required' => 'O contato financeiro é obrigatório.',
            'phone.required' => 'O telefone de contato é obrigatório.',
            'email.required' => 'O e-mail de contato é obrigatório.',
            'email.email' => 'Informe um e-mail de contato válido.',
            'email_nf.required' => 'O e-mail da NF é obrigatório.',
            'email_nf.email' => 'Informe um e-mail da NF válido.',
            'deadline_payment.required' => 'O prazo de pagamento é obrigatório.',
            'deadline_payment.min' => 'O prazo de pagamento deve ser maior que zero.',
            'antecipation_instructions.string' => 'As instruções precisam ser caracteres.',
            'antecipation_instructions.max' => 'O limite de caracteres para Instruções é de 500 caracteres e o mesmo foi excedido'
        ];
    }
}
