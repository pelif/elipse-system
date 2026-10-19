<?php

namespace App\Infrastructure\Eloquent\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\Domain\ThirdParty\ValueObjects\ThirdPartyJobHistory;
use App\Models\ServiceThirdParty as ServiceThirdPartyModel;
use App\Models\ThirdParty as ThirdPartyModel;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentThirdPartyRepository implements ThirdPartyRepositoryInterface
{
    public function create(ThirdParty $thirdParty): ThirdParty
    {
        return DB::transaction(function () use ($thirdParty) {
            $id = $thirdParty->getId();
            if ($id === null || trim((string) $id) === '') {
                $id = (string) Str::uuid();
            } else {
                $id = trim((string) $id);
            }

            $model = ThirdPartyModel::forceCreate(array_merge([
                'id' => $id,
            ], $this->persistAttributesFromEntity($thirdParty)));

            $this->syncServices($model, $thirdParty);

            return $this->toEntity($this->freshWithRelations($model));
        });
    }

    public function findById(string $id): ?ThirdParty
    {
        $model = ThirdPartyModel::query()
            ->with(['services' => function ($query) {
                $query->orderBy('services.service', 'asc');
            }])
            ->find(trim($id));

        if (! $model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function update(ThirdParty $thirdParty): ?ThirdParty
    {
        $id = $thirdParty->getId();
        if ($id === null || trim((string) $id) === '') {
            return null;
        }

        return DB::transaction(function () use ($thirdParty, $id) {
            $model = ThirdPartyModel::query()->find(trim((string) $id));

            if (! $model) {
                return null;
            }

            $model->update($this->persistAttributesFromEntity($thirdParty));
            $this->syncServices($model, $thirdParty);

            return $this->toEntity($this->freshWithRelations($model));
        });
    }

    public function deleteById(string $id): bool
    {
        $id = trim($id);
        if ($id === '') {
            return false;
        }

        $model = ThirdPartyModel::query()->find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findAllPaginated(array $filters = []): array
    {
        $filters = $filters ?? [];

        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        $allowedSortFields = [
            'id',
            'name',
            'alias',
            'email',
            'email_alternative',
            'company_name',
            'document',
            'indication',
            'observation',
            'active',
            'created_at',
            'updated_at',
        ];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'name';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'asc';
        }

        $name = $filters['name'] ?? null;
        $alias = $filters['alias'] ?? null;
        $email = $filters['email'] ?? null;
        $emailAlternative = $filters['emailAlternative'] ?? $filters['email_alternative'] ?? null;
        $indication = $filters['indication'] ?? null;
        $observation = $filters['observation'] ?? null;
        $companyName = $filters['companyName'] ?? $filters['company_name'] ?? null;
        $document = $filters['document'] ?? null;
        $active = $filters['active'] ?? null;
        $search = $filters['search'] ?? null;

        $query = ThirdPartyModel::query()
            ->select('third_parties.*')
            ->with(['services' => function ($relation) {
                $relation->orderBy('services.service', 'asc');
            }])
            ->selectSub($this->jobsCountSubquery(), 'jobs_count')
            ->selectSub($this->lastJobCodeSubquery(), 'last_job_code');

        if (! empty($name)) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        if (! empty($alias)) {
            $query->where('alias', 'like', '%'.$alias.'%');
        }

        if (! empty($email)) {
            $query->where('email', 'like', '%'.$email.'%');
        }

        if (! empty($emailAlternative)) {
            $query->where('email_alternative', 'like', '%'.$emailAlternative.'%');
        }

        if (! empty($indication)) {
            $query->where('indication', 'like', '%'.$indication.'%');
        }

        if (! empty($observation)) {
            $query->where('observation', 'like', '%'.$observation.'%');
        }

        if (! empty($companyName)) {
            $query->where('company_name', 'like', '%'.$companyName.'%');
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

        if ($active !== null && $active !== '') {
            $query->where('active', filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
        }

        if (! empty($search)) {
            $s = (string) $search;
            $searchDigits = preg_replace('/\D/', '', $s) ?? '';

            $query->where(function ($q) use ($s, $searchDigits) {
                $q->where('name', 'like', '%'.$s.'%')
                    ->orWhere('alias', 'like', '%'.$s.'%')
                    ->orWhere('phone', 'like', '%'.$s.'%')
                    ->orWhere('phone_alternative', 'like', '%'.$s.'%')
                    ->orWhere('email', 'like', '%'.$s.'%')
                    ->orWhere('email_alternative', 'like', '%'.$s.'%')
                    ->orWhere('indication', 'like', '%'.$s.'%')
                    ->orWhere('observation', 'like', '%'.$s.'%')
                    ->orWhere('company_name', 'like', '%'.$s.'%')
                    ->orWhere('document', 'like', '%'.$s.'%');

                if ($searchDigits !== '') {
                    $q->orWhere('document', 'like', '%'.$searchDigits.'%')
                        ->orWhere('phone', 'like', '%'.$searchDigits.'%')
                        ->orWhere('phone_alternative', 'like', '%'.$searchDigits.'%');
                }

                $activeSearch = strtolower(trim($s));
                if ($activeSearch !== '') {
                    if (in_array($activeSearch, ['1', 'true', 'sim', 'yes', 'ativo', 'ativa'], true)) {
                        $q->orWhere('active', true);
                    }
                    if (in_array($activeSearch, ['0', 'false', 'não', 'nao', 'no', 'inativo', 'inativa'], true)) {
                        $q->orWhere('active', false);
                    }
                }
            });
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : null;

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (ThirdPartyModel $model) => $this->toEntity($model))->toArray(),
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
    private function persistAttributesFromEntity(ThirdParty $thirdParty): array
    {
        return [
            'name' => $thirdParty->getName(),
            'alias' => $thirdParty->getAlias(),
            'phone' => $thirdParty->getPhone(),
            'phone_alternative' => $thirdParty->getPhoneAlternative(),
            'email' => $thirdParty->getEmail(),
            'email_alternative' => $thirdParty->getEmailAlternative(),
            'indication' => $thirdParty->getIndication(),
            'observation' => $thirdParty->getObservation(),
            'company_name' => $thirdParty->getCompanyName(),
            'document' => $thirdParty->getDocument()->getValue(),
            'active' => $thirdParty->isActive(),
            'created_by' => $thirdParty->getCreatedBy(),
            'updated_by' => $thirdParty->getUpdatedBy(),
        ];
    }

    private function toEntity(ThirdPartyModel $model): ThirdParty
    {
        $servicesPayload = $this->mapServices($model);
        $jobHistory = $this->resolveJobHistory($model);

        $createdAt = null;
        if ($model->created_at !== null) {
            $createdAt = $model->created_at->format(DateTimeInterface::ATOM);
        }

        $updatedAt = null;
        if ($model->updated_at !== null) {
            $updatedAt = $model->updated_at->format(DateTimeInterface::ATOM);
        }

        $createdBy = null;
        if ($model->created_by !== null) {
            $createdBy = (int) $model->created_by;
        }

        $updatedBy = null;
        if ($model->updated_by !== null) {
            $updatedBy = (int) $model->updated_by;
        }

        return new ThirdParty(
            id: $model->id,
            name: (string) ($model->name ?? ''),
            alias: $model->alias,
            phone: (string) ($model->phone ?? ''),
            phoneAlternative: $model->phone_alternative,
            email: (string) ($model->email ?? ''),
            emailAlternative: $model->email_alternative,
            indication: $model->indication,
            observation: $model->observation,
            companyName: $model->company_name,
            document: Document::fromPersisted($model->document),
            active: (bool) $model->active,
            createdBy: $createdBy,
            createdAt: $createdAt,
            updatedBy: $updatedBy,
            updatedAt: $updatedAt,
            serviceIds: $servicesPayload['ids'],
            services: $servicesPayload['items'],
            jobHistory: $jobHistory,
        );
    }

    private function freshWithRelations(ThirdPartyModel $model): ThirdPartyModel
    {
        $fresh = $model->fresh();
        $fresh->load(['services' => function ($query) {
            $query->orderBy('services.service', 'asc');
        }]);

        return $fresh;
    }

    private function syncServices(ThirdPartyModel $model, ThirdParty $thirdParty): void
    {
        $desiredIds = [];
        foreach ($thirdParty->getServiceIds() as $serviceId) {
            $trimmed = trim((string) $serviceId);
            if ($trimmed === '') {
                continue;
            }
            $desiredIds[$trimmed] = $trimmed;
        }
        $desiredList = array_values($desiredIds);

        $existing = ServiceThirdPartyModel::query()
            ->where('third_parties_id', $model->id)
            ->get();

        $existingByServiceId = [];
        foreach ($existing as $row) {
            $sid = (string) $row->services_id;
            $existingByServiceId[$sid] = $row;
        }

        $idsToKeep = [];
        foreach ($desiredList as $serviceId) {
            if (isset($existingByServiceId[$serviceId])) {
                $idsToKeep[] = $existingByServiceId[$serviceId]->id;
                continue;
            }

            $newId = (string) Str::uuid();
            $updatedBy = $thirdParty->getUpdatedBy();
            if ($updatedBy === null) {
                $updatedBy = $thirdParty->getCreatedBy();
            }

            ServiceThirdPartyModel::query()->create([
                'id' => $newId,
                'services_id' => $serviceId,
                'third_parties_id' => $model->id,
                'created_by' => $thirdParty->getCreatedBy(),
                'updated_by' => $updatedBy,
            ]);
            $idsToKeep[] = $newId;
        }

        $deleteQuery = ServiceThirdPartyModel::query()
            ->where('third_parties_id', $model->id);

        if ($idsToKeep === []) {
            $deleteQuery->delete();

            return;
        }

        $deleteQuery->whereNotIn('id', $idsToKeep)->delete();
    }

    /**
     * @return array{ids: array<int, string>, items: array<int, array{id: string, service: string}>}
     */
    private function mapServices(ThirdPartyModel $model): array
    {
        if (! $model->relationLoaded('services')) {
            $model->load(['services' => function ($query) {
                $query->orderBy('services.service', 'asc');
            }]);
        }

        $ids = [];
        $items = [];
        foreach ($model->services as $service) {
            $id = (string) $service->id;
            $ids[] = $id;
            $items[] = [
                'id' => $id,
                'service' => (string) $service->service,
            ];
        }

        return [
            'ids' => $ids,
            'items' => $items,
        ];
    }

    private function resolveJobHistory(ThirdPartyModel $model): ThirdPartyJobHistory
    {
        $attributes = $model->getAttributes();
        if (array_key_exists('jobs_count', $attributes)) {
            $lastJobCode = null;
            if (isset($model->last_job_code) && $model->last_job_code !== '') {
                $lastJobCode = (string) $model->last_job_code;
            }

            return new ThirdPartyJobHistory((int) $model->jobs_count, $lastJobCode);
        }

        return $this->loadJobHistory((string) $model->id);
    }

    private function loadJobHistory(string $thirdPartyId): ThirdPartyJobHistory
    {
        $jobsCount = (int) DB::table('job_budget_third_party')
            ->where('third_party_id', $thirdPartyId)
            ->distinct()
            ->count('job_id');

        $lastJobCode = DB::table('job_budget_third_party as jb')
            ->join('job', 'job.id', '=', 'jb.job_id')
            ->where('jb.third_party_id', $thirdPartyId)
            ->orderByDesc('job.created_at')
            ->value('job.job_code');

        if ($lastJobCode === null || $lastJobCode === '') {
            return new ThirdPartyJobHistory($jobsCount, null);
        }

        return new ThirdPartyJobHistory($jobsCount, (string) $lastJobCode);
    }

    private function jobsCountSubquery()
    {
        return DB::table('job_budget_third_party')
            ->selectRaw('COUNT(DISTINCT job_id)')
            ->whereColumn('third_party_id', 'third_parties.id');
    }

    private function lastJobCodeSubquery()
    {
        return DB::table('job_budget_third_party as jb')
            ->join('job', 'job.id', '=', 'jb.job_id')
            ->select('job.job_code')
            ->whereColumn('jb.third_party_id', 'third_parties.id')
            ->orderByDesc('job.created_at')
            ->limit(1);
    }
}
