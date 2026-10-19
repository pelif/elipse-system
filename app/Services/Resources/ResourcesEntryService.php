<?php

namespace App\Services\Resources;

use App\Models\Department\Department;

class ResourcesEntryService
{
    public function __construct() {}

    public function nullableTrimmedString(mixed $value): ?string
    {
      if (! is_string($value)) return null;

      $trimmed = trim($value);

      return match(true) {
        $trimmed === '' => null,
        default => $trimmed
      };
    }

    public function nullablePositiveInt(mixed $value): ?int
    {

      $result = match(true) {
        $value === null || $value === '' => null,
        ! is_numeric($value) => null,
        default => (int) $value
      };

      return match (true) {
        $result <= 0 => null,
        default => $result
      };
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public function departmentSelectOptions(): array
    {
        $rows = Department::query()
            ->orderBy('department', 'asc')
            ->get(['id', 'department']);

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id' => (string) $row->id,
                'label' => (string) $row->department,
            ];
        }

        return $out;
    }


}
