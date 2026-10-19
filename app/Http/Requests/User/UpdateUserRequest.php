<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|string|max:255|in:Administrador,Comercial,Produção,Financeiro',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID é obrigatório.',
            'id.integer' => 'O ID deve ser um número inteiro.',
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um email válido.',
            'email.max' => 'O email deve ter no máximo 255 caracteres.',
            'role.required' => 'O perfil é obrigatório.',
            'role.string' => 'O perfil deve ser uma string.',
            'role.max' => 'O perfil deve ter no máximo 255 caracteres.',
            'role.in' => 'O perfil deve ser um dos seguintes: Administrador, Comercial, Produção, Financeiro.',
        ];
    }
}
