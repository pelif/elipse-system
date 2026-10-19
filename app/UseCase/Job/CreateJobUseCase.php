<?php

namespace App\UseCase\Job;

use App\Domain\Job\Entities\Job as JobEntity;
use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\Domain\Job\ValueObjects\BriefingUrl;
use App\Domain\Job\ValueObjects\JobCode;
use App\Domain\Status\Entities\Status;
use App\Domain\Status\Enums\StatusType;
use App\UseCase\Job\DTO\CreateJobInputDto;
use App\UseCase\Job\DTO\JobOutputDto;
use App\UseCase\UseCaseInterface;
use Carbon\Carbon;

class CreateJobUseCase implements UseCaseInterface
{
    public function __construct(
        private JobRepositoryInterface $repository
    ) {}

    public function execute(mixed $input = null): JobOutputDto
    {
        if (! $input instanceof CreateJobInputDto) {
            throw new \InvalidArgumentException('Input inválido para CreateJobUseCase');
        }

        $created = $this->repository->create($this->toEntity($input));

        return JobOutputDto::fromEntity($created);
    }

    private function toEntity(CreateJobInputDto $input): JobEntity
    {
        if (trim($input->customerId) === '' || trim($input->productId) === '' || trim($input->contractorId) === '') {
            throw new \InvalidArgumentException('Cliente, produto e contratante são obrigatórios.');
        }

        if (trim($input->jobTitle) === '') {
            throw new \InvalidArgumentException('O título do job é obrigatório.');
        }

        return new JobEntity(
            customerId: $input->customerId,
            productId: $input->productId,
            jobCode: JobCode::fromString($input->jobCode, $input->customerCode),
            jobTitle: $input->jobTitle,
            contractorId: $input->contractorId,
            briefingUrl: BriefingUrl::fromNullable($input->briefingUrl),
            status: new Status(
                id: null,
                status: 'Em análise',
                type: StatusType::JOB
            ),
            createdBy: $input->createdBy,
            osQuantity: $input->osQuantity,
            observations: $input->observations,
            targetBudget: $this->normalizeDecimal($input->targetBudget, 2),
            purchaseOrder: $input->purchaseOrder,
            purchaseOrderLink: $input->purchaseOrderLink,
            invoiceDueDate: $this->normalizeDateTime($input->invoiceDueDate),
            additionalInfo: $input->additionalInfo,
            comissionedAgentId: $input->comissionedAgentId,
            commissionedPercentage: $this->commissionedPercentage($input->commissionedPercentage),
            commissionPaymentDueDate: $this->normalizeDateTime($input->commissionPaymentDueDate),
            adminExpensesPercentage: '0.0000',
            financialExpensesPercentage: '0.0000',
        );
    }

    private function commissionedPercentage(?string $value): ?string
    {
        $normalized = $this->normalizeDecimal($value, 4);
        if ($normalized === null) {
            return null;
        }

        if ((float) $normalized > 999.9999) {
            throw new \InvalidArgumentException('O percentual de comissão deve ficar entre 0 e 999.9999.');
        }

        return $normalized;
    }

    private function normalizeDecimal(?string $value, int $scale): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $trimmed);
        if (! is_numeric($normalized)) {
            throw new \InvalidArgumentException('Valor decimal inválido.');
        }

        if ((float) $normalized < 0) {
            throw new \InvalidArgumentException('Valor decimal não pode ser negativo.');
        }

        return number_format((float) $normalized, $scale, '.', '');
    }

    private function normalizeDateTime(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $trimmed) === 1) {
            $date = \DateTime::createFromFormat('!d/m/Y', $trimmed);
            if ($date === false) {
                throw new \InvalidArgumentException('Data inválida.');
            }

            return $date->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($trimmed)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            throw new \InvalidArgumentException('Data inválida.');
        }
    }
}
