<?php

namespace App\Http\Requests\ResourceCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteResourceCategoryRequest extends FormRequest
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
                Rule::exists('resource_category', 'id'),
            ],
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador da categoria é obrigatório.',
            'id.uuid' => 'O identificador da categoria deve ser um UUID válido.',
            'id.exists' => 'Categoria não encontrada.',
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}
