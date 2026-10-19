<?php

namespace App\Infrastructure\Eloquent\Contractor;

use App\Domain\Contractor\Entities\Contractor;
use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\Models\Contact as ContactModel;
use DateTimeInterface;
use Illuminate\Support\Str;

class EloquentContractorRepository implements ContractorRepositoryInterface
{
    private const TYPE_CONTRACTOR = 'Contractor';

    public function create(Contractor $contractor): Contractor
    {
        $rawId = $contractor->getId();
        $resolvedId = (string) Str::uuid();
        if ($rawId !== null && trim((string) $rawId) !== '') {
            $resolvedId = trim((string) $rawId);
        }

        $model = ContactModel::forceCreate(array_merge([
            'id' => $resolvedId,
            'type_contact' => self::TYPE_CONTRACTOR,
        ], $this->persistAttributesFromEntity($contractor)));

        return $this->toEntity($model->fresh(['customer']));
    }

    public function findById(string $id): ?Contractor
    {
        $trimmed = trim($id);
        if ($trimmed === '') {
            return null;
        }

        $model = ContactModel::query()
            ->where('type_contact', self::TYPE_CONTRACTOR)
            ->with('customer')
            ->find($trimmed);

        if (! $model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(Contractor $contractor): ?Contractor
    {
        $id = $contractor->getId();
        if ($id === null || trim((string) $id) === '') {
            return null;
        }

        $model = ContactModel::query()
            ->where('type_contact', self::TYPE_CONTRACTOR)
            ->with('customer')
            ->find(trim((string) $id));

        if (! $model) {
            return null;
        }

        $model->update($this->persistAttributesFromEntity($contractor));

        return $this->toEntity($model->fresh(['customer']));
    }

    public function deleteById(string $id): bool
    {
        $trimmed = trim($id);
        if ($trimmed === '') {
            return false;
        }

        $model = ContactModel::query()
            ->where('type_contact', self::TYPE_CONTRACTOR)
            ->find($trimmed);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllPaginated(array $filters = []): array
    {
        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = [
            'id',
            'name',
            'phone',
            'email',
            'communication_care',
            'created_at',
            'updated_at',
            'customer_name',
        ];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'name';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $name = $filters['name'] ?? null;
        $phone = $filters['phone'] ?? null;
        $email = $filters['email'] ?? null;
        $communicationCare = $filters['communicationCare'] ?? $filters['communication_care'] ?? null;
        $customerFilter = $filters['customer'] ?? $filters['customerName'] ?? $filters['company_name'] ?? null;
        $customerId = $filters['customer_id'] ?? $filters['customerId'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ContactModel::query()
            ->where('contacts.type_contact', self::TYPE_CONTRACTOR)
            ->leftJoin('customer', 'contacts.customer_id', '=', 'customer.id')
            ->select('contacts.*');

        if (is_string($customerId) && trim($customerId) !== '') {
            $query->where('contacts.customer_id', trim($customerId));
        }

        if (! empty($name)) {
            $query->where('contacts.name', 'like', '%'.$name.'%');
        }

        if (! empty($phone)) {
            $query->where('contacts.phone', 'like', '%'.$phone.'%');
        }

        if (! empty($email)) {
            $query->where('contacts.email', 'like', '%'.$email.'%');
        }

        if (! empty($communicationCare)) {
            $query->where('contacts.communication_care', 'like', '%'.$communicationCare.'%');
        }

        if (! empty($customerFilter)) {
            $needle = (string) $customerFilter;
            $query->where(function ($q) use ($needle) {
                $q->where('customer.company_name', 'like', '%'.$needle.'%')
                    ->orWhere('customer.legal_name', 'like', '%'.$needle.'%');
            });
        }

        if (! empty($search)) {
            $s = (string) $search;
            $searchDigits = preg_replace('/\D/', '', $s);
            if ($searchDigits === null) {
                $searchDigits = '';
            }

            $query->where(function ($q) use ($s, $searchDigits) {
                $q->where('contacts.name', 'like', '%'.$s.'%')
                    ->orWhere('contacts.phone', 'like', '%'.$s.'%')
                    ->orWhere('contacts.email', 'like', '%'.$s.'%')
                    ->orWhere('contacts.communication_care', 'like', '%'.$s.'%')
                    ->orWhere('customer.company_name', 'like', '%'.$s.'%')
                    ->orWhere('customer.legal_name', 'like', '%'.$s.'%');

                if ($searchDigits !== '') {
                    $q->orWhere('contacts.phone', 'like', '%'.$searchDigits.'%');
                }
            });
        }

        if ($sortField === 'customer_name') {
            $query->orderBy('customer.company_name', $sortDirection)
                ->orderBy('customer.legal_name', $sortDirection);
        } else {
            $query->orderBy('contacts.'.$sortField, $sortDirection);
        }

        $page = null;
        if (isset($filters['page'])) {
            $page = max(1, (int) $filters['page']);
        }

        $paginator = $query
            ->with('customer')
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (ContactModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function persistAttributesFromEntity(Contractor $contractor): array
    {
        return [
            'customer_id' => $contractor->getCustomerId(),
            'name' => $contractor->getName(),
            'phone' => $contractor->getPhone(),
            'email' => $contractor->getEmail(),
            'communication_care' => $contractor->getCommunicationCare(),
            'type_contact' => self::TYPE_CONTRACTOR,
            'created_by' => $contractor->getCreatedBy(),
            'updated_by' => $contractor->getUpdatedBy(),
        ];
    }

    private function toEntity(ContactModel $model): Contractor
    {
        return new Contractor(
            id: $model->id,
            customerId: $model->customer_id,
            name: (string) ($model->name ?? ''),
            phone: (string) ($model->phone ?? ''),
            email: (string) ($model->email ?? ''),
            communicationCare: $model->communication_care,
            typeContact: $model->type_contact,
            customerName: $this->customerDisplayNameFromModel($model),
            createdBy: $model->created_by !== null ? (int) $model->created_by : null,
            createdAt: $model->created_at?->format(DateTimeInterface::ATOM),
            updatedBy: $model->updated_by !== null ? (int) $model->updated_by : null,
            updatedAt: $model->updated_at?->format(DateTimeInterface::ATOM),
        );
    }

    private function customerDisplayNameFromModel(ContactModel $model): ?string
    {
        if (! $model->relationLoaded('customer')) {
            return null;
        }

        $customer = $model->customer;
        if ($customer === null) {
            return null;
        }

        $companyName = $customer->company_name;
        if ($companyName !== null && trim((string) $companyName) !== '') {
            return trim((string) $companyName);
        }

        $legalName = $customer->legal_name;
        if ($legalName !== null && trim((string) $legalName) !== '') {
            return trim((string) $legalName);
        }

        return null;
    }
}
