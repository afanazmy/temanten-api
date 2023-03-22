<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;

use App\Locales\Language;
use App\Models\SetupWizard;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\SetupWizardRequest;

class SetupWizardController extends Controller
{
    public $language;

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function index(Request $request)
    {
        $result = DB::table('setup_wizards')->where('status', SetupWizard::S_NOTYET);

        if ($request->excepts ?? false) {
            $result->whereNotIn('type', $request->excepts);
        }

        $result = $result->get();

        return response()->json(
            DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result)
        );
    }

    public function store(SetupWizardRequest $request)
    {
        DB::beginTransaction();

        $superadmin = [
            'id' => Str::orderedUuid(),
            'username' => $request->superadmin['username'],
            'password' => Hash::make($request->superadmin['password']),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];
        DB::table('users')->insert($superadmin);

        $userPermissions = DB::table('permissions')
            ->select(['id as permission_id'])
            ->where('is_active', 1)
            ->orderBy('permission_group_id', 'asc')
            ->get()
            ->toJson();
        $userPermissions = json_decode($userPermissions, true);

        foreach ($userPermissions as $key => $value) {
            $userPermissions[$key]['user_id'] = $superadmin['id'];
        }

        DB::table('user_permissions')->insert($userPermissions);

        DB::table('setup_wizards')->where('type', SetupWizard::T_SUPERADMIN)->update([
            'status' => SetupWizard::S_DONE,
            'updated_at' => Date::now()
        ]);

        DB::commit();

        return response()->json(
            DefaultResponse::parse(
                'success',
                $this->language->get(Language::setupWizard['store'], ['appName' => env('APP_NAME')])
            )
        );
    }
}
