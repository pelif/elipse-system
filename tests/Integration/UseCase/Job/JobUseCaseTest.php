<?php

declare(strict_types=1);

namespace Tests\Integration\UseCase\Job;

use App\Domain\Job\Exceptions\JobInitialStatusNotFoundException;
use App\Infrastructure\Eloquent\Job\EloquentJobRepository;
use App\Models\Job as JobModel;
use App\Models\Order;
use App\UseCase\Job\CreateJobUseCase;
use App\UseCase\Job\DTO\CreateJobInputDto;
use App\UseCase\Job\ListJobsUseCase;
use App\UseCase\Job\NextJobCodeUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class JobUseCaseTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    public function test_create_throws_when_input_is_invalid(): void
    {
        $useCase = new CreateJobUseCase(new EloquentJobRepository);

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute(null);
    }

    public function test_create_persists_job_without_orders(): void
    {
        $graph = $this->context();
        $useCase = new CreateJobUseCase(new EloquentJobRepository);

        $output = $useCase->execute($this->input($graph, 'TST1C_0001', 0));

        $this->assertSame('TST1C_0001', $output->jobCode);
        $this->assertSame('Lançamento', $output->jobTitle);
        $this->assertSame('Cliente Alfa', $output->customerName);
        $this->assertSame('Carla', $output->contractorName);
        $this->assertSame('Live', $output->productName);
        $this->assertSame('Em análise', $output->statusName);
        $this->assertSame('Ana Comercial', $output->createdByName);
        $this->assertSame(0, $output->osQuantity);
        $this->assertSame('1500.50', $output->targetBudget);
        $this->assertSame('10.5000', $output->commissionedPercentage);
        $this->assertSame('2026-05-02 00:00:00', $output->commissionPaymentDueDate);
        $this->assertSame(0, Order::query()->where('job_id', $output->id)->count());
    }

    public function test_create_persists_draft_orders(): void
    {
        $graph = $this->context();
        $useCase = new CreateJobUseCase(new EloquentJobRepository);

        $output = $useCase->execute($this->input($graph, 'TST1C_0003', 3));

        $orders = Order::query()->where('job_id', $output->id)->orderBy('order_code')->get();
        $this->assertCount(3, $orders);
        $this->assertSame(3, $output->osQuantity);
        $this->assertSame(
            ['TST1C_0003_01', 'TST1C_0003_02', 'TST1C_0003_03'],
            $orders->pluck('order_code')->all()
        );

        foreach ($orders as $order) {
            $this->assertSame($graph['contractor']->id, $order->contact_id);
            $this->assertSame($graph['statuses']['order']->id, $order->status_id);
            $this->assertSame($graph['user']->id, (int) $order->created_by);
            $this->assertSame('Lançamento', $order->event_title);
        }
    }

    public function test_create_fails_when_initial_statuses_do_not_exist(): void
    {
        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');
        $product = $this->createJobProduct('Live');
        $contractor = $this->createJobContractor($customer, 'Carla');
        $user = $this->createJobUser();

        $useCase = new CreateJobUseCase(new EloquentJobRepository);

        try {
            $useCase->execute(new CreateJobInputDto(
                customerId: (string) $customer->id,
                customerCode: 'TST1C',
                contractorId: (string) $contractor->id,
                jobCode: 'TST1C_0001',
                jobTitle: 'Sem status',
                productId: (string) $product->id,
                createdBy: (int) $user->id,
            ));
            $this->fail('A criação deveria falhar sem os status iniciais.');
        } catch (JobInitialStatusNotFoundException $exception) {
            $this->assertStringContainsString('Em análise', $exception->getMessage());
            $this->assertStringContainsString('Não Agendado', $exception->getMessage());
        }

        $this->assertSame(0, JobModel::query()->count());
    }

    public function test_list_returns_data_meta_and_filters(): void
    {
        $graph = $this->context();
        $create = new CreateJobUseCase(new EloquentJobRepository);
        $create->execute($this->input($graph, 'TST1C_0001', 0, 'Lançamento Alfa'));

        $otherCustomer = $this->createJobCustomer('OTH2C', 'Cliente Beta');
        $otherProduct = $this->createJobProduct('Gravação');
        $otherContractor = $this->createJobContractor($otherCustomer, 'Bruno');
        $create->execute(new CreateJobInputDto(
            customerId: (string) $otherCustomer->id,
            customerCode: 'OTH2C',
            contractorId: (string) $otherContractor->id,
            jobCode: 'OTH2C_0001',
            jobTitle: 'Outro',
            productId: (string) $otherProduct->id,
            createdBy: (int) $graph['user']->id,
        ));

        $output = (new ListJobsUseCase(new EloquentJobRepository))->execute([
            'job_title' => 'Lançamento',
        ]);

        $this->assertCount(1, $output['data']);
        $this->assertSame('Lançamento Alfa', $output['data'][0]->jobTitle);
        $this->assertSame('Lançamento', $output['filters']['jobTitle']);
        $this->assertSame(10, $output['meta']['per_page']);
        $this->assertSame(1, $output['meta']['total']);
    }

    public function test_next_code_follows_the_created_job(): void
    {
        $graph = $this->context();
        $repository = new EloquentJobRepository;
        $nextCode = new NextJobCodeUseCase($repository);

        $this->assertSame('TST1C_0001', $nextCode->execute((string) $graph['customer']->id));

        (new CreateJobUseCase($repository))->execute($this->input($graph, 'TST1C_0005', 0));

        $this->assertSame('TST1C_0006', $nextCode->execute((string) $graph['customer']->id));
    }

    /**
     * @return array{customer: \App\Models\Customer, product: \App\Models\Product, contractor: \App\Models\Contact, user: \App\Models\User, statuses: array{job: \App\Models\Status, order: \App\Models\Status}}
     */
    private function context(): array
    {
        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');

        return [
            'customer' => $customer,
            'product' => $this->createJobProduct('Live'),
            'contractor' => $this->createJobContractor($customer, 'Carla'),
            'user' => $this->createJobUser(),
            'statuses' => $this->createJobStatuses(),
        ];
    }

    /**
     * @param  array{customer: \App\Models\Customer, product: \App\Models\Product, contractor: \App\Models\Contact, user: \App\Models\User, statuses: array{job: \App\Models\Status, order: \App\Models\Status}}  $graph
     */
    private function input(array $graph, string $code, int $osQuantity, string $title = 'Lançamento'): CreateJobInputDto
    {
        return new CreateJobInputDto(
            customerId: (string) $graph['customer']->id,
            customerCode: trim((string) $graph['customer']->code_customer),
            contractorId: (string) $graph['contractor']->id,
            jobCode: $code,
            jobTitle: $title,
            productId: (string) $graph['product']->id,
            osQuantity: $osQuantity,
            briefingUrl: 'https://briefing.example.com/job',
            targetBudget: '1500,50',
            purchaseOrder: 'PC-1',
            additionalInfo: 'Complemento',
            commissionedPercentage: '10,5',
            commissionPaymentDueDate: '02/05/2026',
            createdBy: (int) $graph['user']->id,
        );
    }
}
