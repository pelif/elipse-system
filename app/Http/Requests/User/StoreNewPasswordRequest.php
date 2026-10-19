<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StoreNewPasswordRequest extends FormRequest
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
            'id' => 'required|integer|exists:users,id',
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    $user = User::find($this->route('id'));
                    if (!$user || !Hash::check($value, $user->password)) {
                        $fail('A senha atual está incorreta.');
                    }
                },
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Nenhum usuário foi encontrado para criar a senha.',
            'id.exists' => 'Nenhum usuário foi encontrado para criar a senha.',
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A nova senha e a confirmação da nova senha não coincidem.',
            'password.mixedCase' => 'A nova senha deve conter pelo menos uma letra maiúscula e uma letra minúscula.',
            'password.numbers' => 'A nova senha deve conter pelo menos um número.',
            'password.symbols' => 'A nova senha deve conter pelo menos um caractere especial.',
        ];
    }
}
