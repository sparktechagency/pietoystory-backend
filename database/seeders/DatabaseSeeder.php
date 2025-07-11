<?php

namespace Database\Seeders;

use App\Models\County;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'full_name' => 'test',
        //     'email' => 'test@gmail.com',
        // ]);

        $county = County::create(['state' => 'Maryland', 'county' => 'Anne Arundel']);

        $county->zipCodes()->createMany([
            ['zip_code' => '21122'],
            ['zip_code' => '21061'],
            ['zip_code' => '21401'],
            // ...more zips
        ]);
    }
}
