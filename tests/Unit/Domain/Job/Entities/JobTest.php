<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Job\Entities;

use App\Domain\ComissionedAgent\Entities\ComissionedAgent;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\ValueObjects\Document;
use App\Domain\Job\Entities\Job;
use App\Domain\Job\ValueObjects\BriefingUrl;
use App\Domain\Job\ValueObjects\JobCode;
use App\Domain\Product\Entities\Product;
use App\Domain\Status\Entities\Status;
use App\Domain\Status\Enums\StatusType;
use InvalidArgumentException;
use Tests\TestCase;

class JobTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $customer = new Customer(
            id: '22222222-2222-2222-2222-222222222222',
            companyName: 'Cliente Alfa',
            legalName: 'Cliente Alfa LTDA',
            document: Document::fromPersisted('11222333000181'),
            codeCustomer: 'TST1C',
            active: true,
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null,
            comissionedAgentId: null,
            comissionedAgent: null,
            financialInfo: null,
            contacts: [],
            relationShipsCounts: null,
        );
        $product = new Product(
            id: '33333333-3333-3333-3333-333333333333',
            name: 'Live'
        );
        $status = new Status(
            id: '44444444-4444-4444-4444-444444444444',
            status: 'Em análise',
            type: StatusType::JOB
        );
        $agent = new ComissionedAgent(
            id: '55555555-5555-5555-5555-555555555555',
            name: 'Agente',
            defaultComission: 10.0,
            paymentDays: 30
        );

        $job = new Job(
            customerId: '22222222-2222-2222-2222-222222222222',
            productId: '33333333-3333-3333-3333-333333333333',
            jobCode: JobCode::fromString('TST1C_0001', 'TST1C'),
            jobTitle: '  Lançamento  ',
            contractorId: '66666666-6666-6666-6666-666666666666',
            id: '11111111-1111-1111-1111-111111111111',
            customer: $customer,
            product: $product,
            briefingUrl: BriefingUrl::fromString('https://briefing.example.com/job'),
            statusId: '44444444-4444-4444-4444-444444444444',
            status: $status,
            createdBy: 7,
            createdByName: 'Ana',
            osQuantity: 2,
            observations: 'Obs',
            targetBudget: '1500.50',
            purchaseOrder: 'PC-1',
            purchaseOrderLink: 'https://po.example.com',
            invoiceDueDate: '2026-04-01 00:00:00',
            additionalInfo: 'Complemento',
            comissionedAgentId: '55555555-5555-5555-5555-555555555555',
            comissionedAgent: $agent,
            commissionedPercentage: '10.0000',
            commissionPaymentDueDate: '2026-05-01 00:00:00',
            contractorName: 'Contratante',
        );

        $this->assertSame('11111111-1111-1111-1111-111111111111', $job->getId());
        $this->assertSame('Lançamento', $job->getJobTitle());
        $this->assertSame('TST1C_0001', $job->getJobCode()->getValue());
        $briefingUrl = $job->getBriefingUrl();
        $this->assertNotNull($briefingUrl);
        $this->assertSame('https://briefing.example.com/job', $briefingUrl->getValue());
        $this->assertSame($customer, $job->getCustomer());
        $this->assertSame($product, $job->getProduct());
        $this->assertSame($status, $job->getStatus());
        $this->assertSame($agent, $job->getComissionedAgent());
        $this->assertSame('66666666-6666-6666-6666-666666666666', $job->getContractorId());
        $this->assertSame('Contratante', $job->getContractorName());
        $this->assertSame(2, $job->getOsQuantity());
        $this->assertSame(7, $job->getCreatedBy());
        $this->assertSame('Ana', $job->getCreatedByName());
        $this->assertSame('1500.50', $job->getTargetBudget());
        $this->assertSame('0.0000', $job->getAdminExpensesPercentage());
        $this->assertSame('0.0000', $job->getFinancialExpensesPercentage());
        $this->assertNull($job->getApprovalDate());
        $this->assertNull($job->getCommissionValue());
        $this->assertNull($job->getTaxPercentage());
        $this->assertNull($job->getProfitPercentage());
        $this->assertNull($job->getMonthlyInterestRate());
        $this->assertNull($job->getDailyInterestRate());
    }

    public function test_rejects_empty_title(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Job(
            customerId: '22222222-2222-2222-2222-222222222222',
            productId: '33333333-3333-3333-3333-333333333333',
            jobCode: JobCode::fromString('TST1C_0001', 'TST1C'),
            jobTitle: '   ',
            contractorId: '66666666-6666-6666-6666-666666666666',
        );
    }

    public function test_rejects_negative_os_quantity(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Job(
            customerId: '22222222-2222-2222-2222-222222222222',
            productId: '33333333-3333-3333-3333-333333333333',
            jobCode: JobCode::fromString('TST1C_0001', 'TST1C'),
            jobTitle: 'Job',
            contractorId: '66666666-6666-6666-6666-666666666666',
            osQuantity: -1,
        );
    }
}
