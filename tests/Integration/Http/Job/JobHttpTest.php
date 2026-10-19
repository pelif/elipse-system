<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Job;

use App\Models\Job as JobModel;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class JobHttpTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    public function test_commercial_creates_job_without_orders_and_lists_it(): void
    {
        $graph = $this->context();

        $this->actingAs($graph['user'])
            ->get(route('jobs.next-code', ['customer_id' => $graph['customer']->id]))
            ->assertOk()
            ->assertJsonPath('job_code', 'TST1C_0001');

        $this->actingAs($graph['user'])
            ->get(route('jobs.create'))
            ->assertOk()
            ->assertSee('Job\/Create', false);

        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 0, 'Launch Alfa'))
            ->assertRedirect(route('jobs.index'));

        $job = JobModel::query()->where('job_code', 'TST1C_0001')->first();
        $this->assertNotNull($job);
        $this->assertSame(0, (int) $job->os_quantity);
        $this->assertSame(0, Order::query()->where('job_id', $job->id)->count());

        $index = $this->actingAs($graph['user'])
            ->get(route('jobs.index', ['job_title' => 'Launch']));

        $index->assertOk()
            ->assertSee('Launch Alfa', false)
            ->assertSee('canCreateJobs', false);

        $page = $index->getContent();
        $hasAnalysisStatus = str_contains($page, 'Em análise') || str_contains($page, 'Em an\u00e1lise');
        $this->assertTrue($hasAnalysisStatus);
    }

    public function test_commercial_creates_job_with_orders_and_contractor_from_json(): void
    {
        $graph = $this->context();

        $created = $this->actingAs($graph['user'])
            ->postJson(route('contractors.store'), [
                'name' => 'Diana Modal',
                'phone' => '11988887777',
                'email' => 'diana.modal@tests.local',
                'customer_id' => $graph['customer']->id,
            ])
            ->assertOk()
            ->assertJsonPath('name', 'Diana Modal');

        $contractorId = $created->json('id');
        $this->assertNotEmpty($contractorId);

        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0002', 3, 'Launch Beta', (string) $contractorId))
            ->assertRedirect(route('jobs.index'));

        $job = JobModel::query()->where('job_code', 'TST1C_0002')->first();
        $this->assertNotNull($job);
        $orderCodes = Order::query()->where('job_id', $job->id)->orderBy('order_code')->pluck('order_code')->all();
        $this->assertSame(['TST1C_0002_01', 'TST1C_0002_02', 'TST1C_0002_03'], $orderCodes);
        $this->assertSame((string) $contractorId, (string) $job->contractor_id);
        $eventTitles = Order::query()->where('job_id', $job->id)->orderBy('order_code')->pluck('event_title')->all();
        $this->assertSame(['Launch Beta', 'Launch Beta', 'Launch Beta'], $eventTitles);
    }

    public function test_non_commercial_can_list_and_cannot_create(): void
    {
        $graph = $this->context();
        $this->actingAs($graph['user'])
            ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0001', 0, 'Launch Alfa'));

        foreach (['admin', 'production', 'finance'] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'name' => 'User '.$role,
            ]);

            $this->actingAs($user)
                ->get(route('jobs.index'))
                ->assertOk()
                ->assertSee('Job\/Index', false)
                ->assertSee('Launch Alfa', false);

            $this->actingAs($user)
                ->get(route('jobs.create'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('jobs.store'), $this->payload($graph, 'TST1C_0009', 0, 'Blocked'))
                ->assertForbidden();
        }
    }

    public function test_autocomplete_limits_results_and_filters_contractors_by_customer(): void
    {
        $graph = $this->context();
        $otherCustomer = $this->createJobCustomer('OTH2C', 'Cliente Beta');
        $this->createJobContractor($otherCustomer, 'Bruno Outro');

        $this->actingAs($graph['user'])
            ->getJson(route('customers.for-select', ['q' => 'Alfa']))
            ->assertOk()
            ->assertJsonFragment(['label' => 'Cliente Alfa'])
            ->assertJsonMissing(['label' => 'Cliente Beta']);

        $this->actingAs($graph['user'])
            ->getJson(route('products.for-select', ['q' => 'Live']))
            ->assertOk()
            ->assertJsonFragment(['label' => 'Live']);

        $contractors = $this->actingAs($graph['user'])
            ->getJson(route('contractors.for-select', [
                'q' => '',
                'customer_id' => $graph['customer']->id,
            ]))
            ->assertOk()
            ->json();

        $labels = array_column($contractors, 'label');
        $this->assertContains('Carla', $labels);
        $this->assertNotContains('Bruno Outro', $labels);

        $this->actingAs($graph['user'])
            ->getJson(route('contractors.for-select', ['q' => 'Carla']))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_store_rejects_invalid_job_code_for_customer(): void
    {
        $graph = $this->context();

        $this->actingAs($graph['user'])
            ->from(route('jobs.create'))
            ->post(route('jobs.store'), $this->payload($graph, 'WRONG_0001', 0, 'Launch Alfa'))
            ->assertRedirect(route('jobs.create'))
            ->assertSessionHasErrors('job_code');
    }

    /**
     * @param  array{customer: \App\Models\Customer, product: \App\Models\Product, contractor: \App\Models\Contact, user: \App\Models\User}  $graph
     * @return array<string, mixed>
     */
    private function payload(array $graph, string $code, int $osQuantity, string $title, ?string $contractorId = null): array
    {
        $contractor = (string) $graph['contractor']->id;
        if ($contractorId !== null) {
            $contractor = $contractorId;
        }

        return [
            'customer_id' => (string) $graph['customer']->id,
            'contractor_id' => $contractor,
            'job_code' => $code,
            'job_title' => $title,
            'product_id' => (string) $graph['product']->id,
            'os_quantity' => $osQuantity,
            'briefing_url' => 'https://briefing.example.com/job',
            'target_budget' => '1.500,50',
            'purchase_order' => 'PC-1',
            'additional_info' => 'Complemento',
            'invoice_due_date' => '01/06/2026',
            'commissioned_percentage' => '10,5',
            'commission_payment_due_date' => '02/05/2026',
        ];
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
}
