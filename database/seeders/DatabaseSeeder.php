<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SysCode;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //User::factory()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        //]);

        SysCode::create([
            'Puuid' => null,
            'Paramcode' => 'M001',
            'Param' => '當月',
            'Note' => null,
            'Createuser' => 'admin',
            'UpdateUser' => 'admin',
            'CreateTime' => now(),
            'UpdateTime' => now(),
        ]);

        SysCode::create([
            'Puuid' => null,
            'Paramcode' => 'M002',
            'Param' => '隔月',
            'Note' => null,
            'Createuser' => 'admin',
            'UpdateUser' => 'admin',
            'CreateTime' => now(),
            'UpdateTime' => now(),
        ]);
    }
}
