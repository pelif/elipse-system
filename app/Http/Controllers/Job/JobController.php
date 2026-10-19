<?php

namespace App\Http\Controllers\Job;

use App\Domain\Job\Exceptions\JobInitialStatusNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\ListJobRequest;
use App\Http\Requests\Job\StoreJobRequest;
use App\UseCase\Customer\GetCustomerUseCase;
use App\UseCase\Job\CreateJobUseCase;
use App\UseCase\Job\DTO\CreateJobInputDto;
use App\UseCase\Job\ListJobsUseCase;
use App\UseCase\Job\NextJobCodeUseCase;
use App\UseCase\Status\ListJobStatusUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    public function __construct(
        private ListJobsUseCase $listJobsUseCase,
        private CreateJobUseCase $createJobUseCase,
        private NextJobCodeUseCase $nextJobCodeUseCase,
        private GetCustomerUseCase $getCustomerUseCase,
        private ListJobStatusUseCase $listJobStatusUseCase,
    ) {}

    public function index(ListJobRequest $request): Response
    {
        $data = $request->validated();
        $data['created_at'] = $this->normalizeFilterDate($data['created_at'] ?? null);
        $data['updated_at'] = $this->normalizeFilterDate($data['updated_at'] ?? null);

        $output = $this->listJobsUseCase->execute($data);

        return Inertia::render('Job/Index', [
            'jobs' => $output,
            'canCreateJobs' => $this->userIsCommercial($request),
            'jobStatuses' => $this->jobStatusOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Job/Create');
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $customer = $this->getCustomerUseCase->execute($data['customer_id']);

        if ($customer === null) {
            return back()->withErrors([
                'customer_id' => 'Cliente não encontrado.',
            ]);
        }

        $input = new CreateJobInputDto(
            customerId: $data['customer_id'],
            customerCode: $customer->codeCustomer,
            contractorId: $data['contractor_id'],
            jobCode: $data['job_code'],
            jobTitle: $data['job_title'],
            productId: $data['product_id'],
            osQuantity: (int) $data['os_quantity'],
            briefingUrl: $this->nullableString($data['briefing_url'] ?? null),
            observations: $this->nullableString($data['observations'] ?? null),
            targetBudget: $this->nullableString($data['target_budget'] ?? null),
            purchaseOrder: $this->nullableString($data['purchase_order'] ?? null),
            purchaseOrderLink: $this->nullableString($data['purchase_order_link'] ?? null),
            invoiceDueDate: $this->nullableString($data['invoice_due_date'] ?? null),
            additionalInfo: $this->nullableString($data['additional_info'] ?? null),
            comissionedAgentId: $this->nullableString($data['comissioned_agent_id'] ?? null),
            commissionedPercentage: $this->nullableString($data['commissioned_percentage'] ?? null),
            commissionPaymentDueDate: $this->nullableString($data['commission_payment_due_date'] ?? null),
            createdBy: $this->userId($request),
        );

        try {
            $output = $this->createJobUseCase->execute($input);
        } catch (JobInitialStatusNotFoundException $exception) {
            Inertia::flash('error', $exception->getMessage());

            return back();
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors([
                $this->errorField($exception->getMessage()) => $exception->getMessage(),
            ]);
        }

        if ($output->id !== '') {
            Inertia::flash('message', 'Job cadastrado com sucesso!');

            return to_route('jobs.index');
        }

        Inertia::flash('error', 'Não foi possível cadastrar o job.');

        return back();
    }

    public function nextCode(Request $request): JsonResponse
    {
        $customerId = $request->query('customer_id');
        if (! is_string($customerId) || trim($customerId) === '') {
            return response()->json(['message' => 'O cliente é obrigatório.'], 422);
        }

        try {
            $code = $this->nextJobCodeUseCase->execute(trim($customerId));
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['job_code' => $code]);
    }

    private function userIsCommercial(Request $request): bool
    {
        $user = $request->user();
        if ($user === null) {
            return false;
        }

        return $user->hasRole('commercial');
    }

    private function userId(Request $request): ?int
    {
        $userId = $request->user()?->id;
        if ($userId === null) {
            return null;
        }

        return (int) $userId;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return $trimmed;
    }

    private function normalizeFilterDate(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $trimmed) === 1) {
            $date = \DateTime::createFromFormat('!d/m/Y', $trimmed);
            if ($date === false) {
                return $trimmed;
            }

            return $date->format('Y-m-d');
        }

        return $trimmed;
    }

    /**
     * @return array<int, array{id: string, name: string}>
     */
    private function jobStatusOptions(): array
    {
        $listed = $this->listJobStatusUseCase->execute([
            'type' => 'job',
            'sortField' => 'status',
            'sortDirection' => 'asc',
        ]);

        $options = [];
        foreach ($listed['data'] as $status) {
            $options[] = [
                'id' => $status->id,
                'name' => $status->status,
            ];
        }

        return $options;
    }

    private function errorField(string $message): string
    {
        $lower = mb_strtolower($message);

        if (str_contains($lower, 'url') || str_contains($lower, 'briefing')) {
            return 'briefing_url';
        }

        if (str_contains($lower, 'título') || str_contains($lower, 'titulo')) {
            return 'job_title';
        }

        if (str_contains($lower, 'comissão') || str_contains($lower, 'comissao')) {
            return 'commissioned_percentage';
        }

        if (str_contains($lower, 'código') || str_contains($lower, 'codigo')) {
            return 'job_code';
        }

        if (str_contains($lower, 'data')) {
            return 'invoice_due_date';
        }

        if (str_contains($lower, 'produto')) {
            return 'product_id';
        }

        if (str_contains($lower, 'contratante')) {
            return 'contractor_id';
        }

        if (str_contains($lower, 'cliente')) {
            return 'customer_id';
        }

        if (str_contains($lower, 'decimal') || str_contains($lower, 'orçamento') || str_contains($lower, 'orcamento')) {
            return 'target_budget';
        }

        return 'job_code';
    }
}
