<?php

namespace App\Infrastructure\Eloquent\Order;

use App\Domain\Order\Entities\Order as OrderEntity;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Models\Order as OrderModel;
use Carbon\Carbon;
use DateTimeInterface;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findAllPaginated(array $filters = []): array
    {
        $sortField = 'created_at';
        if (isset($filters['sortField']) && is_string($filters['sortField']) && $filters['sortField'] !== '') {
            $sortField = $filters['sortField'];
        }

        $sortDirection = 'desc';
        if (isset($filters['sortDirection']) && is_string($filters['sortDirection']) && $filters['sortDirection'] !== '') {
            $sortDirection = $filters['sortDirection'];
        }

        $allowedSortFields = [
            'id',
            'order_code',
            'event_title',
            'start_time',
            'created_at',
            'updated_at',
        ];
        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'created_at';
        }

        $allowedSortDirections = ['asc', 'desc'];
        if (! in_array($sortDirection, $allowedSortDirections, true)) {
            $sortDirection = 'desc';
        }

        $query = OrderModel::query()->with($this->relations());
        $this->applyFilters($query, $filters);

        $page = null;
        if (isset($filters['page'])) {
            $page = max(1, (int) $filters['page']);
        }

        $paginator = $query
            ->orderBy($sortField, $sortDirection)
            ->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page)
            ->appends($filters);

        return [
            'data' => $paginator->getCollection()->map(fn (OrderModel $model) => $this->toEntity($model))->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ];
    }

    public function findById(string $id): ?OrderEntity
    {
        $id = trim($id);
        if ($id === '') {
            return null;
        }

        $model = OrderModel::query()->with($this->relations())->where('id', $id)->first();
        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<OrderModel>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(mixed $query, array $filters): void
    {
        $orderCode = $this->filterValue($filters, 'order_code', 'orderCode');
        if ($orderCode === null) {
            $orderCode = $this->filterValue($filters, 'os_code', 'osCode');
        }
        if ($orderCode !== null) {
            $query->where('order_code', 'like', '%'.$orderCode.'%');
        }

        $eventTitle = $this->filterValue($filters, 'event_title', 'eventTitle');
        if ($eventTitle !== null) {
            $query->where('event_title', 'like', '%'.$eventTitle.'%');
        }

        $jobCode = $this->filterValue($filters, 'job_code', 'jobCode');
        if ($jobCode !== null) {
            $query->whereHas('job', function ($jobQuery) use ($jobCode) {
                $jobQuery->where('job_code', 'like', '%'.$jobCode.'%');
            });
        }

        $customer = $this->filterValue($filters, 'customer', 'customer');
        if ($customer !== null) {
            $query->whereHas('customer', function ($customerQuery) use ($customer) {
                $customerQuery->where('company_name', 'like', '%'.$customer.'%')
                    ->orWhere('legal_name', 'like', '%'.$customer.'%')
                    ->orWhere('code_customer', 'like', '%'.$customer.'%');
            });
        }

        $organizer = $this->filterValue($filters, 'organizer', 'organizer');
        if ($organizer === null) {
            $organizer = $this->filterValue($filters, 'organizer_name', 'organizerName');
        }
        if ($organizer !== null) {
            $query->whereHas('organizer', function ($organizerQuery) use ($organizer) {
                $organizerQuery->where('name', 'like', '%'.$organizer.'%');
            });
        }
    }

    /**
     * @return list<string>
     */
    private function relations(): array
    {
        return [
            'job.customer',
            'job.contractor',
            'status',
            'organizer',
            'job',
            'contact',
            'customer',
        ];
    }

    private function toEntity(OrderModel $model): OrderEntity
    {
        $startTime = $this->formatDateTime($model->start_time);
        $endTime = $this->formatDateTime($model->end_time);
        $duration = $this->intOrNull($model->duration);

        return new OrderEntity(
            id: (string) $model->id,
            osCode: $this->stringOrNull($model->order_code),
            customerId: $this->stringOrNull($model->customer_id),
            organizerId: $this->stringOrNull($model->organizer_id),
            statusId: $this->stringOrNull($model->status_id),
            jobId: $this->stringOrNull($model->job_id),
            contactId: $this->stringOrNull($model->contact_id),
            eventTitle: $this->stringOrNull($model->event_title),
            osType: $this->stringOrNull($model->os_type),
            startTime: $startTime,
            endTime: $endTime,
            duration: $duration,
            location: $this->stringOrNull($model->location),
            locationDetails: $this->stringOrNull($model->location_details),
            numberParticipants: $this->intOrNull($model->number_participants),
            communicationNotes: $this->stringOrNull($model->communication_notes),
            productionNotes: $this->stringOrNull($model->production_notes),
            platformDestination: $this->stringOrNull($model->platform_destination),
            videoEngine: $this->stringOrNull($model->video_engine),
            clientPlatformName: $this->stringOrNull($model->client_platform_name),
            platformLink: $this->stringOrNull($model->platform_link),
            fileLink: $this->stringOrNull($model->file_link),
            customerLegalName: $this->customerLegalName($model),
            jobCode: $this->jobCode($model),
            jobTitle: $this->jobTitle($model),
            statusLabel: $this->statusLabel($model),
            organizerName: $this->organizerField($model, 'name'),
            organizerPhone: $this->organizerField($model, 'phone'),
            organizerEmail: $this->organizerField($model, 'email'),
            contractorName: $this->contractorName($model),
            createdAt: $this->formatTimestamp($model->created_at),
            updatedAt: $this->formatTimestamp($model->updated_at),
            durationLabel: $this->durationLabel($startTime, $endTime, $duration),
            createdBy: $this->intOrNull($model->created_by),
            updatedBy: $this->intOrNull($model->updated_by),
        );
    }

    private function customerLegalName(OrderModel $model): ?string
    {
        $customer = $model->customer;
        if ($customer === null && $model->job !== null) {
            $customer = $model->job->customer;
        }
        if ($customer === null) {
            return null;
        }

        $legalName = trim((string) $customer->legal_name);
        if ($legalName !== '') {
            return $legalName;
        }

        $companyName = trim((string) $customer->company_name);
        if ($companyName !== '') {
            return $companyName;
        }

        return null;
    }

    private function jobCode(OrderModel $model): ?string
    {
        if ($model->job === null) {
            return null;
        }

        return $this->stringOrNull($model->job->job_code);
    }

    private function jobTitle(OrderModel $model): ?string
    {
        if ($model->job === null) {
            return null;
        }

        return $this->stringOrNull($model->job->job_title);
    }

    private function statusLabel(OrderModel $model): ?string
    {
        if ($model->status === null) {
            return null;
        }

        return $this->stringOrNull($model->status->status);
    }

    private function organizerField(OrderModel $model, string $field): ?string
    {
        if ($model->organizer === null) {
            return null;
        }

        return $this->stringOrNull($model->organizer->{$field});
    }

    private function contractorName(OrderModel $model): ?string
    {
        if ($model->contact !== null) {
            $name = $this->stringOrNull($model->contact->name);
            if ($name !== null) {
                return $name;
            }
        }

        if ($model->job !== null && $model->job->contractor !== null) {
            return $this->stringOrNull($model->job->contractor->name);
        }

        return null;
    }

    private function durationLabel(?string $startTime, ?string $endTime, ?int $duration): string
    {
        if ($startTime !== null && $endTime !== null) {
            try {
                $startAt = Carbon::parse($startTime);
                $endAt = Carbon::parse($endTime);
            } catch (\Throwable) {
                return $this->labelFromStoredDuration($duration);
            }

            if ($endAt->lessThan($startAt)) {
                return $this->labelFromStoredDuration($duration);
            }

            $minutes = (int) round($startAt->diffInMinutes($endAt, true));

            return $this->labelFromMinutes($minutes);
        }

        return $this->labelFromStoredDuration($duration);
    }

    private function labelFromStoredDuration(?int $duration): string
    {
        if ($duration === null) {
            return '—';
        }

        return (string) $duration;
    }

    private function labelFromMinutes(int $minutes): string
    {
        if ($minutes < 0) {
            return '—';
        }

        $minutesPerDay = 24 * 60;
        if ($minutes < $minutesPerDay) {
            return $this->labelUnderOneDay($minutes);
        }

        $days = intdiv($minutes, $minutesPerDay);
        $restMinutes = $minutes % $minutesPerDay;
        $restHours = intdiv($restMinutes, 60);

        $dayLabel = 'dias';
        if ($days === 1) {
            $dayLabel = 'dia';
        }

        if ($restHours === 0) {
            return $days.' '.$dayLabel;
        }

        $hourLabel = 'horas';
        if ($restHours === 1) {
            $hourLabel = 'hora';
        }

        return $days.' '.$dayLabel.' e '.$restHours.' '.$hourLabel;
    }

    private function labelUnderOneDay(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        if ($hours === 0 && $rest === 0) {
            return '0 horas';
        }

        if ($hours === 0) {
            $minuteLabel = 'minutos';
            if ($rest === 1) {
                $minuteLabel = 'minuto';
            }

            return $rest.' '.$minuteLabel;
        }

        $hourLabel = 'horas';
        if ($hours === 1) {
            $hourLabel = 'hora';
        }

        if ($rest === 0) {
            return $hours.' '.$hourLabel;
        }

        $minuteLabel = 'minutos';
        if ($rest === 1) {
            $minuteLabel = 'minuto';
        }

        return $hours.' '.$hourLabel.' e '.$rest.' '.$minuteLabel;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filterValue(array $filters, string $snake, string $camel): mixed
    {
        if (array_key_exists($snake, $filters) && $filters[$snake] !== null && $filters[$snake] !== '') {
            return $filters[$snake];
        }

        if (array_key_exists($camel, $filters) && $filters[$camel] !== null && $filters[$camel] !== '') {
            return $filters[$camel];
        }

        return null;
    }

    private function formatDateTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        try {
            return Carbon::parse($text)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return $text;
        }
    }

    private function formatTimestamp(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        try {
            return Carbon::parse($text)->toAtomString();
        } catch (\Throwable) {
            return $text;
        }
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        return $text;
    }

    private function intOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
