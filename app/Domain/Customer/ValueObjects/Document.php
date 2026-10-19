<?php

namespace App\Domain\Customer\ValueObjects;

class Document
{
    private function __construct(
        private string $normalizedDigits
    ) {}

    public static function fromString(string $raw): self
    {
        $digits = self::stripToDigits($raw);

        if (!self::hasCnpjLength($digits)) {
            throw new \InvalidArgumentException('CNPJ must contain 14 digits.');
        }

        if (self::isUniformDigits($digits)) {
            throw new \InvalidArgumentException('Invalid CNPJ.');
        }

        if (!self::checkFirstVerifierDigit($digits)) {
            throw new \InvalidArgumentException('Invalid CNPJ check digits.');
        }

        if (!self::checkSecondVerifierDigit($digits)) {
            throw new \InvalidArgumentException('Invalid CNPJ check digits.');
        }

        return new self($digits);
    }

    /**
     * Load persisted document values without failing on invalid or legacy rows (persists the digit string).
     */
    public static function fromPersisted(?string $raw): self
    {
        $digits = self::stripToDigits((string) ($raw ?? ''));
        if ($digits === '') {
            return new self(str_repeat('0', 14));
        }

        if (strlen($digits) > 14) {
            $digits = substr($digits, -14);
        } elseif (strlen($digits) < 14) {
            $digits = str_pad($digits, 14, '0', STR_PAD_LEFT);
        }

        if (self::hasCnpjLength($digits) && ! self::isUniformDigits($digits)
            && self::checkFirstVerifierDigit($digits)
            && self::checkSecondVerifierDigit($digits)) {
            return new self($digits);
        }

        return new self($digits);
    }

    public function getValue(): string
    {
        return $this->normalizedDigits;
    }

    public function equals(self $other): bool
    {
        return $this->normalizedDigits === $other->normalizedDigits;
    }

    private static function stripToDigits(string $raw): string
    {
        return preg_replace('/\D/', '', $raw) ?? '';
    }

    private static function hasCnpjLength(string $digits): bool
    {
        return strlen($digits) === 14;
    }

    private static function isUniformDigits(string $digits): bool
    {
        return (bool) preg_match('/^(\d)\1{13}$/', $digits);
    }

    /** @var array<int, int> */
    private static function firstVerifierWeights(): array
    {
        return [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    }

    /** @var array<int, int> */
    private static function secondVerifierWeights(): array
    {
        return [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    }

    private static function checkFirstVerifierDigit(string $digits): bool
    {
        $weights = self::firstVerifierWeights();
        $sum = self::weightedSum(substr($digits, 0, 12), $weights);
        $expected = self::verifierDigitFromSum($sum);

        return (int) $digits[12] === $expected;
    }

    private static function checkSecondVerifierDigit(string $digits): bool
    {
        $weights = self::secondVerifierWeights();
        $sum = self::weightedSum(substr($digits, 0, 13), $weights);
        $expected = self::verifierDigitFromSum($sum);

        return (int) $digits[13] === $expected;
    }

    /**
     * @param  array<int, int>  $weights
     */
    private static function weightedSum(string $digitSequence, array $weights): int
    {
        $sum = 0;
        $len = strlen($digitSequence);
        for ($i = 0; $i < $len; $i++) {
            $sum += (int) $digitSequence[$i] * $weights[$i];
        }

        return $sum;
    }

    private static function verifierDigitFromSum(int $sum): int
    {
        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
