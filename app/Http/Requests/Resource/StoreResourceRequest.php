<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $nullableKeys = ['manufacturer', 'model', 'serial_number', 'notes', 'documentation_link', 'responsible_id'];

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
            'resource_name' => 'required|string|max:150',
            'patrimonial_code' => 'required|string|max:120',
            'category_id' => [
                'required',
                'uuid',
                Rule::exists('resource_category', 'id'),
            ],
            'department_id' => [
                'required',
                'uuid',
                Rule::exists('department', 'id'),
            ],
            'manufacturer' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'responsible_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'notes' => 'nullable|string',
            'documentation_link' => ['nullable', 'string', 'max:500', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'resource_name.required' => 'O nome do recurso é obrigatório.',
            'resource_name.max' => 'O nome do recurso deve ter no máximo 150 caracteres.',
            'patrimonial_code.required' => 'O código patrimonial é obrigatório.',
            'patrimonial_code.max' => 'O código patrimonial deve ter no máximo 120 caracteres.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.uuid' => 'A categoria selecionada é inválida.',
            'category_id.exists' => 'A categoria selecionada não foi encontrada.',
            'department_id.required' => 'O departamento é obrigatório.',
            'department_id.uuid' => 'O departamento selecionado é inválido.',
            'department_id.exists' => 'O departamento selecionado não foi encontrado.',
            'manufacturer.max' => 'O fabricante deve ter no máximo 150 caracteres.',
            'model.max' => 'O modelo deve ter no máximo 255 caracteres.',
            'serial_number.max' => 'O número de série deve ter no máximo 255 caracteres.',
            'responsible_id.integer' => 'O responsável selecionado é inválido.',
            'responsible_id.exists' => 'O responsável selecionado não foi encontrado.',
            'documentation_link.url' => 'Informe um link de documentação válido (URL http ou https).',
            'documentation_link.max' => 'O link de documentação deve ter no máximo 500 caracteres.',
        ];
    }
}
