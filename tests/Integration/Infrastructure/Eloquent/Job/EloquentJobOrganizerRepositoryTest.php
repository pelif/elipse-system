<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\Job;

use App\Infrastructure\Eloquent\Job\EloquentJobOrganizerRepository;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Job as JobModel;
use App\Models\JobOrganizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\Support\PreparesJobContext;
use Tests\TestCase;

class EloquentJobOrganizerRepositoryTest extends TestCase
{
    use PreparesJobContext;
    use RefreshDatabase;

    private EloquentJobOrganizerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentJobOrganizerRepository;
    }

    public function test_sync_links_only_organizers_of_the_job_customer(): void
    {
        $graph = $this->graph();
        $job = $this->persistJob($graph['customer'], $graph['product']->id, $graph['contractor']->id);
        $first = $this->createJobOrganizer($graph['customer'], 'Marina');
        $second = $this->createJobOrganizer($graph['customer'], 'Paulo');
        $otherCustomer = $this->createJobCustomer('OTH2C', 'Outro Cliente');
        $foreign = $this->createJobOrganizer($otherCustomer, 'Helena');

        $this->repository->syncOrganizersForJob((string) $job->id, [
            (string) $second->id,
            (string) $first->id,
            (string) $foreign->id,
            (string) $graph['contractor']->id,
            '   ',
        ]);

        $ids = $this->repository->findOrganizerIdsByJobId((string) $job->id);
        $this->assertSame([(string) $first->id, (string) $second->id], $ids);

        $organizers = $this->repository->findOrganizersByJobId((string) $job->id);
        $this->assertCount(2, $organizers);
        $this->assertSame('Marina', $organizers[0]->getName());
        $this->assertSame('Paulo', $organizers[1]->getName());
        $this->assertSame('Cliente Alfa', $organizers[0]->getCustomerName());
        $this->assertSame('Organizer', $organizers[0]->getTypeContact());
        $this->assertSame(2, JobOrganizer::query()->where('job_id', $job->id)->count());
        $this->assertNotNull(JobOrganizer::query()->where('job_id', $job->id)->value('created_at'));
    }

    public function test_sync_replaces_the_previous_set(): void
    {
        $graph = $this->graph();
        $job = $this->persistJob($graph['customer'], $graph['product']->id, $graph['contractor']->id);
        $first = $this->createJobOrganizer($graph['customer'], 'Marina');
        $second = $this->createJobOrganizer($graph['customer'], 'Paulo');

        $this->repository->syncOrganizersForJob((string) $job->id, [(string) $first->id, (string) $second->id]);
        $this->repository->syncOrganizersForJob((string) $job->id, [(string) $second->id]);

        $this->assertSame([(string) $second->id], $this->repository->findOrganizerIdsByJobId((string) $job->id));
    }

    public function test_sync_with_empty_list_clears_links(): void
    {
        $graph = $this->graph();
        $job = $this->persistJob($graph['customer'], $graph['product']->id, $graph['contractor']->id);
        $organizer = $this->createJobOrganizer($graph['customer'], 'Marina');

        $this->repository->syncOrganizersForJob((string) $job->id, [(string) $organizer->id]);
        $this->repository->syncOrganizersForJob((string) $job->id, []);

        $this->assertSame([], $this->repository->findOrganizersByJobId((string) $job->id));
    }

    public function test_find_returns_empty_when_job_id_is_blank(): void
    {
        $this->assertSame([], $this->repository->findOrganizerIdsByJobId('   '));
        $this->assertSame([], $this->repository->findOrganizersByJobId(''));
    }

    public function test_sync_rejects_unknown_job(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->repository->syncOrganizersForJob('11111111-1111-1111-1111-111111111111', []);
    }

    public function test_sync_rejects_blank_job_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->repository->syncOrganizersForJob('   ', []);
    }

    /**
     * @return array{customer: Customer, product: \App\Models\Product, contractor: Contact}
     */
    private function graph(): array
    {
        $customer = $this->createJobCustomer('TST1C', 'Cliente Alfa');

        return [
            'customer' => $customer,
            'product' => $this->createJobProduct('Live'),
            'contractor' => $this->createJobContractor($customer, 'Carla'),
        ];
    }

    private function persistJob(Customer $customer, string $productId, string $contractorId): JobModel
    {
        return JobModel::query()->create([
            'customer_id' => $customer->id,
            'product_id' => $productId,
            'contractor_id' => $contractorId,
            'job_code' => 'TST1C_0099',
            'job_title' => 'Job da pivot',
            'os_quantity' => 0,
        ]);
    }
}
