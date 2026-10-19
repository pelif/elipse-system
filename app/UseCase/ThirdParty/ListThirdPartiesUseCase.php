<?php

namespace App\UseCase\ThirdParty;

use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\DTO\ThirdPartyOutputDto;
use App\UseCase\UseCaseInterface;

class ListThirdPartiesUseCase implements UseCaseInterface
{
    public function __construct(
        private ThirdPartyRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): array
    {
        $input = $input ?? [];

        $paginated = $this->repository->findAllPaginated(is_array($input) ? $input : []);

        $thirdParties = array_map(
            static fn ($thirdParty) => ThirdPartyOutputDto::fromEntity($thirdParty),
            $paginated['data']
        );

        return [
            'data' => $thirdParties,
            'meta' => $paginated['meta'],
            'filters' => [
                'name' => $input['name'] ?? null,
                'alias' => $input['alias'] ?? null,
                'email' => $input['email'] ?? null,
                'emailAlternative' => $input['emailAlternative'] ?? $input['email_alternative'] ?? null,
                'indication' => $input['indication'] ?? null,
                'observation' => $input['observation'] ?? null,
                'companyName' => $input['companyName'] ?? $input['company_name'] ?? null,
                'document' => $input['document'] ?? null,
                'active' => $input['active'] ?? null,
                'search' => $input['search'] ?? null,
                'sortField' => $input['sortField'] ?? 'name',
                'sortDirection' => $input['sortDirection'] ?? 'asc',
            ],
        ];
    }
}
