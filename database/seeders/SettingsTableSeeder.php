<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = DB::table('settings')->first();

        if (env('APP_ENV') !== 'local' && $setting) return;

        $setting = [
            [
                'name' => 'bride',
                'value' => null,
            ],
            [
                'name' => 'groom',
                'value' => null,
            ],
            [
                'name' => 'bride_nickname',
                'value' => null,
            ],
            [
                'name' => 'groom_nickname',
                'value' => null,
            ],
            [
                'name' => 'bride_father',
                'value' => null,
            ],
            [
                'name' => 'bride_mother',
                'value' => null,
            ],
            [
                'name' => 'groom_father',
                'value' => null,
            ],
            [
                'name' => 'groom_mother',
                'value' => null,
            ],
            [
                'name' => 'akad_datetime',
                'value' => null,
            ],
            [
                'name' => 'akad_place',
                'value' => null,
            ],
            [
                'name' => 'akad_map',
                'value' => null,
            ],
            [
                'name' => 'reception_datetime',
                'value' => null,
            ],
            [
                'name' => 'reception_place',
                'value' => null,
            ],
            [
                'name' => 'reception_maps',
                'value' => null,
            ],
            [
                'name' => 'dresscode',
                'value' => null,
            ],
            [
                'name' => 'invitation_wording',
                'value' => null
            ],
            [
                'name' => 'variables',
                'value' => '%recipient%,%bride%,%groom%,%invitation_link%,%bride_nickname%,%groom_nickname%'
            ]
        ];

        DB::table('settings')->insert($setting);
    }
}
