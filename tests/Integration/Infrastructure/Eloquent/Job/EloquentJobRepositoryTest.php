<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\Job;

use App\Domain\Job\Entities\Job as JobEntity;
use App\Domain\Job\Exceptions\JobInitialStatusNotFoundException;
use App\Domain\Job\ValueObjects\BriefingUrl;
use App\Domain\Job\ValueObjects\JobCode;
use App\Domain\Status\Enums\StatusType;
use App\Infrastructure\Eloquent\Job\EloquentJobRepository;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Job as JobModel;
use App\Models\JobOrganizer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class EloquentJobRepositoryTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    private EloquentJobRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentJobRepository;
    }

    public function test_job_table_has_entry_columns_and_unique_job_code(): void
    {
        $this->assertTrue(Schema::hasColumns('job', [
            'job_title',
            'os_quantity',
            'observations',
            'approval_date',
            'target_budget',
            'purchase_order',
            'purchase_order_link',
            'invoice_due_date',
            'additional_info',
            'comissioned_agent_id',
            'commissioned_percentage',
            'commission_value',
            'commission_payment_due_date',
            'tax_percentage',
            'profit_percentage',
            'monthly_interest_rate',
            'daily_interest_rate',
            'admin_expenses_percentage',
            'financial_expenses_percentage',
            'contractor_id',
        ]));
        $this->assertTrue(Schema::hasIndex('job', 'job_job_code_unique', 'unique'));
    }

    public function test_create_without_orders_persists_job_in_analysis(): void
    {
        $graph = $this->graph();
        $agent = $this->createJobAgent('Agente Sul');

        $created = $this->repository->create($this->draft(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Lançamento',
            0,
            (string) $agent->id,
            '1500.50',
            '01/04/2026'
        ));

        $this->assertNotNull($created->getId());
        $this->assertSame('TST1C_0001', $created->getJobCode()->getValue());
        $this->assertSame('Lançamento', $created->getJobTitle());
        $this->assertSame(0, $created->getOsQuantity());
        $status = $created->getStatus();
        $customer = $created->getCustomer();
        $product = $created->getProduct();
        $agent = $created->getComissionedAgent();
        $this->assertNotNull($status);
        $this->assertNotNull($customer);
        $this->assertNotNull($product);
        $this->assertNotNull($agent);
        $this->assertSame('Em análise', $status->getStatus());
        $this->assertSame('Cliente Alfa', $customer->getCompanyName());
        $this->assertSame('Live', $product->getName());
        $this->assertSame('Carla', $created->getContractorName());
        $this->assertSame('Agente Sul', $agent->getName());
        $this->assertSame('Ana Comercial', $created->getCreatedByName());
        $this->assertSame('1500.50', $created->getTargetBudget());
        $this->assertSame('2026-04-01 00:00:00', $created->getInvoiceDueDate());
        $this->assertSame('0.0000', $created->getAdminExpensesPercentage());
        $this->assertNull($created->getApprovalDate());
        $this->assertNull($created->getCommissionValue());
        $this->assertSame(0, Order::query()->where('job_id', $created->getId())->count());

        $row = JobModel::query()->find($created->getId());
        $this->assertSame('https://briefing.example.com/job', $row->attachments);
        $this->assertSame($graph['statuses']['job']->id, $row->status_id);
    }

    public function test_create_with_draft_orders(): void
    {
        $graph = $this->graph();

        $created = $this->repository->create($this->draft(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0002',
            'Com OS',
            2
        ));

        $orders = Order::query()->where('job_id', $created->getId())->orderBy('order_code')->get();
        $this->assertCount(2, $orders);
        $this->assertSame(2, $created->getOsQuantity());
        $this->assertSame(['TST1C_0002_01', 'TST1C_0002_02'], $orders->pluck('order_code')->all());

        foreach ($orders as $order) {
            $this->assertSame($graph['customer']->id, $order->customer_id);
            $this->assertSame($graph['contractor']->id, $order->contact_id);
            $this->assertSame($graph['statuses']['order']->id, $order->status_id);
            $this->assertSame($graph['user']->id, (int) $order->created_by);
            $this->assertSame('Com OS', $order->event_title);
        }
    }

    public function test_create_links_active_organizers_of_the_customer(): void
    {
        $graph = $this->graph();
        $marina = $this->createJobOrganizer($graph['customer'], 'Marina');
        $paulo = $this->createJobOrganizer($graph['customer'], 'Paulo');
        $otherCustomer = $this->createJobCustomer('OTH2C', 'Outro Cliente');
        $foreign = $this->createJobOrganizer($otherCustomer, 'Helena');

        $created = $this->repository->create($this->draft(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0003',
            'Com organizadores'
        ));

        $linked = JobOrganizer::query()
            ->where('job_id', $created->getId())
            ->orderBy('organizer_id')
            ->pluck('organizer_id')
            ->all();

        $expected = [(string) $marina->id, (string) $paulo->id];
        sort($expected);
        sort($linked);

        $this->assertSame($expected, $linked);
        $this->assertNotContains((string) $foreign->id, $linked);
        $this->assertNotContains((string) $graph['contractor']->id, $linked);
    }

    public function test_create_fails_when_initial_status_is_missing_and_rolls_back(): void
    {
        $graph = $this->graph(false);
        Status::query()->create([
            'status' => 'Em análise',
            'type' => StatusType::JOB->value,
        ]);

        try {
            $this->repository->create($this->draft(
                $graph['customer'],
                $graph['product'],
                $graph['contractor'],
                $graph['user'],
                'TST1C_0001',
                'Sem OS status'
            ));
            $this->fail('A criação deveria falhar sem o status Não Agendado.');
        } catch (JobInitialStatusNotFoundException $exception) {
            $this->assertStringContainsString('Não Agendado', $exception->getMessage());
        }

        $this->assertSame(0, JobModel::query()->count());
        $this->assertSame(0, Order::query()->count());
        $this->assertSame(0, JobOrganizer::query()->count());
    }

    public function test_create_rejects_code_that_does_not_belong_to_the_customer(): void
    {
        $graph = $this->graph();

        $this->expectException(InvalidArgumentException::class);

        $this->repository->create(new JobEntity(
            customerId: (string) $graph['customer']->id,
            productId: (string) $graph['product']->id,
            jobCode: JobCode::fromString('OTH2C_0001', 'OTH2C'),
            jobTitle: 'Código alheio',
            contractorId: (string) $graph['contractor']->id,
        ));
    }

    public function test_next_code_reads_the_highest_suffix_of_that_customer(): void
    {
        $graph = $this->graph();
        $otherCustomer = $this->createJobCustomer('OTH2C', 'Cliente Beta');

        $this->assertSame('TST1C_0001', $this->repository->nextCodeForCustomer((string) $graph['customer']->id));

        $this->repository->create($this->draft(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Primeiro'
        ));

        JobModel::query()->create([
            'customer_id' => $graph['customer']->id,
            'product_id' => $graph['product']->id,
            'contractor_id' => $graph['contractor']->id,
            'status_id' => $graph['statuses']['job']->id,
            'job_code' => 'TST1C_0009',
            'job_title' => 'Salto',
            'os_quantity' => 0,
        ]);
        JobModel::query()->create([
            'customer_id' => $graph['customer']->id,
            'product_id' => $graph['product']->id,
            'contractor_id' => $graph['contractor']->id,
            'status_id' => $graph['statuses']['job']->id,
            'job_code' => 'OTHER_0008',
            'job_title' => 'Ignorado',
            'os_quantity' => 0,
        ]);

        $this->assertSame('TST1C_0010', $this->repository->nextCodeForCustomer((string) $graph['customer']->id));
        $this->assertSame('OTH2C_0001', $this->repository->nextCodeForCustomer((string) $otherCustomer->id));
    }

    public function test_next_code_ignores_a_counter_glued_to_the_customer_code(): void
    {
        $customer = $this->createJobCustomer('NCT001', 'Neo Customer');
        $product = $this->createJobProduct('Live');
        $contractor = $this->createJobContractor($customer, 'Carla');
        $statuses = $this->createJobStatuses();

        JobModel::query()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'contractor_id' => $contractor->id,
            'status_id' => $statuses['job']->id,
            'job_code' => 'NCT001_0001',
            'job_title' => 'Código antigo',
            'os_quantity' => 0,
        ]);

        $listed = $this->repository->findAllPaginated([
            'customer_id' => $customer->id,
        ]);
        $this->assertSame('NCT001_0001', $listed['data'][0]->getJobCode()->getValue());

        $this->assertSame('NCT_0001', $this->repository->nextCodeForCustomer((string) $customer->id));

        $this->repository->create($this->draft(
            $customer,
            $product,
            $contractor,
            $this->createJobUser(),
            'NCT_0001',
            'Primeiro válido'
        ));

        $this->assertSame('NCT_0002', $this->repository->nextCodeForCustomer((string) $customer->id));
    }

    public function test_find_all_paginated_filters_and_pages(): void
    {
        $graph = $this->graph();
        $otherCustomer = $this->createJobCustomer('OTH2C', 'Cliente Beta');
        $otherProduct = $this->createJobProduct('Gravação');
        $otherContractor = $this->createJobContractor($otherCustomer, 'Bruno');

        $first = $this->repository->create($this->draft(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Lançamento Alfa'
        ));

        $quote = Status::query()->create([
            'status' => 'Orçar',
            'type' => StatusType::JOB->value,
        ]);
        JobModel::query()->where('id', $first->getId())->update([
            'status_id' => $quote->id,
            'created_at' => '2024-01-10 08:00:00',
            'updated_at' => '2024-02-11 09:00:00',
        ]);

        $this->repository->create($this->draft(
            $otherCustomer,
            $otherProduct,
            $otherContractor,
            $graph['user'],
            'OTH2C_0001',
            'Outro job'
        ));

        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['customer' => 'Alfa']));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['customer_id' => $graph['customer']->id]));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['job_code' => 'TST1C_0001']));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['job_title' => 'Lançamento']));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['product' => 'Live']));
        $this->assertSame('Outro job', $this->onlyTitle(['product_id' => $otherProduct->id]));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['status' => 'Orçar']));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['status_id' => $quote->id]));
        $this->assertSame('Outro job', $this->onlyTitle(['contractor' => 'Bruno']));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['contractor_id' => $graph['contractor']->id]));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['created_at' => '2024-01-10']));
        $this->assertSame('Lançamento Alfa', $this->onlyTitle(['updated_at' => '2024-02-11']));

        for ($sequence = 2; $sequence <= 11; $sequence++) {
            $suffix = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $this->repository->create($this->draft(
                $graph['customer'],
                $graph['product'],
                $graph['contractor'],
                $graph['user'],
                'TST1C_'.$suffix,
                'Página '.$suffix
            ));
        }

        $page = $this->repository->findAllPaginated([
            'customer_id' => $graph['customer']->id,
            'sortField' => 'job_code',
            'sortDirection' => 'asc',
            'page' => 2,
        ]);

        $this->assertCount(1, $page['data']);
        $this->assertSame(10, $page['meta']['per_page']);
        $this->assertSame(2, $page['meta']['last_page']);
        $this->assertSame(11, $page['meta']['total']);
        $this->assertArrayHasKey('links', $page['meta']);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function onlyTitle(array $filters): string
    {
        $result = $this->repository->findAllPaginated($filters);
        $this->assertCount(1, $result['data']);

        return $result['data'][0]->getJobTitle();
    }

    /**
     * @return array{customer: Customer, product: Product, contractor: Contact, user: User, statuses: array{job: Status, order: Status}}
     */
    private function graph(bool $withStatuses = true): array
    {
        $statuses = [
            'job' => new Status,
            'order' => new Status,
        ];
        if ($withStatuses) {
            $statuses = $this->createJobStatuses();
        }

        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');

        return [
            'customer' => $customer,
            'product' => $this->createJobProduct('Live'),
            'contractor' => $this->createJobContractor($customer, 'Carla'),
            'user' => $this->createJobUser(),
            'statuses' => $statuses,
        ];
    }

    private function draft(
        Customer $customer,
        Product $product,
        Contact $contractor,
        User $user,
        string $code,
        string $title,
        int $osQuantity = 0,
        ?string $agentId = null,
        ?string $targetBudget = null,
        ?string $invoiceDueDate = null,
    ): JobEntity {
        $customerCode = trim((string) $customer->code_customer);

        return new JobEntity(
            customerId: (string) $customer->id,
            productId: (string) $product->id,
            jobCode: JobCode::fromString($code, $customerCode),
            jobTitle: $title,
            contractorId: (string) $contractor->id,
            briefingUrl: BriefingUrl::fromString('https://briefing.example.com/job'),
            createdBy: (int) $user->id,
            osQuantity: $osQuantity,
            targetBudget: $targetBudget,
            invoiceDueDate: $invoiceDueDate,
            comissionedAgentId: $agentId,
            commissionedPercentage: '12.5000',
            purchaseOrder: 'PC-9',
            additionalInfo: 'Notas',
        );
    }
}
