<?php

namespace App\Http\Requests\Contractor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowContractorRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do contratante é obrigatório.',
            'id.uuid' => 'O identificador do contratante deve ser um UUID válido.',
            'id.exists' => 'Contratante não encontrado.',
        ];
    }
}
