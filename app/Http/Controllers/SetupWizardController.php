<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Date;

use App\Locales\Language;
use App\Models\SetupWizard;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\SetupWizardRequest;

class SetupWizardController extends Controller
{
    public function store(SetupWizardRequest $request)
    {
        DB::beginTransaction();

        DB::table('users')->insert([
            'id' => Str::orderedUuid(),
            'username' => $request->superadmin['username'],
            'password' => Hash::make($request->superadmin['password']),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);

        DB::table('setup_wizards')->where('type', SetupWizard::T_SUPERADMIN)->update([
            'status' => SetupWizard::S_DONE,
            'updated_at' => Date::now()
        ]);

        DB::commit();

        $language = new Language();
        return response()->json(DefaultResponse::parse('success', $language->get(Language::setupWizard['store']), null));
    }
}
