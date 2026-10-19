<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Order\Entities;

use App\Domain\Order\Entities\Order;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $order = new Order(
            id: ' 11111111-1111-1111-1111-111111111111 ',
            osCode: ' TST1C_0001_01 ',
            customerId: '22222222-2222-2222-2222-222222222222',
            organizerId: '33333333-3333-3333-3333-333333333333',
            statusId: '44444444-4444-4444-4444-444444444444',
            jobId: '55555555-5555-5555-5555-555555555555',
            contactId: '66666666-6666-6666-6666-666666666666',
            eventTitle: ' Lançamento ',
            osType: 'Remoto',
            startTime: '2026-10-01 09:00:00',
            endTime: '2026-10-01 12:00:00',
            duration: 180,
            location: 'Estúdio',
            locationDetails: 'Sala 2',
            numberParticipants: 40,
            communicationNotes: 'Falar com o RH',
            productionNotes: 'Duas câmeras',
            platformDestination: 'Zoom',
            videoEngine: 'Zoom',
            clientPlatformName: 'Portal Cliente',
            platformLink: 'https://zoom.example/sala',
            fileLink: 'https://files.example/os',
            customerLegalName: 'Cliente Alfa LTDA',
            jobCode: 'TST1C_0001',
            jobTitle: 'Job do lançamento',
            statusLabel: 'Não Agendado',
            organizerName: 'Marina',
            organizerPhone: '11988887777',
            organizerEmail: 'marina@tests.local',
            contractorName: 'Carla',
            createdAt: '2026-09-29T14:00:00+00:00',
            updatedAt: '2026-09-29T15:00:00+00:00',
            durationLabel: '3 horas',
            createdBy: 7,
            updatedBy: 8,
        );

        $this->assertSame('11111111-1111-1111-1111-111111111111', $order->getId());
        $this->assertSame('TST1C_0001_01', $order->getOsCode());
        $this->assertSame('22222222-2222-2222-2222-222222222222', $order->getCustomerId());
        $this->assertSame('33333333-3333-3333-3333-333333333333', $order->getOrganizerId());
        $this->assertSame('44444444-4444-4444-4444-444444444444', $order->getStatusId());
        $this->assertSame('55555555-5555-5555-5555-555555555555', $order->getJobId());
        $this->assertSame('66666666-6666-6666-6666-666666666666', $order->getContactId());
        $this->assertSame('Lançamento', $order->getEventTitle());
        $this->assertSame('Remoto', $order->getOsType());
        $this->assertSame('2026-10-01 09:00:00', $order->getStartTime());
        $this->assertSame('2026-10-01 12:00:00', $order->getEndTime());
        $this->assertSame(180, $order->getDuration());
        $this->assertSame('Estúdio', $order->getLocation());
        $this->assertSame('Sala 2', $order->getLocationDetails());
        $this->assertSame(40, $order->getNumberParticipants());
        $this->assertSame('Falar com o RH', $order->getCommunicationNotes());
        $this->assertSame('Duas câmeras', $order->getProductionNotes());
        $this->assertSame('Zoom', $order->getPlatformDestination());
        $this->assertSame('Zoom', $order->getVideoEngine());
        $this->assertSame('Portal Cliente', $order->getClientPlatformName());
        $this->assertSame('https://zoom.example/sala', $order->getPlatformLink());
        $this->assertSame('https://files.example/os', $order->getFileLink());
        $this->assertSame('Cliente Alfa LTDA', $order->getCustomerLegalName());
        $this->assertSame('TST1C_0001', $order->getJobCode());
        $this->assertSame('Job do lançamento', $order->getJobTitle());
        $this->assertSame('Não Agendado', $order->getStatusLabel());
        $this->assertSame('Marina', $order->getOrganizerName());
        $this->assertSame('11988887777', $order->getOrganizerPhone());
        $this->assertSame('marina@tests.local', $order->getOrganizerEmail());
        $this->assertSame('Carla', $order->getContractorName());
        $this->assertSame('2026-09-29T14:00:00+00:00', $order->getCreatedAt());
        $this->assertSame('2026-09-29T15:00:00+00:00', $order->getUpdatedAt());
        $this->assertSame('3 horas', $order->getDurationLabel());
        $this->assertSame(7, $order->getCreatedBy());
        $this->assertSame(8, $order->getUpdatedBy());
    }

    public function test_optional_fields_can_be_null_and_blank_strings_become_null(): void
    {
        $order = new Order(
            id: '11111111-1111-1111-1111-111111111111',
            osCode: 'TST1C_0001_01',
            eventTitle: '   ',
            numberParticipants: 0,
        );

        $this->assertNull($order->getEventTitle());
        $this->assertNull($order->getCustomerId());
        $this->assertNull($order->getOrganizerId());
        $this->assertNull($order->getDuration());
        $this->assertNull($order->getPlatformDestination());
        $this->assertNull($order->getDurationLabel());
        $this->assertSame(0, $order->getNumberParticipants());
    }

    public function test_rejects_blank_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Order(
            id: '   ',
            osCode: 'TST1C_0001_01',
        );
    }

    public function test_blank_os_code_becomes_null(): void
    {
        $order = new Order(
            id: '11111111-1111-1111-1111-111111111111',
            osCode: '   ',
        );

        $this->assertNull($order->getOsCode());
    }

    public function test_rejects_negative_number_of_participants(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Order(
            id: '11111111-1111-1111-1111-111111111111',
            osCode: 'TST1C_0001_01',
            numberParticipants: -1,
        );
    }

    public function test_public_api_exposes_getters_only(): void
    {
        $methods = (new ReflectionClass(Order::class))->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() !== Order::class) {
                continue;
            }

            if ($method->getName() === '__construct') {
                continue;
            }

            $this->assertStringStartsWith('get', $method->getName());
        }
    }
}
