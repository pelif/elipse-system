<?php

namespace App\Http\Requests\ThirdParty;

use App\Rules\ValidCnpj;
use Illuminate\Foundation\Http\FormRequest;

class StoreThirdPartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $nullableKeys = ['alias', 'phone_alternative', 'email_alternative', 'indication', 'observation', 'company_name'];

        $merged = [];
        foreach ($nullableKeys as $key) {
            if ($this->has($key) && trim((string) $this->input($key)) === '') {
                $merged[$key] = null;
            }
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_alternative' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:120',
            'email_alternative' => 'nullable|string|email|max:120',
            'indication' => 'nullable|string|max:120',
            'observation' => 'nullable|string',
            'company_name' => 'nullable|string|max:150',
            'document' => ['required', 'string', new ValidCnpj],
            'active' => 'required|boolean',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'uuid|exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'phone_alternative.max' => 'O telefone alternativo deve ter no máximo 20 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 120 caracteres.',
            'email_alternative.email' => 'Informe um e-mail alternativo válido.',
            'email_alternative.max' => 'O e-mail alternativo deve ter no máximo 120 caracteres.',
            'indication.max' => 'A indicação deve ter no máximo 120 caracteres.',
            'company_name.max' => 'A razão social deve ter no máximo 150 caracteres.',
            'document.required' => 'O CNPJ é obrigatório.',
            'active.required' => 'O campo ativo é obrigatório.',
            'service_ids.array' => 'As especialidades devem ser uma lista.',
            'service_ids.*.uuid' => 'Cada especialidade deve ter um identificador válido.',
            'service_ids.*.exists' => 'Uma ou mais especialidades selecionadas não existem.',
        ];
    }
}
