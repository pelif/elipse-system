<?php

namespace App\Http\Requests\Logs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ShowSystemLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'file' => $this->route('file'),
        ]);
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'string',
                'regex:/^elipse_logs_\d{4}_\d{2}_[12]\.log$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'O arquivo de log é obrigatório.',
            'file.string' => 'O arquivo de log deve ser uma string.',
            'file.regex' => 'Formato de arquivo inválido.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $file = (string) $this->input('file', '');

            if ($file === '')
                return;


            $fullPath = storage_path('logs/' . $file);

            if (!is_file($fullPath) || !is_readable($fullPath)) {
                $validator
                    ->errors()
                    ->add('file', 'Arquivo não encontrado ou sem permissão de leitura.');
            }
        });
    }
}

