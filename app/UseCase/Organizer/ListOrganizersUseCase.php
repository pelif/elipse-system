<?php

namespace App\UseCase\Organizer;

use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\DTO\OrganizerOutputDto;
use App\UseCase\UseCaseInterface;

class ListOrganizersUseCase implements UseCaseInterface
{
    public function __construct(
        private OrganizerRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $organizers = array_map(
            static fn ($organizer) => OrganizerOutputDto::fromEntity($organizer),
            $paginated['data']
        );

        return [
            'data' => $organizers,
            'meta' => $paginated['meta'],
            'filters' => [
                'name' => $input['name'] ?? null,
                'phone' => $input['phone'] ?? null,
                'email' => $input['email'] ?? null,
                'customer' => $input['customer'] ?? null,
                'customerName' => $input['customerName'] ?? null,
                'company_name' => $input['company_name'] ?? null,
                'communicationCare' => $input['communicationCare'] ?? $input['communication_care'] ?? null,
                'communication_care' => $input['communication_care'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'name',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}
