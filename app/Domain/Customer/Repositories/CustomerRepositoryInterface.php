<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function create(Customer $customer): Customer;

    public function update(Customer $customer): ?Customer;

    public function deleteById(string $id): bool;

    public function findById(string $id): ?Customer;

    public function findByDocument(string $document): ?Customer;

    public function findByCodeCustomer(string $codeCustomer): ?Customer;

    public function findByCompanyName(string $companyName): ?Customer;

    public function findByLegalName(string $legalName): ?Customer;

    public function findByActive(bool $active): array;

    public function findAllForSelect(): array;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Customer>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array;
}
