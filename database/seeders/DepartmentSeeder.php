<?php

namespace Database\Seeders;

use App\Models\Department\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Admin',
            'Comercial',
            'Produção',
            'Financeiro',
            'AudioVisual',
            'Mídias'
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['department' => $department],
                ['department' => $department]
            );
        }
    }
}
