<?php

namespace App\UseCase\EmailConfig;

use App\Domain\EmailConfig\Entities\EmailConfig as EmailConfigEntity;
use App\Domain\EmailConfig\Repositories\EmailConfigRepositoryInterface;
use App\UseCase\EmailConfig\DTO\CreateEmailConfigInputDto;
use App\UseCase\EmailConfig\DTO\EmailConfigOutputDto;
use App\UseCase\UseCaseInterface;

class CreateEmailConfigUseCase implements UseCaseInterface
{
    public function __construct(
        private EmailConfigRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): EmailConfigOutputDto
    {
        if (!$input instanceof CreateEmailConfigInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateEmailConfigUseCase');
        }

        $entity = new EmailConfigEntity(
            id: null,
            email: $input->address,
            departmentId: $input->departmentId
        );

        $created = $this->repository->create($entity);

        return new EmailConfigOutputDto(
            id: (string) $created->getId(),
            address: $created->getEmail(),
            departmentId: $created->getDepartmentId()
        );
    }
}

