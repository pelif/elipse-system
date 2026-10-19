<?php

namespace App\Infrastructure\Eloquent\Customer;

use App\Domain\Customer\Entities\ContactEntity;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Entities\FinancialInfoEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\ValueObjects\Document;
use App\Models\ComissionedAgent;
use App\Domain\ComissionedAgent\Entities\ComissionedAgent as ComissionedAgentEntity;
use App\Domain\Customer\ValueObjects\CustomerRelationshipsCounts;
use App\Domain\User\Entities\User;
use App\Models\Contact as ContactModel;
use App\Models\Customer as CustomerModel;
use App\Models\FinancialInfo as FinancialInfoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function create(Customer $customer): Customer
    {
        return DB::transaction(function () use ($customer) {
            $id = $customer->getId() ?: (string) Str::uuid();

            $persisted = CustomerModel::forceCreate(array_merge([
                'id' => $id,
            ], $this->customerCoreAttributes($customer)));

            $this->syncComissionedAgent($persisted, $customer);
            $this->syncFinancialInfo($persisted, $customer);
            $this->syncContacts($persisted, $customer, true);

            return $this->toEntity($persisted->fresh(['financialInfo', 'contacts','comissionedAgent']));
        });
    }

    public function update(Customer $customer): ?Customer
    {
        $id = $customer->getId();
        if ($id === null || $id === '') {
            return null;
        }

        return DB::transaction(function () use ($customer, $id) {
            $model = CustomerModel::query()
                ->with(['financialInfo', 'contacts','comissionedAgent'])
                ->find($id);

            if (! $model) return null;

            $model->forceFill($this->customerCoreAttributes($customer))->save();

            $this->syncComissionedAgent($model->fresh(), $customer);
            $this->syncFinancialInfo($model->fresh(), $customer);
            $this->syncContacts($model->fresh(), $customer, false);

            return $this->toEntity($model->fresh(['financialInfo', 'contacts', 'comissionedAgent']));
        });
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') return false;

        return DB::transaction(function () use ($id) {
            $model = CustomerModel::query()->find($id);

            if (! $model) return false;

            ComissionedAgent::query()->where('customer_id', $id)->delete();
            ContactModel::query()->where('customer_id', $id)->delete();
            FinancialInfoModel::query()->where('customer_id', $id)->delete();

            return (bool) $model->delete();
        });
    }

    public function findById(string $id): ?Customer
    {
        $model = CustomerModel::query()
            ->with(['financialInfo', 'contacts','comissionedAgent'])
            ->find(trim($id));

        if (! $model) return null;

        return $this->toEntity($model);
    }

    public function findByDocument(string $document): ?Customer
    {
        $normalized = preg_replace('/\D/', '', $document) ?? '';

        $model = CustomerModel::query()
            ->with(['financialInfo', 'contacts','comissionedAgent'])
            ->where('document', $normalized)
            ->first();

        if (! $model) return null;

        return $this->toEntity($model);
    }

    public function findByCodeCustomer(string $codeCustomer): ?Customer
    {
        $model = CustomerModel::query()
            ->with(['financialInfo', 'contacts','comissionedAgent'])
            ->where('code_customer', $codeCustomer)
            ->first();

        if (!$model) return null;

        return $this->toEntity($model);
    }

    public function findByCompanyName(string $companyName): ?Customer
    {
        $model = CustomerModel::query()
            ->with(['financialInfo', 'contacts','comissionedAgent'])
            ->where('company_name', $companyName)
            ->first();

        if (!$model) return null;

        return $this->toEntity($model);
    }

    public function findByLegalName(string $legalName): ?Customer
    {
        $model = CustomerModel::query()
            ->with(['financialInfo', 'contacts','comissionedAgent'])
            ->where('legal_name', $legalName)
            ->first();

        if (!$model) return null;

        return $this->toEntity($model);
    }

    /**
     * @return array<int, Customer>
     */
    public function findByActive(bool $active): array
    {
        return CustomerModel::query()
            ->where('active', $active)
            ->with(['financialInfo', 'contacts','comissionedAgent'])
            ->orderBy('company_name')
            ->get()
            ->map(fn (CustomerModel $model) => $this->toEntity($model))
            ->all();
    }

    /**
     * @return array<int, array{id: string, company_name: string|null}>
     */
    public function findAllForSelect(): array
    {
        return CustomerModel::query()
            ->select(['id', 'company_name'])
            ->orderBy('company_name')
            ->get()
            ->map(fn (CustomerModel $r) => [
                'id' => $r->id,
                'company_name' => $r->company_name,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, Customer>, meta: array<string, mixed>}
     */
    public function findAllPaginated(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'updated_at';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        $allowedSortFields = [
            'id',
            'company_name',
            'legal_name',
            'document',
            'code_customer',
            'active',
            'created_at',
            'updated_at',
        ];

        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'updated_at';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'desc';
        }

        $companyName = $filters['companyName'] ?? $filters['company_name'] ?? null;
        $legalName = $filters['legalName'] ?? $filters['legal_name'] ?? null;
        $document = $filters['document'] ?? null;
        $codeCustomer = $filters['codeCustomer'] ?? $filters['code_customer'] ?? null;
        $active = $filters['active'] ?? null;
        $search = $filters['search'] ?? null;
        $updatedAt = $filters['updated_at'] ?? $filters['updatedAt'] ?? null;

        $query = CustomerModel::query()
            ->with([
                'financialInfo',
                'contacts',
                'comissionedAgent'])
            ->withCount([
                'jobs',
                'contractors',
                'organizers'
            ]);

        if (! empty($companyName)) {
            $query->where('company_name', 'like', '%'.$companyName.'%');
        }

        if (! empty($legalName)) {
            $query->where('legal_name', 'like', '%'.$legalName.'%');
        }

        if (! empty($document)) {
            $docDigits = preg_replace('/\D/', '', (string) $document) ?? '';
            $query->where(function ($q) use ($document, $docDigits) {
                $q->where('document', 'like', '%'.$document.'%');
                if ($docDigits !== '') {
                    $q->orWhere('document', 'like', '%'.$docDigits.'%');
                }
            });
        }

        if (! empty($codeCustomer)) {
            $query->where('code_customer', 'like', '%'.$codeCustomer.'%');
        }

        if ($active !== null && $active !== '') {
            $query->where('active', filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
        }

        if (! empty($updatedAt)) {
            try {
                $query->whereDate('updated_at', Carbon::parse((string) $updatedAt)->toDateString());
            } catch (\Throwable) {
                // ignore invalid date filter
            }
        }

        if (! empty($search)) {
            $s = (string) $search;
            $searchDigits = preg_replace('/\D/', '', $s) ?? '';

            $query->where(function ($q) use ($s, $searchDigits) {
                $q->where('company_name', 'like', '%'.$s.'%')
                    ->orWhere('legal_name', 'like', '%'.$s.'%')
                    ->orWhere('document', 'like', '%'.$s.'%');

                if ($searchDigits !== '') {
                    $q->orWhere('document', 'like', '%'.$searchDigits.'%');
                }

                $q->orWhere('code_customer', 'like', '%'.$s.'%');
            });
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : null;

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator
                ->getCollection()
                ->map(fn (CustomerModel $model) => $this->toEntity($model))->toArray(),
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
    private function customerCoreAttributes(Customer $customer): array
    {
        $payload = [
            'company_name' => $customer->getCompanyName(),
            'legal_name' => $customer->getLegalName(),
            'document' => $customer->getDocument()->getValue(),
            'code_customer' => $customer->getCodeCustomer(),
            'active' => $customer->isActive(),
            'created_by' => $customer->getCreatedBy(),
            'updated_by' => $customer->getUpdatedBy(),
        ];

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function financialPersistAttributes(string $customerId, FinancialInfoEntity $entity, Customer $customer): array
    {
        $auditCreatedBy = $entity->getCreatedBy() ?? $customer->getCreatedBy();
        $auditUpdatedBy = $entity->getUpdatedBy() ?? $customer->getUpdatedBy();

        return [
            'customer_id' => $customerId,
            'email_nf' => $entity->getEmailNf(),
            'financial_contact_name' => $entity->getFinancialContactName(),
            'financial_contact_phone' => $entity->getFinancialContactPhone(),
            'financial_contact_email' => $entity->getFinancialContactEmail(),
            'deadline_payment' => $entity->getDeadlinePayment(),
            'antecipation' => $entity->getAntecipation(),
            'antecipation_instructions' => $entity->getAntecipationInstructions(),
            'created_by' => $auditCreatedBy,
            'updated_by' => $auditUpdatedBy,
        ];
    }

    /**
     * @return FinancialInfoEntity|null  Null when nothing to persist
     */
    private function payloadFinancial(Customer $customer): ?FinancialInfoEntity
    {
        $dto = $customer->getFinancialInfo();
        if ($dto instanceof FinancialInfoEntity) {
            return $dto;
        }

        if (
            $customer->getDeadlinePayment() === null
            && $customer->getAntecipation() === null
            && ($customer->getAntecipationInstrutions() === null || $customer->getAntecipationInstrutions() === '')
        ) {
            return null;
        }

        return new FinancialInfoEntity(
            id: null,
            customerId: null,
            deadlinePayment: $customer->getDeadlinePayment(),
            antecipation: $customer->getAntecipation(),
            antecipationInstructions: $customer->getAntecipationInstrutions(),
            createdBy: $customer->getCreatedBy(),
            updatedBy: $customer->getUpdatedBy(),
            createdAt: null,
            updatedAt: null,
            emailNf: null,
            financialContactName: null,
            financialContactPhone: null,
            financialContactEmail: null,
        );
    }

    private function syncComissionedAgent(CustomerModel $model, Customer $customer): void
    {
        $agentId = $customer->getComissionedAgentId();
        $this->unlinkAgent($model);

        if ($agentId === null || $agentId === '') return;

        ComissionedAgent::query()
            ->whereKey($agentId)
            ->update(['customer_id' => $model->id]);
    }



    private function unlinkAgent(CustomerModel $customerModel): void
    {
        ComissionedAgent::query()
            ->where('customer_id', $customerModel->id)
            ->update(['customer_id' => null]);
    }

    private function syncFinancialInfo(CustomerModel $model, Customer $customer): void
    {
        $payload = $this->payloadFinancial($customer);

        /** @var FinancialInfoModel|null $existing */
        $existing = FinancialInfoModel::query()->where('customer_id', $model->id)->first();

        if ($payload === null) {
            return;
        }

        $attrs = $this->financialPersistAttributes($model->id, $payload, $customer);

        if ($existing) {
            $existing->update($attrs);
        } else {
            FinancialInfoModel::query()->create(array_merge(
                ['id' => $payload->getId() ?? (string) Str::uuid()],
                $attrs,
            ));
        }
    }

    private function syncContacts(CustomerModel $model, Customer $customer, bool $isCreate): void
    {
        $idsToKeep = [];

        foreach ($customer->getContacts() as $contact) {
            $rows = $this->contactPersistAttributes($model->id, $contact, $customer);

            $cid = $contact->getId();
            if (! $isCreate && $cid && ContactModel::query()->whereKey($cid)->where('customer_id', $model->id)->exists()) {
                ContactModel::query()->whereKey($cid)->update($rows);

                $idsToKeep[] = $cid;

                continue;
            }

            $newId = $cid ?: (string) Str::uuid();
            ContactModel::query()->create(array_merge(['id' => $newId, 'customer_id' => $model->id], $rows));
            $idsToKeep[] = $newId;
        }

        if ($idsToKeep === []) {
            ContactModel::query()->where('customer_id', $model->id)->delete();

            return;
        }

        ContactModel::query()
            ->where('customer_id', $model->id)
            ->whereNotIn('id', $idsToKeep)
            ->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function contactPersistAttributes(string $customerId, ContactEntity $contact, Customer $customer): array
    {
        return [
            'customer_id' => $customerId,
            'name' => $contact->getName(),
            'phone' => $contact->getPhone(),
            'email' => $contact->getEmail(),
            'type_contact' => $contact->getTypeContact(),
            'created_by' => $contact->getCreatedBy() ?? $customer->getCreatedBy(),
            'updated_by' => $contact->getUpdatedBy() ?? $customer->getUpdatedBy(),
        ];
    }

    private function toEntity(CustomerModel $model): Customer
    {
        $comissionedRow = $model->comissionedAgent;
        $comissionedEntity = null;
        if ($comissionedRow instanceof ComissionedAgent)
            $comissionedEntity = $this->comissionedRowtoEntity($comissionedRow);

        $financialRow = $model->financialInfo;
        $financialEntity = null;
        if ($financialRow instanceof FinancialInfoModel)
            $financialEntity = $this->financialRowToEntity($financialRow, $model->id);

        $mappedContacts = [];
        foreach ($model->contacts as $c) {
            if ($c instanceof ContactModel)
                $mappedContacts[] = $this->contactRowToEntity($c);
        }

        $relationshipCounts = new CustomerRelationshipsCounts(
            jobs: (int) $model->jobs_count,
            contractors: (int) $model->contractors_count,
            organizers: (int) $model->organizers_count
        );

        return new Customer(
            id: $model->id,
            companyName: (string) ($model->company_name ?? ''),
            legalName: (string) ($model->legal_name ?? ''),
            document: Document::fromPersisted($model->document),
            codeCustomer: (string) ($model->code_customer ?? ''),
            active: (bool) $model->active,
            deadlinePayment: $financialEntity?->getDeadlinePayment(),
            antecipation: $financialEntity?->getAntecipation(),
            antecipationInstrutions: $financialEntity?->getAntecipationInstructions(),
            createdBy: $model->created_by !== null ? (int) $model->created_by : null,
            updatedBy: $model->updated_by !== null ? (int) $model->updated_by : null,
            createdAt: $this->formatTs($model->created_at),
            updatedAt: $this->formatTs($model->updated_at),
            comissionedAgentId: $this->readComissionedAgentId($model),
            comissionedAgent: $comissionedEntity,
            financialInfo: $financialEntity,
            contacts: $mappedContacts,
            relationShipsCounts: $relationshipCounts
        );
    }

    private function comissionedRowtoEntity(?ComissionedAgent $row): ?ComissionedAgentEntity
    {
        if (!$row) return null;

        return new ComissionedAgentEntity(
            id: $row->id,
            name: (string) $row->name,
            defaultComission: (float) $row->default_comission,
            paymentDays: (int) $row->payment_days,
            observations: $row->observations,
            active: (bool) $row->active,
            createdBy: $row->created_by,
            updatedBy: $row->updated_by,
            createdAt: $this->formatTs($row->created_at),
            updatedAt: $this->formatTs($row->updated_at),
        );
    }

    private function financialRowToEntity(?FinancialInfoModel $row, string $customerId): ?FinancialInfoEntity
    {
        if (! $row) return null;

        return new FinancialInfoEntity(
            id: $row->id,
            customerId: $customerId,
            deadlinePayment: $row->deadline_payment !== null ? (int) $row->deadline_payment : null,
            antecipation: $row->antecipation !== null ? (int) $row->antecipation : null,
            antecipationInstructions: $row->antecipation_instructions,
            createdBy: $row->created_by !== null ? (int) $row->created_by : null,
            updatedBy: $row->updated_by !== null ? (int) $row->updated_by : null,
            createdAt: $this->formatTs($row->created_at),
            updatedAt: $this->formatTs($row->updated_at),
            emailNf: $row->email_nf,
            financialContactName: $row->financial_contact_name,
            financialContactPhone: $row->financial_contact_phone,
            financialContactEmail: $row->financial_contact_email,
        );
    }

    private function contactRowToEntity(ContactModel $c): ContactEntity
    {
        return new ContactEntity(
            id: $c->id,
            customerId: $c->customer_id,
            name: $c->name,
            phone: $c->phone,
            email: $c->email,
            typeContact: $c->type_contact,
            createdBy: $c->created_by !== null ? (int) $c->created_by : null,
            updatedBy: $c->updated_by !== null ? (int) $c->updated_by : null,
            createdAt: $this->formatTs($c->created_at),
            updatedAt: $this->formatTs($c->updated_at),
        );
    }

    private function formatTs(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toIso8601String();
        }

        return (string) $value;
    }

    private function isComissionedAgentLoaded(CustomerModel $model): bool
    {
        return $model->relationLoaded('comissionedAgent') && $model->comissionedAgent;
    }

    private function getComissionedAgentId(?string $customerId): ?string
    {
        $comissionedAgentId = ComissionedAgent::query()
            ->where('customer_id', $customerId)
            ->value('id');

        return match(true) {
            $comissionedAgentId !== null => (string) $comissionedAgentId,
            default => null
        };
    }

    private function readComissionedAgentId(CustomerModel $model): ?string
    {
        return match(true) {
            true === $this->isComissionedAgentLoaded($model) => $model->comissionedAgent->id,
            default => $this->getComissionedAgentId($model->id)
        };
    }
}
