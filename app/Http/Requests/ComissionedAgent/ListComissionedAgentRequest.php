<?php

namespace App\Http\Requests\ComissionedAgent;

use Illuminate\Foundation\Http\FormRequest;

class ListComissionedAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'nullable|string|max:255',
            'defaultComission' => 'nullable|numeric',
            'paymentDays'      => 'nullable|integer|min:0',
            'sortField'        => 'nullable|string|in:id,name,defaultComission,paymentDays,createdAt',
            'sortDirection'    => 'nullable|string|in:asc,desc',
            'page'             => 'nullable|integer|min:1',
        ];
    }
}
