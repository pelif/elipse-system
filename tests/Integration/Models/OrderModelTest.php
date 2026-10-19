<?php

declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Models\Job as JobModel;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    public function test_order_table_has_production_fields(): void
    {
        $this->assertTrue(Schema::hasColumns('order', [
            'number_participants',
            'communication_notes',
            'production_notes',
            'platform_destination',
            'video_engine',
            'client_platform_name',
            'platform_link',
            'file_link',
        ]));
    }

    public function test_order_persists_production_fields_and_relations(): void
    {
        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');
        $product = $this->createJobProduct('Live');
        $contractor = $this->createJobContractor($customer, 'Carla');
        $organizer = $this->createJobOrganizer($customer, 'Marina');
        $statuses = $this->createJobStatuses();
        $user = $this->createJobUser();

        $job = JobModel::query()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'contractor_id' => $contractor->id,
            'job_code' => 'TST1C_0001',
            'job_title' => 'Lançamento',
            'status_id' => $statuses['job']->id,
            'os_quantity' => 1,
            'created_by' => $user->id,
        ]);

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'contact_id' => $contractor->id,
            'organizer_id' => $organizer->id,
            'status_id' => $statuses['order']->id,
            'job_id' => $job->id,
            'order_code' => 'TST1C_0001_01',
            'event_title' => 'Lançamento',
            'number_participants' => 12,
            'communication_notes' => 'Falar com o RH',
            'production_notes' => 'Duas câmeras',
            'platform_destination' => 'Zoom',
            'video_engine' => 'Zoom',
            'client_platform_name' => 'Portal Cliente',
            'platform_link' => 'https://zoom.example/sala',
            'file_link' => 'https://files.example/os',
            'created_by' => $user->id,
        ]);

        $fresh = $order->fresh(['customer', 'organizer', 'status', 'contact', 'job']);

        $this->assertNotNull($fresh);
        $this->assertSame(12, $fresh->number_participants);
        $this->assertSame('Falar com o RH', $fresh->communication_notes);
        $this->assertSame('Duas câmeras', $fresh->production_notes);
        $this->assertSame('Zoom', $fresh->platform_destination);
        $this->assertSame('Zoom', $fresh->video_engine);
        $this->assertSame('Portal Cliente', $fresh->client_platform_name);
        $this->assertSame('https://zoom.example/sala', $fresh->platform_link);
        $this->assertSame('https://files.example/os', $fresh->file_link);
        $this->assertSame($customer->id, $fresh->customer->id);
        $this->assertSame($organizer->id, $fresh->organizer->id);
        $this->assertSame($statuses['order']->id, $fresh->status->id);
        $this->assertSame($contractor->id, $fresh->contact->id);
        $this->assertSame($job->id, $fresh->job->id);
    }

    public function test_number_participants_defaults_to_zero(): void
    {
        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');
        $statuses = $this->createJobStatuses();

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'status_id' => $statuses['order']->id,
            'order_code' => 'TST1C_0001_01',
            'event_title' => 'Sem participantes',
        ]);

        $this->assertSame(0, $order->fresh()->number_participants);
    }
}
