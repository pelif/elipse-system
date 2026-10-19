<?php

namespace App\Domain\Job\ValueObjects;

class JobCode
{
    private function __construct(
        private string $value,
        private string $customerCode,
        private int $sequence
    ) {}

    public static function fromString(string $raw, string $customerCode): self
    {
        $prefix = self::normalizeCustomerCode($customerCode);
        $code = trim($raw);
        $separator = $prefix.'_';

        if (! str_starts_with($code, $separator)) {
            throw new \InvalidArgumentException('O código do job deve começar com o código do cliente seguido de underscore.');
        }

        $suffix = substr($code, strlen($separator));
        if (strlen($suffix) !== 4 || ! ctype_digit($suffix)) {
            throw new \InvalidArgumentException('O código do job deve terminar com 4 dígitos.');
        }

        $value = $separator.$suffix;
        if (strlen($value) > 20) {
            throw new \InvalidArgumentException('O código do job deve ter no máximo 20 caracteres.');
        }

        return new self($value, $prefix, (int) $suffix);
    }

    public static function fromPersisted(string $raw): self
    {
        $code = trim($raw);
        $position = strrpos($code, '_');
        if ($position === false || $position === 0) {
            throw new \InvalidArgumentException('O código do job deve começar com o código do cliente seguido de underscore.');
        }

        $prefix = substr($code, 0, $position);
        $suffix = substr($code, $position + 1);
        if ($prefix === '' || strlen($suffix) !== 4 || ! ctype_digit($suffix)) {
            throw new \InvalidArgumentException('O código do job deve terminar com 4 dígitos.');
        }

        if (strlen($code) > 20) {
            throw new \InvalidArgumentException('O código do job deve ter no máximo 20 caracteres.');
        }

        return new self($code, $prefix, (int) $suffix);
    }

    public static function fromSequence(string $customerCode, int $sequence): self
    {
        $prefix = self::normalizeCustomerCode($customerCode);

        if ($sequence < 0 || $sequence > 9999) {
            throw new \InvalidArgumentException('A sequência do código do job deve ficar entre 0 e 9999.');
        }

        $suffix = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        return new self($prefix.'_'.$suffix, $prefix, $sequence);
    }

    public static function trySequence(?string $raw, string $customerCode): ?int
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        try {
            return self::fromString($raw, $customerCode)->getSequence();
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCustomerCode(): string
    {
        return $this->customerCode;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    private static function normalizeCustomerCode(string $customerCode): string
    {
        $prefix = trim($customerCode);

        if ($prefix === '') {
            throw new \InvalidArgumentException('O código do cliente é obrigatório para o código do job.');
        }

        $base = self::baseCustomerCode($prefix);
        if ($base === '') {
            throw new \InvalidArgumentException('O código do cliente é obrigatório para o código do job.');
        }

        if (strlen($base) > 10) {
            throw new \InvalidArgumentException('O código do cliente deve ter no máximo 10 caracteres.');
        }

        return $base;
    }

    private static function baseCustomerCode(string $code): string
    {
        $matched = preg_match('/^(.*\D)\d{3,}$/', $code, $matches);
        if ($matched !== 1) {
            return $code;
        }

        return $matches[1];
    }
}
