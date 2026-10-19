<?php

namespace App\Http\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;

class ListOrganizerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:120',
            'customer' => 'nullable|string|max:255',
            'customerName' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'communication_care' => 'nullable|string',
            'communicationCare' => 'nullable|string',
            'search' => 'nullable|string|max:255',
            'sortField' => 'nullable|string|in:id,name,phone,email,communication_care,created_at,updated_at,customer_name',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
