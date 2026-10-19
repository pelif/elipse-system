<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteProductRequest extends FormRequest
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
            'id' => ['required','uuid',Rule::exists('products', 'id')],
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do produto é obrigatório.',
            'id.uuid' => 'O identificador do produto deve ser um UUID válido.',
            'id.exists' => 'Produto não encontrado.',
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}
