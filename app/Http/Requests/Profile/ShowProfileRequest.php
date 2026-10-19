<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ShowProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (int) $this->user()->id === (int) $this->route('id');
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
            'id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID é obrigatório.',
            'id.integer' => 'O ID deve ser um número inteiro.',
        ];
    }
}
