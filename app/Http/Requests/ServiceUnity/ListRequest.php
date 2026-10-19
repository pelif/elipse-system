<?php

namespace App\Http\Requests\ServiceUnity;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
            'unity'        => 'nullable|string',
            'created_at'   => 'nullable|string',
            'updated_at'   => 'nullable|string',
            'search'       => 'nullable|string',
            'sortField'    => 'nullable|string|in:id,unity,created_at,updated_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page'         => 'nullable|integer|min:1',
        ];
    }
}
