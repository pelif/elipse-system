<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\ListThirdPartiesUseCase;
use Mockery;
use Tests\TestCase;

class ListThirdPartiesUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '11222333000181';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_filters_and_defaults(): void
    {
        $party = new ThirdParty(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            name: 'Lista Terceiros',
            alias: 'LT',
            phone: '1',
            phoneAlternative: null,
            email: 'list@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: 'Co',
            document: Document::fromString(self::VALID_CNPJ),
            active: true,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $filters = [
            'name' => 'Terceiros',
            'company_name' => 'Co Name',
            'page' => 1,
            'alias' => 'x',
            'email' => 'e@',
            'emailAlternative' => 'ea@',
            'indication' => 'Ind',
            'observation' => 'Obs',
            'document' => self::VALID_CNPJ,
            'active' => true,
            'search' => 'glob',
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
        ];

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$party],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListThirdPartiesUseCase($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista Terceiros', $out['data'][0]->name);
        $this->assertSame([], $out['data'][0]->serviceIds);
        $this->assertSame(0, $out['data'][0]->jobsCount);
        $this->assertNull($out['data'][0]->lastJobCode);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Terceiros', $out['filters']['name']);
        $this->assertSame('Co Name', $out['filters']['companyName']);
        $this->assertSame('ea@', $out['filters']['emailAlternative']);
        $this->assertSame(self::VALID_CNPJ, $out['filters']['document']);
        $this->assertSame('created_at', $out['filters']['sortField']);
        $this->assertSame('desc', $out['filters']['sortDirection']);
        $this->assertSame(10, $out['meta']['per_page']);
    }

    public function test_passes_empty_array_when_execute_without_args(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with([])->andReturn([
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 0,
                'links' => [],
            ],
        ]);

        $useCase = new ListThirdPartiesUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame([], $out['data']);
    }
}
