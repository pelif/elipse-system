<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;

class DeleteJobStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirm' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}

