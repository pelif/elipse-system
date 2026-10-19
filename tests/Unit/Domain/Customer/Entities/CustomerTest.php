<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Customer\Entities;

use App\Domain\Customer\Entities\ContactEntity;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Entities\FinancialInfoEntity;
use App\Domain\Customer\ValueObjects\CustomerRelationshipsCounts;
use App\Domain\Customer\ValueObjects\Document;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    private const VALID_CNPJ = '60701190000104';

    public function test_aggregate_getters_reflect_nested_value_objects_and_contacts(): void
    {
        $document = Document::fromString(self::VALID_CNPJ);
        $financial = new FinancialInfoEntity(
            id: null,
            customerId: null,
            deadlinePayment: 20,
            antecipation: 2,
            antecipationInstructions: 'Bank transfer',
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null,
            emailNf: 'x@corp.com'
        );

        $contact = new ContactEntity(
            id: null,
            customerId: null,
            name: 'Maria',
            phone: null,
            email: 'm@corp.com',
            typeContact: 'Financial',
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );

        $customer = new Customer(
            id: 'fedcba98-7654-3210-fedc-ba9876543210',
            companyName: 'ACME Brasil',
            legalName: 'ACME Brasil LTDA',
            document: $document,
            codeCustomer: 'ACME0001',
            active: true,
            deadlinePayment: $financial->getDeadlinePayment(),
            antecipation: $financial->getAntecipation(),
            antecipationInstrutions: $financial->getAntecipationInstructions(),
            createdBy: 100,
            updatedBy: 200,
            createdAt: '2026-02-01T10:00:00+00:00',
            updatedAt: '2026-02-02T10:00:00+00:00',
            comissionedAgentId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            comissionedAgent: null,
            financialInfo: $financial,
            contacts: [$contact],
            relationShipsCounts: new CustomerRelationshipsCounts(2, 1, 3),
        );

        $this->assertSame('fedcba98-7654-3210-fedc-ba9876543210', $customer->getId());
        $this->assertSame('60701190000104', $customer->getDocument()->getValue());
        $this->assertSame($financial, $customer->getFinancialInfo());
        $this->assertCount(1, $customer->getContacts());
        $this->assertSame('Maria', $customer->getContacts()[0]->getName());
        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $customer->getComissionedAgentId());
        $this->assertTrue($customer->isActive());
        $this->assertNotNull($customer->getRelationShipsCounts());
        $this->assertSame(2, $customer->getRelationShipsCounts()->getJobs());
        $this->assertSame(1, $customer->getRelationShipsCounts()->getContractors());
        $this->assertSame(3, $customer->getRelationShipsCounts()->getOrganizers());
    }
}
