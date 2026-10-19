<?php

namespace App\Infrastructure\Eloquent\Job;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent as ComissionedAgentEntity;
use App\Domain\Customer\Entities\Customer as CustomerEntity;
use App\Domain\Customer\ValueObjects\Document;
use App\Domain\Job\Entities\Job as JobEntity;
use App\Domain\Job\Exceptions\JobInitialStatusNotFoundException;
use App\Domain\Job\Repositories\JobOrganizerRepositoryInterface;
use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\Domain\Job\ValueObjects\BriefingUrl;
use App\Domain\Job\ValueObjects\JobCode;
use App\Domain\Job\ValueObjects\OrderCode;
use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Models\ComissionedAgent as ComissionedAgentModel;
use App\Models\Contact as ContactModel;
use App\Models\Customer as CustomerModel;
use App\Models\Job as JobModel;
use App\Models\Order as OrderModel;
use App\Models\Status as StatusModel;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class EloquentJobRepository implements JobRepositoryInterface
{
    private const INITIAL_JOB_STATUS = 'Em análise';

    private const INITIAL_ORDER_STATUS = 'Não Agendado';

    private const ORGANIZER_TYPE = 'Organizer';

    public function __construct(
        private ?JobOrganizerRepositoryInterface $jobOrganizerRepository = null,
    ) {}

    public function create(JobEntity $job): JobEntity
    {
        try {
            return DB::transaction(function () use ($job) {
                $customer = CustomerModel::query()->find($job->getCustomerId());
                if ($customer === null) {
                    throw new \InvalidArgumentException('Cliente não encontrado.');
                }

                $jobCode = JobCode::fromString(
                    $job->getJobCode()->getValue(),
                    trim((string) $customer->code_customer)
                );

                $jobStatus = StatusModel::query()
                    ->where('status', self::INITIAL_JOB_STATUS)
                    ->where('type', StatusType::JOB->value)
                    ->first();
                $orderStatus = StatusModel::query()
                    ->where('status', self::INITIAL_ORDER_STATUS)
                    ->where('type', StatusType::ORDER->value)
                    ->first();

                if ($jobStatus === null || $orderStatus === null) {
                    throw JobInitialStatusNotFoundException::forMissing(
                        $jobStatus === null,
                        $orderStatus === null
                    );
                }

                $codeInUse = JobModel::query()->where('job_code', $jobCode->getValue())->exists();
                if ($codeInUse) {
                    throw new \InvalidArgumentException('Já existe um job com este código.');
                }

                $model = JobModel::query()->create($this->persistAttributes($job, $jobCode, (string) $jobStatus->id));

                $quantity = $job->getOsQuantity();
                $persistedJobCode = $jobCode->getValue();
                for ($index = 0; $index < $quantity; $index++) {
                    $orderCode = OrderCode::fromJobCodeAndSequence($persistedJobCode, $index + 1);
                    OrderModel::query()->create([
                        'customer_id' => $customer->id,
                        'contact_id' => $job->getContractorId(),
                        'job_id' => $model->id,
                        'status_id' => $orderStatus->id,
                        'order_code' => $orderCode->getValue(),
                        'event_title' => $job->getJobTitle(),
                        'created_by' => $job->getCreatedBy(),
                    ]);
                }

                $this->syncCustomerOrganizers($model);

                $fresh = $model->fresh($this->relations());
                if ($fresh === null) {
                    throw new \RuntimeException('Job não encontrado após a criação.');
                }

                return $this->toEntity($fresh);
            });
        } catch (UniqueConstraintViolationException) {
            throw new \InvalidArgumentException('Já existe um job com este código.');
        }
    }

    public function findAllPaginated(array $filters = []): array
    {
        $sortField = 'updated_at';
        if (isset($filters['sortField']) && is_string($filters['sortField']) && $filters['sortField'] !== '') {
            $sortField = $filters['sortField'];
        }

        $sortDirection = 'desc';
        if (isset($filters['sortDirection']) && is_string($filters['sortDirection']) && $filters['sortDirection'] !== '') {
            $sortDirection = $filters['sortDirection'];
        }

        $allowedSortFields = [
            'id',
            'job_code',
            'job_title',
            'os_quantity',
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

        $query = JobModel::query()->with($this->relations());

        $customerId = $this->filterValue($filters, 'customer_id', 'customerId');
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $customer = $this->filterValue($filters, 'customer', 'customer');
        if ($customer !== null) {
            $query->whereHas('customer', function ($customerQuery) use ($customer) {
                $customerQuery->where('company_name', 'like', '%'.$customer.'%')
                    ->orWhere('code_customer', 'like', '%'.$customer.'%');
            });
        }

        $jobCode = $this->filterValue($filters, 'job_code', 'jobCode');
        if ($jobCode !== null) {
            $query->where('job_code', 'like', '%'.$jobCode.'%');
        }

        $jobTitle = $this->filterValue($filters, 'job_title', 'jobTitle');
        if ($jobTitle !== null) {
            $query->where('job_title', 'like', '%'.$jobTitle.'%');
        }

        $productId = $this->filterValue($filters, 'product_id', 'productId');
        if ($productId !== null) {
            $query->where('product_id', $productId);
        }

        $product = $this->filterValue($filters, 'product', 'product');
        if ($product !== null) {
            $query->whereHas('product', function ($productQuery) use ($product) {
                $productQuery->where('name', 'like', '%'.$product.'%');
            });
        }

        $statusId = $this->filterValue($filters, 'status_id', 'statusId');
        if ($statusId !== null) {
            $query->where('status_id', $statusId);
        }

        $status = $this->filterValue($filters, 'status', 'status');
        if ($status !== null) {
            $query->whereHas('status', function ($statusQuery) use ($status) {
                $statusQuery->where('status', 'like', '%'.$status.'%');
            });
        }

        $contractorId = $this->filterValue($filters, 'contractor_id', 'contractorId');
        if ($contractorId !== null) {
            $query->where('contractor_id', $contractorId);
        }

        $contractor = $this->filterValue($filters, 'contractor', 'contractor');
        if ($contractor !== null) {
            $query->whereHas('contractor', function ($contractorQuery) use ($contractor) {
                $contractorQuery->where('name', 'like', '%'.$contractor.'%');
            });
        }

        $this->applyDateFilter($query, 'created_at', $this->filterValue($filters, 'created_at', 'createdAt'));
        $this->applyDateFilter($query, 'updated_at', $this->filterValue($filters, 'updated_at', 'updatedAt'));

        $page = null;
        if (isset($filters['page'])) {
            $page = max(1, (int) $filters['page']);
        }

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (JobModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    public function nextCodeForCustomer(string $customerId): string
    {
        $customer = CustomerModel::query()->find($customerId);
        if ($customer === null) {
            throw new \InvalidArgumentException('Cliente não encontrado.');
        }

        $customerCode = trim((string) $customer->code_customer);
        $highest = 0;
        $codes = JobModel::query()
            ->where('customer_id', $customerId)
            ->pluck('job_code');

        foreach ($codes as $code) {
            $rawCode = null;
            if ($code !== null) {
                $rawCode = (string) $code;
            }

            $sequence = JobCode::trySequence($rawCode, $customerCode);
            if ($sequence !== null && $sequence > $highest) {
                $highest = $sequence;
            }
        }

        return JobCode::fromSequence($customerCode, $highest + 1)->getValue();
    }

    private function syncCustomerOrganizers(JobModel $job): void
    {
        $organizerIds = ContactModel::query()
            ->where('customer_id', $job->customer_id)
            ->where('type_contact', self::ORGANIZER_TYPE)
            ->pluck('id')
            ->all();

        $ids = [];
        foreach ($organizerIds as $organizerId) {
            $ids[] = (string) $organizerId;
        }

        $this->jobOrganizerRepository()->syncOrganizersForJob((string) $job->id, $ids);
    }

    private function jobOrganizerRepository(): JobOrganizerRepositoryInterface
    {
        if ($this->jobOrganizerRepository === null) {
            $this->jobOrganizerRepository = app(JobOrganizerRepositoryInterface::class);
        }

        return $this->jobOrganizerRepository;
    }

    /**
     * @return list<string>
     */
    private function relations(): array
    {
        return [
            'customer',
            'product',
            'status',
            'comissionedAgent',
            'contractor',
            'creator',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function persistAttributes(JobEntity $job, JobCode $jobCode, string $statusId): array
    {
        $briefingUrl = null;
        if ($job->getBriefingUrl() !== null) {
            $briefingUrl = $job->getBriefingUrl()->getValue();
        }

        return [
            'customer_id' => $job->getCustomerId(),
            'product_id' => $job->getProductId(),
            'contractor_id' => $job->getContractorId(),
            'job_code' => $jobCode->getValue(),
            'job_title' => $job->getJobTitle(),
            'attachments' => $briefingUrl,
            'status_id' => $statusId,
            'os_quantity' => $job->getOsQuantity(),
            'observations' => $job->getObservations(),
            'approval_date' => $this->normalizeDateTime($job->getApprovalDate()),
            'target_budget' => $this->normalizeDecimal($job->getTargetBudget(), 2),
            'purchase_order' => $job->getPurchaseOrder(),
            'purchase_order_link' => $job->getPurchaseOrderLink(),
            'invoice_due_date' => $this->normalizeDateTime($job->getInvoiceDueDate()),
            'additional_info' => $job->getAdditionalInfo(),
            'comissioned_agent_id' => $job->getComissionedAgentId(),
            'commissioned_percentage' => $this->normalizeDecimal($job->getCommissionedPercentage(), 4),
            'commission_value' => $this->normalizeDecimal($job->getCommissionValue(), 2),
            'commission_payment_due_date' => $this->normalizeDateTime($job->getCommissionPaymentDueDate()),
            'tax_percentage' => $this->normalizeDecimal($job->getTaxPercentage(), 4),
            'profit_percentage' => $this->normalizeDecimal($job->getProfitPercentage(), 4),
            'monthly_interest_rate' => $this->normalizeDecimal($job->getMonthlyInterestRate(), 4),
            'daily_interest_rate' => $this->normalizeDecimal($job->getDailyInterestRate(), 4),
            'admin_expenses_percentage' => $this->requiredDecimal($job->getAdminExpensesPercentage(), 4),
            'financial_expenses_percentage' => $this->requiredDecimal($job->getFinancialExpensesPercentage(), 4),
            'created_by' => $job->getCreatedBy(),
            'updated_by' => $job->getUpdatedBy(),
        ];
    }

    private function toEntity(JobModel $model): JobEntity
    {
        $customer = null;
        if ($model->customer !== null) {
            $customer = $this->mapCustomer($model->customer);
        }

        $product = null;
        if ($model->product !== null) {
            $product = $this->mapProduct($model->product);
        }

        $status = null;
        if ($model->status !== null) {
            $status = $this->mapStatus($model->status);
        }

        $agent = null;
        if ($model->comissionedAgent !== null) {
            $agent = $this->mapAgent($model->comissionedAgent);
        }

        $contractorName = null;
        if ($model->contractor !== null) {
            $contractorName = $model->contractor->name;
        }

        $createdByName = null;
        if ($model->creator !== null) {
            $createdByName = $model->creator->name;
        }

        $briefingUrl = BriefingUrl::fromNullable($model->attachments);

        return new JobEntity(
            customerId: (string) $model->customer_id,
            productId: (string) $model->product_id,
            jobCode: JobCode::fromPersisted((string) $model->job_code),
            jobTitle: (string) $model->job_title,
            contractorId: (string) $model->contractor_id,
            id: (string) $model->id,
            customer: $customer,
            product: $product,
            briefingUrl: $briefingUrl,
            approvalAr: $this->formatDateTime($model->approval_ar),
            statusId: $this->stringOrNull($model->status_id),
            status: $status,
            createdBy: $this->intOrNull($model->created_by),
            createdByName: $createdByName,
            updatedBy: $this->intOrNull($model->updated_by),
            createdAt: $this->formatTimestamp($model->created_at),
            updatedAt: $this->formatTimestamp($model->updated_at),
            osQuantity: (int) $model->os_quantity,
            observations: $model->observations,
            approvalDate: $this->formatDateTime($model->approval_date),
            targetBudget: $this->normalizeDecimal($this->stringOrNull($model->target_budget), 2),
            purchaseOrder: $model->purchase_order,
            purchaseOrderLink: $model->purchase_order_link,
            invoiceDueDate: $this->formatDateTime($model->invoice_due_date),
            additionalInfo: $model->additional_info,
            comissionedAgentId: $this->stringOrNull($model->comissioned_agent_id),
            comissionedAgent: $agent,
            commissionedPercentage: $this->normalizeDecimal($this->stringOrNull($model->commissioned_percentage), 4),
            commissionValue: $this->normalizeDecimal($this->stringOrNull($model->commission_value), 2),
            commissionPaymentDueDate: $this->formatDateTime($model->commission_payment_due_date),
            taxPercentage: $this->normalizeDecimal($this->stringOrNull($model->tax_percentage), 4),
            profitPercentage: $this->normalizeDecimal($this->stringOrNull($model->profit_percentage), 4),
            monthlyInterestRate: $this->normalizeDecimal($this->stringOrNull($model->monthly_interest_rate), 4),
            dailyInterestRate: $this->normalizeDecimal($this->stringOrNull($model->daily_interest_rate), 4),
            adminExpensesPercentage: $this->requiredDecimal($this->stringOrNull($model->admin_expenses_percentage), 4),
            financialExpensesPercentage: $this->requiredDecimal($this->stringOrNull($model->financial_expenses_percentage), 4),
            contractorName: $contractorName,
        );
    }

    private function mapCustomer(CustomerModel $model): CustomerEntity
    {
        return new CustomerEntity(
            id: $model->id,
            companyName: (string) ($model->company_name ?? ''),
            legalName: (string) ($model->legal_name ?? ''),
            document: Document::fromPersisted($model->document),
            codeCustomer: trim((string) ($model->code_customer ?? '')),
            active: (bool) $model->active,
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: $this->intOrNull($model->created_by),
            updatedBy: $this->intOrNull($model->updated_by),
            createdAt: $this->formatTimestamp($model->created_at),
            updatedAt: $this->formatTimestamp($model->updated_at),
            comissionedAgentId: null,
            comissionedAgent: null,
            financialInfo: null,
            contacts: [],
            relationShipsCounts: null,
        );
    }

    private function mapProduct(\App\Models\Product $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            name: (string) $model->name,
            description: $model->description,
            createdAt: $this->formatTimestamp($model->created_at),
            updatedAt: $this->formatTimestamp($model->updated_at),
        );
    }

    private function mapStatus(StatusModel $model): StatusEntity
    {
        $type = StatusType::tryFrom((int) $model->type);
        if ($type === null) {
            throw new \InvalidArgumentException('Tipo de status inválido.');
        }

        return new StatusEntity(
            id: $model->id,
            status: (string) $model->status,
            type: $type,
            observacoes: $model->observacoes,
        );
    }

    private function mapAgent(ComissionedAgentModel $model): ComissionedAgentEntity
    {
        $defaultComission = 0.0;
        if ($model->default_comission !== null) {
            $defaultComission = (float) $model->default_comission;
        }

        $paymentDays = 0;
        if ($model->payment_days !== null) {
            $paymentDays = (int) $model->payment_days;
        }

        $name = '';
        if ($model->name !== null) {
            $name = (string) $model->name;
        }

        return new ComissionedAgentEntity(
            id: $model->id,
            name: $name,
            defaultComission: $defaultComission,
            paymentDays: $paymentDays,
            observations: $model->observations,
            active: (bool) $model->active,
            createdBy: $this->intOrNull($model->created_by),
            updatedBy: $this->intOrNull($model->updated_by),
            createdAt: $this->formatTimestamp($model->created_at),
            updatedAt: $this->formatTimestamp($model->updated_at),
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filterValue(array $filters, string $snake, string $camel): mixed
    {
        if (array_key_exists($snake, $filters) && $filters[$snake] !== null && $filters[$snake] !== '') {
            return $filters[$snake];
        }

        if (array_key_exists($camel, $filters) && $filters[$camel] !== null && $filters[$camel] !== '') {
            return $filters[$camel];
        }

        return null;
    }

    private function applyDateFilter(mixed $query, string $column, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        try {
            $query->whereDate($column, Carbon::parse((string) $value)->toDateString());
        } catch (\Throwable) {
            // ignore invalid date filter
        }
    }

    private function normalizeDateTime(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $trimmed) === 1) {
            $date = \DateTime::createFromFormat('!d/m/Y', $trimmed);
            if ($date === false) {
                throw new \InvalidArgumentException('Data inválida.');
            }

            return $date->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($trimmed)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            throw new \InvalidArgumentException('Data inválida.');
        }
    }

    private function normalizeDecimal(?string $value, int $scale): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $trimmed);
        if (! is_numeric($normalized)) {
            throw new \InvalidArgumentException('Valor decimal inválido.');
        }

        if ((float) $normalized < 0) {
            throw new \InvalidArgumentException('Valor decimal não pode ser negativo.');
        }

        return number_format((float) $normalized, $scale, '.', '');
    }

    private function requiredDecimal(?string $value, int $scale): string
    {
        $normalized = $this->normalizeDecimal($value, $scale);
        if ($normalized === null) {
            return number_format(0, $scale, '.', '');
        }

        return $normalized;
    }

    private function formatDateTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) $value;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    private function intOrNull(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return (int) $value;
    }

    private function formatTimestamp(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        return (string) $value;
    }
}
