<?php

namespace App\Http\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowOrganizerRequest extends FormRequest
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
    }

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'uuid',
                Rule::exists('contacts', 'id')->where('type_contact', self::TYPE_ORGANIZER),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do organizador é obrigatório.',
            'id.uuid' => 'O identificador do organizador deve ser um UUID válido.',
            'id.exists' => 'Organizador não encontrado.',
        ];
    }
}
