<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\Order;

use App\Domain\Job\Entities\Job as JobEntity;
use App\Domain\Job\ValueObjects\BriefingUrl;
use App\Domain\Job\ValueObjects\JobCode;
use App\Infrastructure\Eloquent\Job\EloquentJobRepository;
use App\Infrastructure\Eloquent\Order\EloquentOrderRepository;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class EloquentOrderRepositoryTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    private EloquentOrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentOrderRepository;
    }

    public function test_lists_newest_orders_first_with_pagination_meta(): void
    {
        $graph = $this->graph();
        $this->createJob(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Launch Alfa',
            2
        );

        Order::query()->where('order_code', 'TST1C_0001_01')->update([
            'created_at' => '2026-01-01 08:00:00',
        ]);
        Order::query()->where('order_code', 'TST1C_0001_02')->update([
            'created_at' => '2026-06-01 08:00:00',
        ]);

        $page = $this->repository->findAllPaginated([
            'sortField' => 'not_a_column',
            'sortDirection' => 'sideways',
        ]);

        $this->assertCount(2, $page['data']);
        $this->assertSame('TST1C_0001_02', $page['data'][0]->getOsCode());
        $this->assertSame('TST1C_0001_01', $page['data'][1]->getOsCode());
        $this->assertSame(1, $page['meta']['current_page']);
        $this->assertSame(1, $page['meta']['last_page']);
        $this->assertSame(10, $page['meta']['per_page']);
        $this->assertSame(2, $page['meta']['total']);
        $this->assertArrayHasKey('links', $page['meta']);
    }

    public function test_lists_orders_without_order_code(): void
    {
        $graph = $this->graph();
        $this->createJob(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Launch Alfa',
            1
        );

        Order::query()->where('order_code', 'TST1C_0001_01')->update([
            'order_code' => null,
        ]);

        $page = $this->repository->findAllPaginated();

        $this->assertCount(1, $page['data']);
        $this->assertNull($page['data'][0]->getOsCode());
        $this->assertSame('Launch Alfa', $page['data'][0]->getEventTitle());
    }

    public function test_filters_by_order_code_job_code_title_customer_and_organizer(): void
    {
        $graph = $this->graph();
        $marina = $this->createJobOrganizer($graph['customer'], 'Marina');
        $this->createJob(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Launch Alfa',
            1
        );

        $otherCustomer = $this->createJobCustomer('OTH2C', 'Cliente Beta');
        $otherContractor = $this->createJobContractor($otherCustomer, 'Bruno');
        $this->createJob(
            $otherCustomer,
            $graph['product'],
            $otherContractor,
            $graph['user'],
            'OTH2C_0001',
            'Launch Beta',
            1
        );

        Order::query()->where('order_code', 'TST1C_0001_01')->update([
            'organizer_id' => $marina->id,
        ]);

        $this->assertSame('TST1C_0001_01', $this->onlyCode(['order_code' => 'TST1C_0001_01']));
        $this->assertSame('TST1C_0001_01', $this->onlyCode(['osCode' => 'TST1C_0001']));
        $this->assertSame('OTH2C_0001_01', $this->onlyCode(['job_code' => 'OTH2C']));
        $this->assertSame('Launch Alfa', $this->onlyTitle(['event_title' => 'Alfa']));
        $this->assertSame('TST1C_0001_01', $this->onlyCode(['customer' => 'Alfa LTDA']));
        $this->assertSame('TST1C_0001_01', $this->onlyCode(['customer' => 'TST1C']));
        $this->assertSame('TST1C_0001_01', $this->onlyCode(['organizer' => 'Marina']));
        $this->assertSame(0, count($this->repository->findAllPaginated(['organizer' => 'Carla'])['data']));
        $this->assertSame(0, count($this->repository->findAllPaginated(['organizer' => 'Bruno'])['data']));
    }

    public function test_find_by_id_maps_schedule_production_fields_and_relations(): void
    {
        $graph = $this->graph();
        $marina = $this->createJobOrganizer($graph['customer'], 'Marina');
        $job = $this->createJob(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Launch Alfa',
            1
        );

        $row = Order::query()->where('order_code', 'TST1C_0001_01')->first();
        $this->assertNotNull($row);
        $row->update([
            'organizer_id' => $marina->id,
            'os_type' => 'Remoto',
            'start_time' => '2026-06-01 10:00:00',
            'end_time' => '2026-06-01 12:30:00',
            'duration' => 90,
            'location' => 'Estúdio',
            'location_details' => 'Sala 2',
            'number_participants' => 25,
            'communication_notes' => 'Falar devagar',
            'production_notes' => 'Duas câmeras',
            'platform_destination' => 'Zoom',
            'video_engine' => 'Vimeo',
            'client_platform_name' => 'Portal Alfa',
            'platform_link' => 'https://zoom.example/room',
            'file_link' => 'https://files.example/brief',
        ]);

        $order = $this->repository->findById((string) $row->id);

        $this->assertNotNull($order);
        $this->assertSame('TST1C_0001_01', $order->getOsCode());
        $this->assertSame('Launch Alfa', $order->getEventTitle());
        $this->assertSame('TST1C_0001', $order->getJobCode());
        $this->assertSame('Launch Alfa', $order->getJobTitle());
        $this->assertSame((string) $job->getId(), $order->getJobId());
        $this->assertSame('Cliente Alfa LTDA', $order->getCustomerLegalName());
        $this->assertSame('Não Agendado', $order->getStatusLabel());
        $this->assertSame('Carla', $order->getContractorName());
        $this->assertSame('Marina', $order->getOrganizerName());
        $this->assertSame('11988887777', $order->getOrganizerPhone());
        $this->assertSame('marina@tests.local', $order->getOrganizerEmail());
        $this->assertSame('Remoto', $order->getOsType());
        $this->assertSame('Estúdio', $order->getLocation());
        $this->assertSame('Sala 2', $order->getLocationDetails());
        $this->assertSame('2026-06-01 10:00:00', $order->getStartTime());
        $this->assertSame('2026-06-01 12:30:00', $order->getEndTime());
        $this->assertSame('2 horas e 30 minutos', $order->getDurationLabel());
        $this->assertSame(25, $order->getNumberParticipants());
        $this->assertSame('Falar devagar', $order->getCommunicationNotes());
        $this->assertSame('Duas câmeras', $order->getProductionNotes());
        $this->assertSame('Zoom', $order->getPlatformDestination());
        $this->assertSame('Vimeo', $order->getVideoEngine());
        $this->assertSame('Portal Alfa', $order->getClientPlatformName());
        $this->assertSame('https://zoom.example/room', $order->getPlatformLink());
        $this->assertSame('https://files.example/brief', $order->getFileLink());
        $this->assertNotNull($order->getCreatedAt());
    }

    public function test_duration_label_uses_days_or_falls_back_to_stored_duration(): void
    {
        $graph = $this->graph();
        $this->createJob(
            $graph['customer'],
            $graph['product'],
            $graph['contractor'],
            $graph['user'],
            'TST1C_0001',
            'Launch Alfa',
            2
        );

        $spanningDays = Order::query()->where('order_code', 'TST1C_0001_01')->first();
        $storedOnly = Order::query()->where('order_code', 'TST1C_0001_02')->first();
        $this->assertNotNull($spanningDays);
        $this->assertNotNull($storedOnly);

        $spanningDays->update([
            'start_time' => '2026-06-01 10:00:00',
            'end_time' => '2026-06-02 12:00:00',
            'duration' => 15,
        ]);
        $storedOnly->update([
            'start_time' => null,
            'end_time' => null,
            'duration' => 90,
        ]);

        $days = $this->repository->findById((string) $spanningDays->id);
        $stored = $this->repository->findById((string) $storedOnly->id);
        $this->assertNotNull($days);
        $this->assertNotNull($stored);
        $this->assertSame('1 dia e 2 horas', $days->getDurationLabel());
        $this->assertSame('90', $stored->getDurationLabel());

        $storedOnly->update([
            'duration' => null,
            'end_time' => '2026-06-01 09:00:00',
            'start_time' => '2026-06-01 10:00:00',
        ]);
        $inverted = $this->repository->findById((string) $storedOnly->id);
        $this->assertNotNull($inverted);
        $this->assertSame('—', $inverted->getDurationLabel());
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $this->assertNull($this->repository->findById(''));
        $this->assertNull($this->repository->findById('11111111-1111-1111-1111-111111111111'));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function onlyCode(array $filters): string
    {
        $result = $this->repository->findAllPaginated($filters);
        $this->assertCount(1, $result['data']);

        return $result['data'][0]->getOsCode();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function onlyTitle(array $filters): string
    {
        $result = $this->repository->findAllPaginated($filters);
        $this->assertCount(1, $result['data']);
        $title = $result['data'][0]->getEventTitle();
        $this->assertNotNull($title);

        return $title;
    }

    /**
     * @return array{customer: Customer, product: Product, contractor: Contact, user: User}
     */
    private function graph(): array
    {
        $this->createJobStatuses();
        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');

        return [
            'customer' => $customer,
            'product' => $this->createJobProduct('Live'),
            'contractor' => $this->createJobContractor($customer, 'Carla'),
            'user' => $this->createJobUser(),
        ];
    }

    private function createJob(
        Customer $customer,
        Product $product,
        Contact $contractor,
        User $user,
        string $code,
        string $title,
        int $osQuantity,
    ): JobEntity {
        $customerCode = trim((string) $customer->code_customer);

        return (new EloquentJobRepository)->create(new JobEntity(
            customerId: (string) $customer->id,
            productId: (string) $product->id,
            jobCode: JobCode::fromString($code, $customerCode),
            jobTitle: $title,
            contractorId: (string) $contractor->id,
            briefingUrl: BriefingUrl::fromString('https://briefing.example.com/job'),
            createdBy: (int) $user->id,
            osQuantity: $osQuantity,
        ));
    }
}
