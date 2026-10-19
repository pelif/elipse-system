<?php

namespace Database\Seeders;

use App\Models\ServiceUnity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceUnitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'admin@example.com')
            ->first();

        $createdBy = null;
        if ($admin !== null) {
            $createdBy = (int) $admin->id;
        }

        $units = [
            'Hora',
            'Diária',
            'Pacote',
        ];

        foreach ($units as $unity) {
            ServiceUnity::firstOrCreate(
                ['unity' => $unity],
                [
                    'unity' => $unity,
                    'created_by' => $createdBy,
                ]
            );
        }
    }
}
