<?php

namespace App\Http\Requests\ResourceCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceCategoryRequest extends FormRequest
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

        $nullableKeys = ['description'];

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
                Rule::exists('resource_category', 'id'),
            ],
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador da categoria é obrigatório.',
            'id.uuid' => 'O identificador da categoria deve ser um UUID válido.',
            'id.exists' => 'Categoria não encontrada.',
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.max' => 'O nome da categoria deve ter no máximo 120 caracteres.',
            'description.max' => 'A descrição deve ter no máximo 255 caracteres.',
        ];
    }
}
