<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\DTO\ServiceUnityOutputDto;
use App\UseCase\UseCaseInterface;

class ListServiceUnity implements UseCaseInterface
{
    public function __construct(
        private ServiceUnityRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];
        $inputParam = $this->resolveParam($input);
        $paginated = $this->paginated($inputParam);
        $rows = $this->iterator($paginated);
        return $this->toOutput($paginated, $rows, $input);
    }

    private function resolveParam(mixed $input): array
    {
        return match(is_array($input)) {
            true => $input,
            false => []
        };
    }

    private function paginated(array $input): array
    {
        return $this
            ->repository
            ->findAllPaginated($input);
    }

    private function iterator(array $paginated): array
    {
        $serviceUnities = array_map(
            fn ($entity) => new ServiceUnityOutputDto(
                id: (string) $entity->getId(),
                unity: $entity->getUnity(),
                createdBy: $entity->getCreator(),
                updatedBy: $entity->getEditor(),
                createdAt: $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
                updatedAt: $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
            ),
            $paginated['data']
        );

        return $serviceUnities;
    }

    private function toOutput(
        array $paginated,
        array $rows,
        array $input): array {

        return [
            'data' => $rows,
            'meta' => $paginated['meta'],
            'filters' => [
                'unity'        => $input['unity'] ?? null,
                'created_at'   => $input['created_at'] ?? null,
                'updated_at'   => $input['updated_at'] ?? null,
                'search'       => $input['search'] ?? null,
                'sortField'    => $input['sortField'] ?? 'unity',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }

}
