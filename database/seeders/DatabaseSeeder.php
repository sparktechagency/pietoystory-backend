<?php

namespace Database\Seeders;

use App\Models\County;
use App\Models\State;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        // $state = State::create(['state' => 'Maryland']);
        // $county = County::create([
        //     'county' => 'Maryland',
        // ]);

        // $county->zipCodes()->createMany([
        //     ['zip_code' => '21122'],
        //     ['zip_code' => '21061'],
        //     ['zip_code' => '21401'],
        //     // ...more zips
        // ]);

        // $state = DB::table('states')->insertGetId([
        //     'state_name' => 'Maryland',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('counties')->insert([
        //     'state_id' => $state,
        //     'county_name' => 'Anne Arundel',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);


    }
}
