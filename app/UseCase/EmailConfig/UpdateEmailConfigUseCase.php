<?php

namespace App\UseCase\EmailConfig;

use App\Domain\EmailConfig\Entities\EmailConfig as EmailConfigEntity;
use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\UseCase\EmailConfig\DTO\EmailConfigOutputDto;
use App\UseCase\EmailConfig\DTO\UpdateEmailConfigInputDto;
use App\UseCase\UseCaseInterface;

class UpdateEmailConfigUseCase implements UseCaseInterface
{
    public function __construct(
        private EmailConfigRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): EmailConfigOutputDto
    {
        if (!$input instanceof UpdateEmailConfigInputDto) {
            throw new \InvalidArgumentException('Input inválido para UpdateEmailConfigUseCase');
        }

        $entity = new EmailConfigEntity(
            id: $input->id,
            email: $input->address,
            departmentId: $input->departmentId
        );

        $updated = $this->repository->update($entity);

        if (!$updated) {
            throw new \Exception('Configuração de E-mail não encontrada');
        }

        return new EmailConfigOutputDto(
            id: (string) $updated->getId(),
            address: $updated->getEmail(),
            departmentId: $updated->getDepartmentId()
        );
    }
}

