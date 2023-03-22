<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class PersonalAccessClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $personalAccessClient = DB::table('personal_access_clients')->first();
        if ($personalAccessClient) return;

        $personalAccessClients = [
            [
                'id' => Str::orderedUuid(),
                'name' => 'Invitation App',
                'token' => bin2hex(random_bytes(40)),
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'QR Code Scanner App',
                'token' => bin2hex(random_bytes(40)),
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ],
        ];

        DB::table('personal_access_clients')->insert($personalAccessClients);
    }
}
