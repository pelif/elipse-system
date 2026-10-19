<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
                Rule::exists('products', 'id'),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do produto é obrigatório.',
            'id.uuid' => 'O identificador do produto deve ser um UUID válido.',
            'id.exists' => 'Produto não encontrado.',
            'name.required' => 'O campo produto é obrigatório.',
            'name.max' => 'O campo produto deve ter no máximo 255 caracteres.',
            'description.max' => 'O campo descrição deve ter no máximo 500 caracteres.',
        ];
    }
}
