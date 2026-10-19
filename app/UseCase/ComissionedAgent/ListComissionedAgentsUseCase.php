<?php

namespace App\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\ComissionedAgent\DTO\ComissionedAgentOutputDto;
use App\UseCase\UseCaseInterface;

class ListComissionedAgentsUseCase implements UseCaseInterface
{
    public function __construct(
        private ComissionedAgentRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $items = array_map(
            fn ($entity) => new ComissionedAgentOutputDto(
                id: (string) $entity->getId(),
                name: $entity->getName(),
                defaultComission: $entity->getDefaultComission(),
                paymentDays: $entity->getPaymentDays(),
                observations: $entity->getObservations(),
                active: $entity->isActive(),
                createdBy: $entity->getCreatedBy(),
                updatedBy: $entity->getUpdatedBy(),
                createdAt: $entity->getCreatedAt(),
                updatedAt: $entity->getUpdatedAt(),
            ),
            $paginated['data']
        );

        return [
            'data'    => $items,
            'meta'    => $paginated['meta'],
            'filters' => [
                'name'             => $input['name'] ?? null,
                'defaultComission' => $input['defaultComission'] ?? null,
                'paymentDays'      => $input['paymentDays'] ?? null,
                'sortField'        => $input['sortField'] ?? 'createdAt',
                'sortDirection'    => $input['sortDirection'] ?? 'desc',
            ],
        ];
    }
}
