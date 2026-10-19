<?php

namespace App\Http\Requests\ThirdParty;

use Illuminate\Foundation\Http\FormRequest;

class ListThirdPartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:120',
            'email_alternative' => 'nullable|string|max:120',
            'indication' => 'nullable|string|max:120',
            'observation' => 'nullable|string',
            'company_name' => 'nullable|string|max:150',
            'document' => 'nullable|string|max:32',
            'active' => 'nullable',
            'search' => 'nullable|string|max:255',
            'sortField' => 'nullable|string|in:id,name,alias,email,email_alternative,company_name,document,indication,observation,active,created_at,updated_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
