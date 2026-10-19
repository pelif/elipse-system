<?php

namespace Tests\Integration\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\ComissionedAgent\CreateComissionedAgentUseCase;
use App\UseCase\ComissionedAgent\DeleteComissionedAgentUseCase;
use App\UseCase\ComissionedAgent\DTO\CreateComissionedAgentInputDto;
use App\UseCase\ComissionedAgent\DTO\UpdateComissionedAgentInputDto;
use App\UseCase\ComissionedAgent\GetComissionedAgentUseCase;
use App\UseCase\ComissionedAgent\ListComissionedAgentsUseCase;
use App\UseCase\ComissionedAgent\UpdateComissionedAgentUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComissionedAgentUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private ComissionedAgentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ComissionedAgentRepositoryInterface::class);
    }

    public function test_create_use_case_persists_and_returns_output(): void
    {
        $input = new CreateComissionedAgentInputDto(
            name: 'Carlos Alberto',
            defaultComission: 12.5,
            paymentDays: 30,
            observations: 'Obs',
        );

        $output = (new CreateComissionedAgentUseCase($this->repository))->execute($input);

        $this->assertNotEmpty($output->id);
        $this->assertSame('Carlos Alberto', $output->name);
        $this->assertSame(12.5, $output->defaultComission);
        $this->assertSame(30, $output->paymentDays);
        $this->assertSame('Obs', $output->observations);
    }

    public function test_create_use_case_throws_for_invalid_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CreateComissionedAgentUseCase($this->repository))->execute('invalid');
    }

    public function test_get_use_case_returns_output_for_existing_id(): void
    {
        $input = new CreateComissionedAgentInputDto(name: 'Fernanda Lima', defaultComission: 5.0, paymentDays: 15);
        $created = (new CreateComissionedAgentUseCase($this->repository))->execute($input);

        $output = (new GetComissionedAgentUseCase($this->repository))->execute($created->id);

        $this->assertNotNull($output);
        $this->assertSame($created->id, $output->id);
        $this->assertSame('Fernanda Lima', $output->name);
    }

    public function test_get_use_case_returns_null_for_missing_id(): void
    {
        $output = (new GetComissionedAgentUseCase($this->repository))->execute('00000000-0000-0000-0000-000000000000');
        $this->assertNull($output);
    }

    public function test_update_use_case_persists_changes(): void
    {
        $input = new CreateComissionedAgentInputDto(name: 'Carlos Alberto', defaultComission: 5.0, paymentDays: 10);
        $created = (new CreateComissionedAgentUseCase($this->repository))->execute($input);

        $updateInput = new UpdateComissionedAgentInputDto(
            id: $created->id,
            name: 'Carlos Atualizado',
            defaultComission: 25.0,
            paymentDays: 60,
            observations: 'Atualizado',
        );

        $output = (new UpdateComissionedAgentUseCase($this->repository))->execute($updateInput);

        $this->assertNotNull($output);
        $this->assertSame('Carlos Atualizado', $output->name);
        $this->assertSame(25.0, $output->defaultComission);
        $this->assertSame(60, $output->paymentDays);
        $this->assertSame('Atualizado', $output->observations);
    }

    public function test_delete_use_case_removes_record(): void
    {
        $input = new CreateComissionedAgentInputDto(name: 'Carlos Alberto', defaultComission: 5.0, paymentDays: 10);
        $created = (new CreateComissionedAgentUseCase($this->repository))->execute($input);

        $result = (new DeleteComissionedAgentUseCase($this->repository))->execute($created->id);

        $this->assertTrue($result);
        $this->assertNull((new GetComissionedAgentUseCase($this->repository))->execute($created->id));
    }

    public function test_list_use_case_returns_paginated_data(): void
    {
        $input1 = new CreateComissionedAgentInputDto(name: 'Carlos Alberto', defaultComission: 5.0, paymentDays: 10);
        $input2 = new CreateComissionedAgentInputDto(name: 'Fernanda Lima', defaultComission: 8.0, paymentDays: 20);
        (new CreateComissionedAgentUseCase($this->repository))->execute($input1);
        (new CreateComissionedAgentUseCase($this->repository))->execute($input2);

        $result = (new ListComissionedAgentsUseCase($this->repository))->execute([]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertCount(2, $result['data']);
        $this->assertSame(10, $result['meta']['per_page']);
    }
}
