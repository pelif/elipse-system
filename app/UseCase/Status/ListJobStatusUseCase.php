<?php

namespace App\UseCase\Status;

use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\DTO\StatusOutputDto;
use App\UseCase\UseCaseInterface;

class ListJobStatusUseCase implements UseCaseInterface
{
    public function __construct(
        private StatusRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllByType($input);

        $statuses = array_map(
            fn($status) => new StatusOutputDto(
                id: (string) $status->getId(),
                status: $status->getStatus(),
                type: $status->getType()->value,
                observacoes: $status->getObservacoes()
            ),
            $paginated['data']
        );

        return [
            'data' => $statuses,
            'meta' => $paginated['meta'],
            'filters' => [
                'id' => $input['id'] ?? null,
                'status' => $input['status'] ?? null,
                'type' => $input['type'] ?? null,
                'observacoes' => $input['observacoes'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'id',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}

