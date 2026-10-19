<?php

namespace App\Http\Requests\ServiceUnity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['id' => $this->route('id')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required','uuid',Rule::exists('service_unity', 'id')],
            'unity' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O indentificador de Unidade de Serviço é obrigatório.',
            'id.uuid' => 'O identificador de Unidade de Serviço deve ser UUID válido.',
            'id.exists' => 'Unidade de Serviço não encontrada.',
            'unity.required' => 'O campo Unidade é obrigatório.',
            'unity.max' => 'O campo Unidade deve ter no máximo 255 caracteres.'
        ];
    }

}
