<?php

namespace App\UseCase\Job;

use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\UseCase\UseCaseInterface;

class NextJobCodeUseCase implements UseCaseInterface
{
    public function __construct(
        private JobRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): string
    {
        if (! is_string($input) || trim($input) === '') {
            throw new \InvalidArgumentException('Input inválido para NextJobCodeUseCase');
        }

        return $this->repository->nextCodeForCustomer(trim($input));
    }
}
