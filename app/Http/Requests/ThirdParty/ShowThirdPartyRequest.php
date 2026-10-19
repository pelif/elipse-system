<?php

namespace App\Http\Requests\ThirdParty;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowThirdPartyRequest extends FormRequest
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
                Rule::exists('third_parties', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do terceiro é obrigatório.',
            'id.uuid' => 'O identificador do terceiro deve ser um UUID válido.',
            'id.exists' => 'Terceiro não encontrado.',
        ];
    }
}
