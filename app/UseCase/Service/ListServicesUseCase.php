<?php

namespace App\UseCase\Service;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\DTO\ServiceOutputDto;
use App\UseCase\UseCaseInterface;

class ListServicesUseCase implements UseCaseInterface
{
    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $services = array_map(
            fn ($service) => new ServiceOutputDto(
                id: (string) $service->getId(),
                service: $service->getService(),
                description: $service->getDescription(),
                unityId: $service->getUnityId(),
                unity: $service->getUnity(),
                createdAt: $service->getCreatedAt(),
                updatedAt: $service->getUpdatedAt()
            ),
            $paginated['data']
        );

        return [
            'data' => $services,
            'meta' => $paginated['meta'],
            'filters' => [
                'service'       => $input['service'] ?? null,
                'search'        => $input['search'] ?? null,
                'created_at'    => $input['created_at'] ?? null,
                'updated_at'    => $input['updated_at'] ?? null,
                'sortField'     => $input['sortField'] ?? 'service',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}
