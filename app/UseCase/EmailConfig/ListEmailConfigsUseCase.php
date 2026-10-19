<?php

namespace App\UseCase\EmailConfig;

use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\UseCase\EmailConfig\DTO\EmailConfigOutputDto;
use App\UseCase\UseCaseInterface;

class ListEmailConfigsUseCase implements UseCaseInterface
{
    public function __construct(
        private EmailConfigRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAll($input);

        $items = array_map(
            fn($emailConfig) => new EmailConfigOutputDto(
                id: (string) $emailConfig->getId(),
                address: $emailConfig->getEmail(),
                departmentId: $emailConfig->getDepartmentId()
            ),
            $paginated['data']
        );

        return [
            'data' => $items,
            'meta' => $paginated['meta'],
            'filters' => $paginated['filters'] ?? [
                'address' => $input['address'] ?? null,
                'departmentId' => $input['departmentId'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'address',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}

