<?php

namespace App\UseCase\ComissionedAgent;

use App\Domain\ComissionedAgent\Repositories\ComissionedAgentRepositoryInterface;
use App\UseCase\UseCaseInterface;

class DeleteComissionedAgentUseCase implements UseCaseInterface
{
    public function __construct(
        private ComissionedAgentRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): bool
    {
        $id = is_string($input) ? trim($input) : null;

        if (empty($id)) {
            throw new \InvalidArgumentException('Input inválido para DeleteComissionedAgentUseCase');
        }

        return $this->repository->deleteById($id);
    }
}
