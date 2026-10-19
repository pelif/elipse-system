<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Order;

use App\Domain\Job\Repositories\JobOrganizerRepositoryInterface;
use App\Domain\Order\Entities\Order;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Organizer\Entities\Organizer;
use App\UseCase\Order\GetOrderUseCase;
use App\UseCase\Order\ListJobOrganizersUseCase;
use App\UseCase\Order\ListOrdersUseCase;
use Mockery;
use Tests\TestCase;

class OrderUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_list_orders_maps_items_and_normalizes_filters(): void
    {
        $order = $this->order();
        $filters = [
            'order_code' => 'TST1C_0001_01',
            'job_code' => 'TST1C_0001',
            'event_title' => 'Launch',
            'customer' => 'Alfa',
            'organizer' => 'Marina',
            'page' => 1,
        ];

        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$order],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $output = (new ListOrdersUseCase($repository))->execute($filters);

        $this->assertCount(1, $output['data']);
        $this->assertSame('TST1C_0001_01', $output['data'][0]->osCode);
        $this->assertSame('Launch Alfa', $output['data'][0]->eventTitle);
        $this->assertSame('2 horas', $output['data'][0]->durationLabel);
        $this->assertSame('TST1C_0001_01', $output['filters']['osCode']);
        $this->assertSame('TST1C_0001', $output['filters']['jobCode']);
        $this->assertSame('Launch', $output['filters']['eventTitle']);
        $this->assertSame('Alfa', $output['filters']['customer']);
        $this->assertSame('Marina', $output['filters']['organizer']);
        $this->assertSame('created_at', $output['filters']['sortField']);
        $this->assertSame('desc', $output['filters']['sortDirection']);
    }

    public function test_get_order_returns_detail_or_null(): void
    {
        $order = $this->order();
        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('11111111-1111-1111-1111-111111111111')->andReturn($order);
        $repository->shouldReceive('findById')->once()->with('22222222-2222-2222-2222-222222222222')->andReturn(null);

        $useCase = new GetOrderUseCase($repository);

        $found = $useCase->execute('11111111-1111-1111-1111-111111111111');
        $this->assertNotNull($found);
        $this->assertSame('Zoom', $found->platformDestination);
        $this->assertSame(12, $found->numberParticipants);
        $this->assertSame('Marina', $found->organizerName);
        $this->assertNull($useCase->execute('22222222-2222-2222-2222-222222222222'));
        $this->assertNull($useCase->execute(''));
    }

    public function test_list_job_organizers_requires_job_and_maps_options(): void
    {
        $organizer = new Organizer(
            id: '33333333-3333-3333-3333-333333333333',
            customerId: '44444444-4444-4444-4444-444444444444',
            name: 'Marina',
            phone: '11988887777',
            email: 'marina@tests.local',
            communicationCare: null,
            observations: null,
            typeContact: 'Organizer',
            customerName: 'Cliente Alfa',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(JobOrganizerRepositoryInterface::class);
        $repository->shouldReceive('findOrganizersByJobId')
            ->once()
            ->with('55555555-5555-5555-5555-555555555555')
            ->andReturn([$organizer]);

        $useCase = new ListJobOrganizersUseCase($repository);
        $options = $useCase->execute(['job_id' => '55555555-5555-5555-5555-555555555555']);

        $this->assertCount(1, $options);
        $this->assertSame('Marina', $options[0]->name);
        $this->assertSame('11988887777', $options[0]->phone);

        $this->expectException(\InvalidArgumentException::class);
        $useCase->execute(['job_id' => ' ']);
    }

    private function order(): Order
    {
        return new Order(
            id: '11111111-1111-1111-1111-111111111111',
            osCode: 'TST1C_0001_01',
            customerId: '44444444-4444-4444-4444-444444444444',
            organizerId: '33333333-3333-3333-3333-333333333333',
            statusId: '66666666-6666-6666-6666-666666666666',
            jobId: '55555555-5555-5555-5555-555555555555',
            contactId: '77777777-7777-7777-7777-777777777777',
            eventTitle: 'Launch Alfa',
            osType: 'Remoto',
            startTime: '2026-06-01 10:00:00',
            endTime: '2026-06-01 12:00:00',
            duration: 120,
            location: 'Estúdio',
            numberParticipants: 12,
            platformDestination: 'Zoom',
            customerLegalName: 'Cliente Alfa LTDA',
            jobCode: 'TST1C_0001',
            statusLabel: 'Não Agendado',
            organizerName: 'Marina',
            organizerPhone: '11988887777',
            organizerEmail: 'marina@tests.local',
            contractorName: 'Carla',
            durationLabel: '2 horas',
        );
    }
}
