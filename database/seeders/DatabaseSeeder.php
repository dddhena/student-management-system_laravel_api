<?php

namespace Database\Seeders;

use App\Models\ParentModel;
use App\Models\User;
use App\Models\Student;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      
    User::factory()->count(5)->create();
    ParentModel::factory()->count(5)->create();
    Student::factory()->count(10)->create();
    }
}
