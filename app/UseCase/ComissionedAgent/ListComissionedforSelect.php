<?php

namespace App\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\UseCaseInterface;

class ListComissionedforSelect implements UseCaseInterface
{
    public function __construct(
        private ComissionedAgentRepositoryInterface $repository
    ) {}

    public function execute(mixed $input=null): array
    {
        return $this->repository->findAllForSelect();
    }
}
