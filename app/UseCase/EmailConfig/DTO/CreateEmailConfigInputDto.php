<?php

namespace App\UseCase\EmailConfig\DTO;

class CreateEmailConfigInputDto
{
    public function __construct(
        public string $address,
        public string $departmentId,
    ) {}
}

