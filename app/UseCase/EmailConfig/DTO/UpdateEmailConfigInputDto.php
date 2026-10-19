<?php

namespace App\UseCase\EmailConfig\DTO;

class UpdateEmailConfigInputDto
{
    public function __construct(
        public string $id,
        public string $address,
        public string $departmentId,
    ) {}
}

