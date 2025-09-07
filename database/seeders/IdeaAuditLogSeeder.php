<?php

namespace Database\Seeders;

use App\Models\IdeaAuditLog;
use Illuminate\Database\Seeder;

class IdeaAuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IdeaAuditLog::factory()->count(50)->create();
    }
}
