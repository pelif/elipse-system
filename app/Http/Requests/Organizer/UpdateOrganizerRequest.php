<?php

namespace App\Http\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizerRequest extends FormRequest
{
    private const TYPE_ORGANIZER = 'Organizer';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);

        $nullableKeys = ['communication_care'];

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
            'id' => [
                'required',
                'uuid',
                Rule::exists('contacts', 'id')->where('type_contact', self::TYPE_ORGANIZER),
            ],
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:120',
            'customer_id' => 'required|uuid|exists:customer,id',
            'communication_care' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do organizador é obrigatório.',
            'id.uuid' => 'O identificador do organizador deve ser um UUID válido.',
            'id.exists' => 'Organizador não encontrado.',
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 120 caracteres.',
            'customer_id.required' => 'O cliente é obrigatório.',
            'customer_id.uuid' => 'O identificador do cliente deve ser um UUID válido.',
            'customer_id.exists' => 'Cliente não encontrado.',
        ];
    }
}
