<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Customer\Entities;

use App\Domain\Customer\Entities\FinancialInfoEntity;
use Tests\TestCase;

class FinancialInfoEntityTest extends TestCase
{
    public function test_getters_match_constructor_values(): void
    {
        $entity = new FinancialInfoEntity(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            customerId: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            deadlinePayment: 30,
            antecipation: 15,
            antecipationInstructions: 'Pay ahead',
            createdBy: 10,
            updatedBy: 20,
            createdAt: '2026-03-01T00:00:00+00:00',
            updatedAt: '2026-03-02T00:00:00+00:00',
            emailNf: 'nf@mail.com',
            financialContactName: 'Finance Dept',
            financialContactPhone: '1199887766',
            financialContactEmail: 'finance@mail.com'
        );

        $this->assertSame('cccccccc-cccc-cccc-cccc-cccccccccccc', $entity->getId());
        $this->assertSame('dddddddd-dddd-dddd-dddd-dddddddddddd', $entity->getCustomerId());
        $this->assertSame(30, $entity->getDeadlinePayment());
        $this->assertSame(15, $entity->getAntecipation());
        $this->assertSame('Pay ahead', $entity->getAntecipationInstructions());
        $this->assertSame(10, $entity->getCreatedBy());
        $this->assertSame(20, $entity->getUpdatedBy());
        $this->assertSame('nf@mail.com', $entity->getEmailNf());
        $this->assertSame('Finance Dept', $entity->getFinancialContactName());
        $this->assertSame('1199887766', $entity->getFinancialContactPhone());
        $this->assertSame('finance@mail.com', $entity->getFinancialContactEmail());
    }
}
