<?php

namespace Tests\Support;

use App\Domain\Status\Enums\StatusType;
use App\Models\ComissionedAgent;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;

trait PreparesJobContext
{
    protected function createJobUser(string $name = 'Ana Comercial'): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'commercial',
        ]);
    }

    protected function createJobCustomer(string $code, string $name): Customer
    {
        return Customer::query()->create([
            'company_name' => $name,
            'legal_name' => $name.' LTDA',
            'document' => '11222333000181',
            'code_customer' => $code,
            'active' => true,
        ]);
    }

    protected function createJobProduct(string $name): Product
    {
        return Product::query()->create([
            'name' => $name,
        ]);
    }

    protected function createJobContractor(Customer $customer, string $name): Contact
    {
        $emailName = strtolower(str_replace(' ', '.', $name));

        return Contact::query()->create([
            'customer_id' => $customer->id,
            'name' => $name,
            'phone' => '11999999999',
            'email' => $emailName.'@tests.local',
            'type_contact' => 'Contractor',
        ]);
    }

    protected function createJobOrganizer(Customer $customer, string $name): Contact
    {
        $emailName = strtolower(str_replace(' ', '.', $name));

        return Contact::query()->create([
            'customer_id' => $customer->id,
            'name' => $name,
            'phone' => '11988887777',
            'email' => $emailName.'@tests.local',
            'type_contact' => 'Organizer',
        ]);
    }

    /**
     * @return array{job: Status, order: Status}
     */
    protected function createJobStatuses(): array
    {
        return [
            'job' => Status::query()->create([
                'status' => 'Em análise',
                'type' => StatusType::JOB->value,
                'observacoes' => null,
            ]),
            'order' => Status::query()->create([
                'status' => 'Não Agendado',
                'type' => StatusType::ORDER->value,
                'observacoes' => null,
            ]),
        ];
    }

    protected function createJobAgent(string $name): ComissionedAgent
    {
        return ComissionedAgent::query()->create([
            'name' => $name,
            'default_comission' => 10,
            'payment_days' => 30,
            'active' => true,
        ]);
    }
}
