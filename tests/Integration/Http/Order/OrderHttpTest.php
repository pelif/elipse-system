<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class OrderHttpTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    public function test_listing_returns_orders_created_with_the_job_newest_first(): void
    {
        $graph = $this->context();
        $this->createJobOrganizer($graph['customer'], 'Marina');

        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 2, 'Launch Alfa'))
            ->assertRedirect(route('jobs.index'));

        Order::query()->where('order_code', 'TST1C_0001_01')->update([
            'created_at' => '2026-01-01 08:00:00',
        ]);
        Order::query()->where('order_code', 'TST1C_0001_02')->update([
            'created_at' => '2026-06-01 08:00:00',
        ]);

        $this->actingAs($graph['user'])
            ->get(route('orders.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Order/Index')
                ->has('orders.data', 2)
                ->where('orders.data.0.osCode', 'TST1C_0001_02')
                ->where('orders.data.1.osCode', 'TST1C_0001_01')
                ->where('orders.data.0.eventTitle', 'Launch Alfa')
                ->where('orders.data.0.jobCode', 'TST1C_0001')
                ->where('orders.data.0.customerLegalName', 'Cliente Alfa LTDA')
                ->where('orders.data.0.contractorName', 'Carla')
                ->where('orders.data.0.statusLabel', 'Não Agendado')
                ->where('orderStatuses.0.name', 'Não Agendado')
            );
    }

    public function test_listing_includes_orders_without_order_code(): void
    {
        $graph = $this->context();

        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 1, 'Launch Alfa'))
            ->assertRedirect(route('jobs.index'));

        Order::query()->where('order_code', 'TST1C_0001_01')->update([
            'order_code' => null,
        ]);

        $this->actingAs($graph['user'])
            ->get(route('orders.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Order/Index')
                ->has('orders.data', 1)
                ->where('orders.data.0.osCode', null)
                ->where('orders.data.0.eventTitle', 'Launch Alfa')
            );
    }

    public function test_listing_filters_by_order_code(): void
    {
        $graph = $this->context();

        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 2, 'Launch Alfa'));

        $this->actingAs($graph['user'])
            ->get(route('orders.index', ['order_code' => 'TST1C_0001_02']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('orders.data', 1)
                ->where('orders.data.0.osCode', 'TST1C_0001_02')
                ->where('orders.filters.osCode', 'TST1C_0001_02')
            );
    }

    public function test_edit_page_returns_order_detail_and_job_organizers(): void
    {
        $graph = $this->context();
        $marina = $this->createJobOrganizer($graph['customer'], 'Marina');

        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 1, 'Launch Alfa'));

        $order = Order::query()->where('order_code', 'TST1C_0001_01')->first();
        $this->assertNotNull($order);
        $order->update([
            'organizer_id' => $marina->id,
            'os_type' => 'Remoto',
            'start_time' => '2026-06-01 10:00:00',
            'end_time' => '2026-06-01 12:00:00',
            'location' => 'Estúdio',
            'number_participants' => 12,
            'platform_destination' => 'Zoom',
            'platform_link' => 'https://zoom.example/room',
            'communication_notes' => 'Falar devagar',
            'production_notes' => 'Duas câmeras',
            'file_link' => 'https://files.example/brief',
        ]);

        $this->actingAs($graph['user'])
            ->get(route('orders.edit', ['id' => $order->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Order/EditOs')
                ->where('order.osCode', 'TST1C_0001_01')
                ->where('order.jobCode', 'TST1C_0001')
                ->where('order.eventTitle', 'Launch Alfa')
                ->where('order.customerLegalName', 'Cliente Alfa LTDA')
                ->where('order.contractorName', 'Carla')
                ->where('order.organizerName', 'Marina')
                ->where('order.osType', 'Remoto')
                ->where('order.location', 'Estúdio')
                ->where('order.durationLabel', '2 horas')
                ->where('order.platformDestination', 'Zoom')
                ->where('order.numberParticipants', 12)
                ->has('jobOrganizers', 1)
                ->where('jobOrganizers.0.name', 'Marina')
                ->where('jobOrganizers.0.email', 'marina@tests.local')
            );
    }

    public function test_edit_returns_not_found_and_rejects_invalid_id(): void
    {
        $graph = $this->context();

        $this->actingAs($graph['user'])
            ->get(route('orders.edit', ['id' => '11111111-1111-1111-1111-111111111111']))
            ->assertNotFound();

        $this->actingAs($graph['user'])
            ->from(route('orders.index'))
            ->get(route('orders.edit', ['id' => 'nao-e-uuid']))
            ->assertRedirect(route('orders.index'))
            ->assertSessionHasErrors('id');
    }

    public function test_allowed_roles_can_list_and_open_and_others_cannot(): void
    {
        $graph = $this->context();
        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 1, 'Launch Alfa'));

        $order = Order::query()->where('order_code', 'TST1C_0001_01')->first();
        $this->assertNotNull($order);

        foreach (['admin', 'commercial', 'production', 'finance'] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'name' => 'User '.$role,
            ]);

            $this->actingAs($user)
                ->get(route('orders.index'))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page->component('Order/Index'));

            $this->actingAs($user)
                ->get(route('orders.edit', ['id' => $order->id]))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page->component('Order/EditOs'));
        }

        auth()->logout();

        $this->get(route('orders.index'))->assertRedirect(route('login'));
        $this->get(route('orders.edit', ['id' => $order->id]))->assertRedirect(route('login'));

        $blocked = User::factory()->create([
            'role' => 'viewer',
            'name' => 'Sem acesso',
        ]);

        $this->actingAs($blocked)->get(route('orders.index'))->assertForbidden();
        $this->actingAs($blocked)->get(route('orders.edit', ['id' => $order->id]))->assertForbidden();
    }

    /**
     * @param  array{customer: \App\Models\Customer, product: \App\Models\Product, contractor: \App\Models\Contact, user: \App\Models\User}  $graph
     * @return array<string, mixed>
     */
    private function payload(array $graph, string $code, int $osQuantity, string $title): array
    {
        return [
            'customer_id' => (string) $graph['customer']->id,
            'contractor_id' => (string) $graph['contractor']->id,
            'job_code' => $code,
            'job_title' => $title,
            'product_id' => (string) $graph['product']->id,
            'os_quantity' => $osQuantity,
            'briefing_url' => 'https://briefing.example.com/job',
        ];
    }

    /**
     * @return array{customer: \App\Models\Customer, product: \App\Models\Product, contractor: \App\Models\Contact, user: \App\Models\User}
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
}
