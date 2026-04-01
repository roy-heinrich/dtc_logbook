<?php

namespace Database\Seeders;

use App\Models\RegUser;
use Illuminate\Database\Seeder;

class RegUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RegUser::firstOrCreate(
            ['lname_user' => 'Santos'],
            [
                'fname_user' => 'Juan',
                'mname_user' => 'Cruz',
                'suffix_user' => 'Jr',
                'birthdate' => '1990-05-15',
                'sex_user' => 'M',
                'sector_user' => 'Information Technology',
                'number_user' => '09171234567',
            ]
        );

        RegUser::firstOrCreate(
            ['lname_user' => 'Garcia'],
            [
                'fname_user' => 'Maria',
                'mname_user' => 'Rosario',
                'suffix_user' => null,
                'birthdate' => '1995-08-22',
                'sex_user' => 'F',
                'sector_user' => 'Engineering',
                'number_user' => '09187654321',
            ]
        );

        RegUser::firstOrCreate(
            ['lname_user' => 'Reyes'],
            [
                'fname_user' => 'Carlos',
                'mname_user' => 'Antonia',
                'suffix_user' => 'Sr',
                'birthdate' => '1988-03-10',
                'sex_user' => 'M',
                'sector_user' => 'Finance',
                'number_user' => '09195551234',
            ]
        );
    }
}
