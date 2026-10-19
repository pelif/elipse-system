<?php

namespace App\UseCase\EmailConfig\DTO;

class EmailConfigOutputDto
{
    public function __construct(
        public string $id,
        public string $address,
        public string $departmentId,
    ) {}
}

