<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merged = [];

        if (! $this->has('os_quantity') || $this->input('os_quantity') === '' || $this->input('os_quantity') === null) {
            $merged['os_quantity'] = 0;
        }

        foreach (['job_title', 'job_code'] as $key) {
            if ($this->has($key) && is_string($this->input($key))) {
                $merged[$key] = trim($this->input($key));
            }
        }

        $nullableKeys = [
            'briefing_url',
            'observations',
            'target_budget',
            'purchase_order',
            'purchase_order_link',
            'invoice_due_date',
            'additional_info',
            'comissioned_agent_id',
            'commissioned_percentage',
            'commission_payment_due_date',
        ];

        foreach ($nullableKeys as $key) {
            if (! $this->has($key)) {
                continue;
            }

            $value = $this->input($key);
            if (! is_string($value)) {
                continue;
            }

            if (trim($value) === '') {
                $merged[$key] = null;
                continue;
            }

            if ($key === 'target_budget' || $key === 'commissioned_percentage') {
                $merged[$key] = $this->normalizeDecimal($value);
            }
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid', Rule::exists('customer', 'id')],
            'contractor_id' => [
                'required',
                'uuid',
                Rule::exists('contacts', 'id')->where(
                    fn ($query) => $query->where('type_contact', 'Contractor')
                ),
            ],
            'job_code' => ['required', 'string', 'max:20', $this->jobCodeRule()],
            'job_title' => ['required', 'string', 'max:255'],
            'product_id' => ['required', 'uuid', Rule::exists('products', 'id')],
            'os_quantity' => ['required', 'integer', 'min:0'],
            'briefing_url' => ['nullable', 'string', 'max:500', $this->briefingUrlRule()],
            'observations' => ['nullable', 'string', 'max:10000'],
            'target_budget' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'purchase_order' => ['nullable', 'string', 'max:255'],
            'purchase_order_link' => ['nullable', 'string', 'max:10000'],
            'invoice_due_date' => ['nullable', 'date_format:d/m/Y'],
            'additional_info' => ['nullable', 'string', 'max:10000'],
            'comissioned_agent_id' => ['nullable', 'uuid', Rule::exists('comissioned_agent', 'id')],
            'commissioned_percentage' => ['nullable', 'numeric', 'min:0', 'max:999.9999', 'decimal:0,4'],
            'commission_payment_due_date' => ['nullable', 'date_format:d/m/Y'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'O cliente é obrigatório.',
            'customer_id.uuid' => 'O cliente selecionado é inválido.',
            'customer_id.exists' => 'O cliente selecionado não foi encontrado.',
            'contractor_id.required' => 'O contratante é obrigatório.',
            'contractor_id.uuid' => 'O contratante selecionado é inválido.',
            'contractor_id.exists' => 'O contratante selecionado não foi encontrado.',
            'job_code.required' => 'O código do job é obrigatório.',
            'job_code.max' => 'O código do job deve ter no máximo 20 caracteres.',
            'job_title.required' => 'O título do job é obrigatório.',
            'job_title.max' => 'O título do job deve ter no máximo 255 caracteres.',
            'product_id.required' => 'O produto é obrigatório.',
            'product_id.uuid' => 'O produto selecionado é inválido.',
            'product_id.exists' => 'O produto selecionado não foi encontrado.',
            'os_quantity.integer' => 'A quantidade de OS deve ser um número inteiro.',
            'os_quantity.min' => 'A quantidade de OS não pode ser negativa.',
            'briefing_url.max' => 'A URL de briefing deve ter no máximo 500 caracteres.',
            'observations.max' => 'As observações devem ter no máximo 10000 caracteres.',
            'target_budget.numeric' => 'O orçamento alvo deve ser um valor decimal.',
            'target_budget.min' => 'O orçamento alvo não pode ser negativo.',
            'target_budget.decimal' => 'O orçamento alvo deve ter no máximo 2 casas decimais.',
            'purchase_order.max' => 'O pedido de compra deve ter no máximo 255 caracteres.',
            'purchase_order_link.max' => 'O link do pedido de compra deve ter no máximo 10000 caracteres.',
            'invoice_due_date.date_format' => 'A data de faturamento deve estar no formato dd/mm/aaaa.',
            'additional_info.max' => 'As informações complementares devem ter no máximo 10000 caracteres.',
            'comissioned_agent_id.uuid' => 'O comissionado selecionado é inválido.',
            'comissioned_agent_id.exists' => 'O comissionado selecionado não foi encontrado.',
            'commissioned_percentage.numeric' => 'O percentual de comissão deve ser um valor decimal.',
            'commissioned_percentage.min' => 'O percentual de comissão não pode ser negativo.',
            'commissioned_percentage.max' => 'O percentual de comissão deve ficar entre 0 e 999.9999.',
            'commissioned_percentage.decimal' => 'O percentual de comissão deve ter no máximo 4 casas decimais.',
            'commission_payment_due_date.date_format' => 'A previsão de pagamento deve estar no formato dd/mm/aaaa.',
        ];
    }

    private function normalizeDecimal(string $value): string
    {
        $trimmed = trim($value);
        if (str_contains($trimmed, ',')) {
            $trimmed = str_replace('.', '', $trimmed);
            $trimmed = str_replace(',', '.', $trimmed);
        }

        return $trimmed;
    }

    private function jobCodeRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! is_string($value)) {
                $fail('O código do job é inválido.');

                return;
            }

            $position = strrpos($value, '_');
            if ($position === false) {
                $fail('O código do job deve conter o código do cliente, underscore e 4 dígitos.');

                return;
            }

            $suffix = substr($value, $position + 1);
            if (strlen($suffix) !== 4 || ! ctype_digit($suffix)) {
                $fail('O código do job deve terminar com 4 dígitos.');
            }
        };
    }

    private function briefingUrlRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! is_string($value) || trim($value) === '') {
                return;
            }

            $url = trim($value);
            $hasScheme = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
            if (! $hasScheme || ! str_contains($url, '.')) {
                $fail('A URL de briefing deve começar com http:// ou https:// e conter um ponto.');
            }
        };
    }
}
