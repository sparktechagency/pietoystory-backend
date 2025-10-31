<?php

namespace Database\Seeders;

use App\Models\County;
use App\Models\State;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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


        User::create([
            'full_name' => 'User one',
            'email' => 'user.one@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'status' => 'active',
            'parent_referral_code' => strtoupper(Str::random(8)),
        ]);

        User::create([
            'full_name' => 'User two',
            'email' => 'user.two@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'status' => 'active',
            'parent_referral_code' => strtoupper(Str::random(8)),
        ]);


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
