<?php

namespace App\Domain\Job\ValueObjects;

class BriefingUrl
{
    private function __construct(
        private string $value
    ) {}

    public static function fromNullable(?string $raw): ?self
    {
        if ($raw === null) {
            return null;
        }

        $value = trim($raw);
        if ($value === '') {
            return null;
        }

        return self::fromString($value);
    }

    public static function fromString(string $raw): self
    {
        $value = trim($raw);

        if ($value === '') {
            throw new \InvalidArgumentException('A URL de briefing não pode ser vazia.');
        }

        if (strlen($value) > 500) {
            throw new \InvalidArgumentException('A URL de briefing deve ter no máximo 500 caracteres.');
        }

        $hasHttpScheme = str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
        if (! $hasHttpScheme) {
            throw new \InvalidArgumentException('A URL de briefing deve começar com http:// ou https://.');
        }

        if (! str_contains($value, '.')) {
            throw new \InvalidArgumentException('A URL de briefing deve conter um ponto.');
        }

        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
