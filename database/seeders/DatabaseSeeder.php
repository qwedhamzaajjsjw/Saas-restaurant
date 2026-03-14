<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters: plans first, then super admin
        $this->call([
            PlanSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
