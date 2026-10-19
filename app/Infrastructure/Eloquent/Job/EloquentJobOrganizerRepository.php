<?php

namespace App\Infrastructure\Eloquent\Job;

use App\Domain\Job\Entities\JobOrganizerLink;
use App\Domain\Job\Repositories\JobOrganizerRepositoryInterface;
use App\Domain\Organizer\Entities\Organizer;
use App\Models\Contact as ContactModel;
use App\Models\Job as JobModel;
use DateTimeInterface;

class EloquentJobOrganizerRepository implements JobOrganizerRepositoryInterface
{
    private const TYPE_ORGANIZER = 'Organizer';

    public function syncOrganizersForJob(string $jobId, array $organizerIds): void
    {
        $jobId = trim($jobId);
        if ($jobId === '') {
            throw new \InvalidArgumentException('O job é obrigatório.');
        }

        $normalized = [];
        foreach ($organizerIds as $organizerId) {
            $trimmed = trim((string) $organizerId);
            if ($trimmed === '') {
                continue;
            }

            new JobOrganizerLink($jobId, $trimmed);
            $normalized[$trimmed] = $trimmed;
        }

        $job = JobModel::query()->find($jobId);
        if ($job === null) {
            throw new \InvalidArgumentException('Job não encontrado.');
        }

        $validIds = [];
        if ($normalized !== []) {
            $validIds = ContactModel::query()
                ->where('type_contact', self::TYPE_ORGANIZER)
                ->where('customer_id', $job->customer_id)
                ->whereIn('id', array_values($normalized))
                ->pluck('id')
                ->all();
        }

        $ids = [];
        foreach ($validIds as $id) {
            $ids[] = (string) $id;
        }

        $job->organizers()->sync($ids);
    }

    public function findOrganizerIdsByJobId(string $jobId): array
    {
        $ids = [];
        foreach ($this->organizerModels($jobId) as $model) {
            $ids[] = (string) $model->id;
        }

        return $ids;
    }

    public function findOrganizersByJobId(string $jobId): array
    {
        $organizers = [];
        foreach ($this->organizerModels($jobId) as $model) {
            $organizers[] = $this->toEntity($model);
        }

        return $organizers;
    }

    /**
     * @return array<int, ContactModel>
     */
    private function organizerModels(string $jobId): array
    {
        $jobId = trim($jobId);
        if ($jobId === '') {
            return [];
        }

        return ContactModel::query()
            ->select('contacts.*')
            ->join('job_organizer', 'contacts.id', '=', 'job_organizer.organizer_id')
            ->where('job_organizer.job_id', $jobId)
            ->where('contacts.type_contact', self::TYPE_ORGANIZER)
            ->with('customer')
            ->orderBy('contacts.name')
            ->orderBy('contacts.id')
            ->get()
            ->all();
    }

    private function toEntity(ContactModel $model): Organizer
    {
        $createdBy = null;
        if ($model->created_by !== null) {
            $createdBy = (int) $model->created_by;
        }

        $updatedBy = null;
        if ($model->updated_by !== null) {
            $updatedBy = (int) $model->updated_by;
        }

        return new Organizer(
            id: $model->id,
            customerId: $model->customer_id,
            name: (string) ($model->name ?? ''),
            phone: (string) ($model->phone ?? ''),
            email: (string) ($model->email ?? ''),
            communicationCare: $model->communication_care,
            observations: $model->observations,
            typeContact: $model->type_contact,
            customerName: $this->customerDisplayNameFromModel($model),
            createdBy: $createdBy,
            createdAt: $model->created_at?->format(DateTimeInterface::ATOM),
            updatedBy: $updatedBy,
            updatedAt: $model->updated_at?->format(DateTimeInterface::ATOM),
        );
    }

    private function customerDisplayNameFromModel(ContactModel $model): ?string
    {
        if (! $model->relationLoaded('customer')) {
            return null;
        }

        $customer = $model->customer;
        if ($customer === null) {
            return null;
        }

        $companyName = trim((string) $customer->company_name);
        if ($companyName !== '') {
            return $companyName;
        }

        $legalName = trim((string) $customer->legal_name);
        if ($legalName !== '') {
            return $legalName;
        }

        return null;
    }
}
