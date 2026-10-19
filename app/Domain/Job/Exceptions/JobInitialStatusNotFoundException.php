<?php

namespace App\Domain\Job\Exceptions;

use DomainException;

class JobInitialStatusNotFoundException extends DomainException
{
    public static function forMissing(bool $analysisMissing, bool $notScheduledMissing): self
    {
        $missing = [];

        if ($analysisMissing) {
            $missing[] = 'Em análise';
        }

        if ($notScheduledMissing) {
            $missing[] = 'Não Agendado';
        }

        return new self('Status inicial não encontrado: '.implode(', ', $missing).'.');
    }
}
