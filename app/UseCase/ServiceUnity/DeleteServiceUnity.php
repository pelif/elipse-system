<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteServiceUnity implements UseCaseInterface
{
    public function __construct(
        private ServiceUnityRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = $this->resolveId($input);
        return $this->repository->deleteById($id);
    }

    private function resolveId(mixed $input): ?string
    {
        $id = match(is_string($input)) {
            true => trim($input),
            false => null
        };

        if (empty($id))
            throw new \InvalidArgumentException('Invalid input to DeleteServiceUnity usecase');

        return $id;
    }

}
