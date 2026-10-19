<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Customer\ValueObjects;

use App\Domain\Customer\ValueObjects\Document;
use InvalidArgumentException;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    /** Known valid Brazilian CNPJ (digits-only and masked forms). */
    private const VALID_CNPJ = '11222333000181';

    public function test_from_string_normalizes_masked_cnpj(): void
    {
        $document = Document::fromString('11.222.333/0001-81');

        $this->assertSame(self::VALID_CNPJ, $document->getValue());
    }

    public function test_from_string_accepts_digits_only(): void
    {
        $document = Document::fromString(self::VALID_CNPJ);

        $this->assertSame(self::VALID_CNPJ, $document->getValue());
    }

    public function test_from_string_throws_when_not_fourteen_digits_after_strip(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('CNPJ must contain 14 digits.');

        Document::fromString('123456789012');
    }

    public function test_from_string_throws_for_uniform_digit_sequence(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CNPJ.');

        Document::fromString('11.111.111/1111-11');
    }

    public function test_from_string_throws_for_invalid_verifier_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CNPJ check digits.');

        Document::fromString('11222333000182');
    }

    public function test_equals_compares_normalized_values(): void
    {
        $a = Document::fromString(self::VALID_CNPJ);
        $b = Document::fromString('11.222.333/0001-81');

        $this->assertTrue($a->equals($b));
    }

    public function test_from_persisted_empty_returns_zero_padded_fourteen_digits(): void
    {
        $document = Document::fromPersisted('');

        $this->assertSame(str_repeat('0', 14), $document->getValue());
    }

    public function test_from_persisted_truncates_when_more_than_fourteen_digits_keeps_suffix(): void
    {
        // Leading noise so the normalized tail matches a valid CNPJ.
        $raw = '00'.self::VALID_CNPJ;
        $document = Document::fromPersisted($raw);

        $this->assertSame(self::VALID_CNPJ, $document->getValue());
    }

    public function test_from_persisted_keeps_legacy_invalid_but_normalizes_width(): void
    {
        $document = Document::fromPersisted('99');

        $this->assertSame(str_pad('99', 14, '0', STR_PAD_LEFT), $document->getValue());
    }
}
