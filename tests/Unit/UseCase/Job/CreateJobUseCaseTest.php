<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Job;

use App\Domain\Job\Entities\Job as JobEntity;
use App\Domain\Job\Exceptions\JobInitialStatusNotFoundException;
use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\Domain\Job\ValueObjects\JobCode;
use App\Domain\Status\Enums\StatusType;
use App\UseCase\Job\CreateJobUseCase;
use App\UseCase\Job\DTO\CreateJobInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateJobUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository->shouldReceive('create')->never();

        $this->expectException(InvalidArgumentException::class);

        (new CreateJobUseCase($repository))->execute(null);
    }

    public function test_rejects_job_code_that_does_not_match_customer_code(): void
    {
        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository->shouldReceive('create')->never();

        $this->expectException(InvalidArgumentException::class);

        (new CreateJobUseCase($repository))->execute($this->input(jobCode: 'OTH2C_0001'));
    }

    public function test_rejects_invalid_briefing_url(): void
    {
        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository->shouldReceive('create')->never();

        $this->expectException(InvalidArgumentException::class);

        (new CreateJobUseCase($repository))->execute($this->input(briefingUrl: 'ftp://files.example.com/a.pdf'));
    }

    public function test_builds_entity_with_initial_status_and_maps_output(): void
    {
        $input = $this->input(
            osQuantity: 2,
            briefingUrl: 'https://briefing.example.com/job',
            targetBudget: '1500,5',
            commissionedPercentage: '10,5',
            invoiceDueDate: '01/04/2026',
            createdBy: 9,
        );

        $created = new JobEntity(
            customerId: $input->customerId,
            productId: $input->productId,
            jobCode: JobCode::fromString($input->jobCode, $input->customerCode),
            jobTitle: $input->jobTitle,
            contractorId: $input->contractorId,
            id: '11111111-1111-1111-1111-111111111111',
            createdBy: 9,
        );

        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input) {
                if (! $entity instanceof JobEntity) {
                    return false;
                }

                $status = $entity->getStatus();
                $briefing = $entity->getBriefingUrl();

                return $entity->getId() === null
                    && $entity->getJobCode()->getValue() === 'TST1C_0001'
                    && $entity->getCreatedBy() === 9
                    && $entity->getOsQuantity() === 2
                    && $entity->getTargetBudget() === '1500.50'
                    && $entity->getCommissionedPercentage() === '10.5000'
                    && $entity->getInvoiceDueDate() === '2026-04-01 00:00:00'
                    && $entity->getAdminExpensesPercentage() === '0.0000'
                    && $entity->getFinancialExpensesPercentage() === '0.0000'
                    && $entity->getApprovalDate() === null
                    && $entity->getCommissionValue() === null
                    && $briefing !== null
                    && $briefing->getValue() === 'https://briefing.example.com/job'
                    && $status !== null
                    && $status->getStatus() === 'Em análise'
                    && $status->getType() === StatusType::JOB
                    && $entity->getCustomerId() === $input->customerId;
            }))
            ->andReturn($created);

        $output = (new CreateJobUseCase($repository))->execute($input);

        $this->assertSame('11111111-1111-1111-1111-111111111111', $output->id);
        $this->assertSame('Lançamento', $output->jobTitle);
        $this->assertSame('TST1C_0001', $output->jobCode);
    }

    public function test_propagates_missing_initial_status(): void
    {
        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->andThrow(JobInitialStatusNotFoundException::forMissing(true, true));

        $this->expectException(JobInitialStatusNotFoundException::class);

        (new CreateJobUseCase($repository))->execute($this->input());
    }

    private function input(
        string $jobCode = 'TST1C_0001',
        int $osQuantity = 0,
        ?string $briefingUrl = null,
        ?string $targetBudget = null,
        ?string $commissionedPercentage = null,
        ?string $invoiceDueDate = null,
        ?int $createdBy = null,
    ): CreateJobInputDto {
        return new CreateJobInputDto(
            customerId: '22222222-2222-2222-2222-222222222222',
            customerCode: 'TST1C',
            contractorId: '66666666-6666-6666-6666-666666666666',
            jobCode: $jobCode,
            jobTitle: 'Lançamento',
            productId: '33333333-3333-3333-3333-333333333333',
            osQuantity: $osQuantity,
            briefingUrl: $briefingUrl,
            targetBudget: $targetBudget,
            commissionedPercentage: $commissionedPercentage,
            invoiceDueDate: $invoiceDueDate,
            createdBy: $createdBy,
        );
    }
}
