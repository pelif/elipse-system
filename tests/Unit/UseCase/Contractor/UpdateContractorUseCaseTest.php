<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Contractor;

use App\Domain\Contractor\Entities\Contractor;
use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\DTO\UpdateContractorInputDto;
use App\UseCase\Contractor\UpdateContractorUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateContractorUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $useCase = new UpdateContractorUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateContractorInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            name: 'Novo',
            phone: '1177776666',
            email: 'e@test.local',
            customerId: '22222222-2222-2222-2222-222222222222',
        );

        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateContractorUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateContractorInputDto(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            name: 'Atualizado',
            phone: '1144445555',
            email: 'e@test.local',
            customerId: '33333333-3333-3333-3333-333333333333',
            communicationCare: 'Up care',
        );

        $updated = new Contractor(
            id: $input->id,
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            typeContact: 'Contractor',
            customerName: 'Cliente Atual',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn($updated);

        $useCase = new UpdateContractorUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizado', $output->name);
        $this->assertSame('Up care', $output->communicationCare);
        $this->assertSame('Cliente Atual', $output->customerName);
    }
}
