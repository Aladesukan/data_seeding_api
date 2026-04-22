<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\str;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/profiles.json'));
        $profiles = json_decode($json, true);

        foreach ($profiles as $profile) {
            DB::table('profiles')->updateOrInsert(
                ['name' => $profile['name']], // prevents duplicates
                [
                    'id' => Str::uuid(),
                    'gender' => $profile['gender'],
                    'gender_probability' => $profile['gender_probability'],
                    'age' => $profile['age'],
                    'age_group' => $profile['age_group'],
                    'country_id' => $profile['country_id'],
                    'country_name' => $profile['country_name'],
                    'country_probability' => $profile['country_probability'],
                    'created_at' => now()
                ]
            );
        }
    }
}
