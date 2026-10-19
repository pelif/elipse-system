<?php

namespace App\Http\Requests\ServiceUnity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteRequest extends FormRequest
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
            'confirm' => 'required|accepted'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador da unidade de serviço é obrigatório.',
            'id.uuid' => 'O identificador da unidade de serviço deve ser um UUID válido.',
            'id.exists' => 'Unidade de serviço não encontrada.',
            'confirm.required' => 'Você precisa confirmar a exclusão.',
            'confirm.accepted' => 'Você precisa confirmar a exclusão.',
        ];
    }
}
