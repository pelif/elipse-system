<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ListCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'nullable|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'document' => 'nullable|string|max:32',
            'code_customer' => 'nullable|string|max:10',
            'active' => 'nullable',
            'updated_at' => 'nullable|date',
            'search' => 'nullable|string|max:255',
            'sortField' => 'nullable|string|in:id,company_name,legal_name,document,code_customer,active,created_at,updated_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
