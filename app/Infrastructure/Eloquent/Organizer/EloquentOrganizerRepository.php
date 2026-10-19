<?php

namespace App\Infrastructure\Eloquent\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\Models\Contact as ContactModel;
use DateTimeInterface;
use Illuminate\Support\Str;

class EloquentOrganizerRepository implements OrganizerRepositoryInterface
{
    private const TYPE_ORGANIZER = 'Organizer';

    public function create(Organizer $organizer): Organizer
    {
        $rawId = $organizer->getId();
        $resolvedId = (string) Str::uuid();
        if ($rawId !== null && trim((string) $rawId) !== '') {
            $resolvedId = trim((string) $rawId);
        }

        $model = ContactModel::forceCreate(array_merge([
            'id' => $resolvedId,
            'type_contact' => self::TYPE_ORGANIZER,
        ], $this->persistAttributesFromEntity($organizer)));

        return $this->toEntity($model->fresh(['customer']));
    }

    public function findById(string $id): ?Organizer
    {
        $trimmed = trim($id);
        if ($trimmed === '') {
            return null;
        }

        $model = ContactModel::query()
            ->where('type_contact', self::TYPE_ORGANIZER)
            ->with('customer')
            ->find($trimmed);

        if (! $model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(Organizer $organizer): ?Organizer
    {
        $id = $organizer->getId();
        if ($id === null || trim((string) $id) === '') {
            return null;
        }

        $model = ContactModel::query()
            ->where('type_contact', self::TYPE_ORGANIZER)
            ->with('customer')
            ->find(trim((string) $id));

        if (! $model) {
            return null;
        }

        $model->update($this->persistAttributesFromEntity($organizer));

        return $this->toEntity($model->fresh(['customer']));
    }

    public function deleteById(string $id): bool
    {
        $trimmed = trim($id);
        if ($trimmed === '') {
            return false;
        }

        $model = ContactModel::query()
            ->where('type_contact', self::TYPE_ORGANIZER)
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
            'observations',
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
        $observations = $filters['observations'] ?? null;
        $customerFilter = $filters['customer'] ?? $filters['customerName'] ?? $filters['company_name'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ContactModel::query()
            ->where('contacts.type_contact', self::TYPE_ORGANIZER)
            ->leftJoin('customer', 'contacts.customer_id', '=', 'customer.id')
            ->select('contacts.*');

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

        if (! empty($observations)) {
            $query->where('contacts.observations', 'like', '%'.$observations.'%');
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
                    ->orWhere('contacts.observations', 'like', '%'.$s.'%')
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
    private function persistAttributesFromEntity(Organizer $organizer): array
    {
        return [
            'customer_id' => $organizer->getCustomerId(),
            'name' => $organizer->getName(),
            'phone' => $organizer->getPhone(),
            'email' => $organizer->getEmail(),
            'communication_care' => $organizer->getCommunicationCare(),
            'observations' => $organizer->getObservations(),
            'type_contact' => self::TYPE_ORGANIZER,
            'created_by' => $organizer->getCreatedBy(),
            'updated_by' => $organizer->getUpdatedBy(),
        ];
    }

    private function toEntity(ContactModel $model): Organizer
    {
        return new Organizer(
            id: $model->id,
            customerId: $model->customer_id,
            name: (string) ($model->name ?? ''),
            phone: (string) ($model->phone ?? ''),
            email: (string) ($model->email ?? ''),
            communicationCare: $model->communication_care,
            observations: $model->observations,
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
