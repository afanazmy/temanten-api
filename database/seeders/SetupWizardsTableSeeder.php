<?php

namespace Database\Seeders;

use App\Models\SetupWizard;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class SetupWizardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = DB::table('setup_wizards')->where('type', SetupWizard::T_SUPERADMIN)->first() ?? null;
        if ($superadmin) return;

        DB::table('setup_wizards')->insert([
            'id' => Str::orderedUuid(),
            'step' => 1,
            'name' => json_encode(['en-US' => 'Add Superadmin', 'id-ID' => 'Tambah Superadmin']),
            'type' => SetupWizard::T_SUPERADMIN,
            'status' => SetupWizard::S_NOTYET,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);
    }
}
