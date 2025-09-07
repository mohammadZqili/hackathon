<?php

namespace Database\Seeders;

use App\Models\IdeaFile;
use Illuminate\Database\Seeder;

class IdeaFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IdeaFile::factory()->count(30)->create();
    }
}
