<?php

namespace App\Http\Requests\Contractor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteContractorRequest extends FormRequest
{
    private const TYPE_CONTRACTOR = 'Contractor';

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
                Rule::exists('contacts', 'id')->where('type_contact', self::TYPE_CONTRACTOR),
            ],
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do contratante é obrigatório.',
            'id.uuid' => 'O identificador do contratante deve ser um UUID válido.',
            'id.exists' => 'Contratante não encontrado.',
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}
