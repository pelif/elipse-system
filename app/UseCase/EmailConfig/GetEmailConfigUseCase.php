<?php

namespace App\UseCase\EmailConfig;

use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\UseCase\EmailConfig\DTO\EmailConfigOutputDto;
use App\UseCase\UseCaseInterface;

class GetEmailConfigUseCase implements UseCaseInterface
{
    public function __construct(
        private EmailConfigRepositoryInterface $repository
    ) {}

    public function execute(mixed $input): EmailConfigOutputDto
    {
        $emailConfig = $this->repository->findById((string) $input);

        if (!$emailConfig) {
            throw new \Exception('Configuração de E-mail não encontrada');
        }

        return new EmailConfigOutputDto(
            id: (string) $emailConfig->getId(),
            address: $emailConfig->getEmail(),
            departmentId: $emailConfig->getDepartmentId()
        );
    }
}

