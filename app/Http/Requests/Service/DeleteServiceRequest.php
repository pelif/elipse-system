<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['id' => $this->route('id')]);
    }

    public function rules(): array
    {
        return [
            'id'      => ['required', 'uuid', Rule::exists('services', 'id')],
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'      => 'O identificador do serviço é obrigatório.',
            'id.uuid'          => 'O identificador do serviço deve ser um UUID válido.',
            'id.exists'        => 'Serviço não encontrado.',
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}
