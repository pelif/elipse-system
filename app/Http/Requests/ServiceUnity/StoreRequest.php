<?php

namespace App\Http\Requests\ServiceUnity;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'unity' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'unity.required' => 'O campo unity é obrigatório.',
            'unity.string' => 'O campo unity precisa ser alfanumérico',
            'unity.max' => 'O campo Unidade de Serviço deve ter no máximo 255 caracteres.',
        ];
    }
}
