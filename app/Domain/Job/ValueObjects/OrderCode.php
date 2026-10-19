<?php

namespace App\Domain\Job\ValueObjects;

class OrderCode
{
    private const MAX_LENGTH = 50;

    private const MIN_SUFFIX_WIDTH = 2;

    private function __construct(
        private string $value,
        private int $sequence
    ) {}

    public static function fromJobCodeAndSequence(string $jobCode, int $sequence): self
    {
        $code = trim($jobCode);
        if ($code === '') {
            throw new \InvalidArgumentException('O código do job é obrigatório para o código da OS.');
        }

        if ($sequence < 1) {
            throw new \InvalidArgumentException('A sequência da OS deve ser maior ou igual a 1.');
        }

        $width = self::MIN_SUFFIX_WIDTH;
        $digits = strlen((string) $sequence);
        if ($digits > $width) {
            $width = $digits;
        }

        $suffix = str_pad((string) $sequence, $width, '0', STR_PAD_LEFT);
        $value = $code.'_'.$suffix;

        if (strlen($value) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('O código da OS deve ter no máximo 50 caracteres.');
        }

        return new self($value, $sequence);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }
}
