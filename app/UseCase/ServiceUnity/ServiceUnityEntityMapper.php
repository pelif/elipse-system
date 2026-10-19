<?php

namespace App\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use DateTimeImmutable;
use DateTimeZone;

final class ServiceUnityEntityMapper
{
    public static function fromCreateDto(ServiceUnityInputDto $input): ServiceUnity
    {
        $updatedAt = match($input->updatedAt !== null) {
            true => new DateTimeImmutable($input->updatedAt),
            false => null
        };

        return new ServiceUnity(
            id: null,
            unity: $input->unity,
            createdBy: $input->createdBy,
            updatedBy: $input->updatedBy,
            createdAt: new DateTimeImmutable($input->createdAt),
            updatedAt: $updatedAt
        );
    }

    public static function fromUpdateDto(ServiceUnityInputDto $input): ServiceUnity
    {
        $createdAt = match($input->createdAt !== null) {
            true => new DateTimeImmutable($input->createdAt),
            false => null
        };

        return new ServiceUnity(
            id: $input->id,
            unity: $input->unity,
            createdBy: $input->createdBy,
            updatedBy: $input->updatedBy,
            createdAt: $createdAt,
            updatedAt: new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'))
        );
    }
}
