<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

use App\Traits\Filter;
use App\Traits\HasAuth;
use App\Locales\Language;
use App\Http\Requests\SigninRequest;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    use HasAuth, Filter;

    public $language;

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function signin(SigninRequest $request)
    {
        DB::beginTransaction();

        $generateToken = bin2hex(random_bytes(40));
        $user = DB::table('users')->select(['id', 'username', 'token', 'language'])->where('username', $request->username);
        $user->update(['token' => $generateToken]);

        DB::commit();

        $user = $user->first();
        $user->permissions = DB::table('user_permissions')
            ->select(['permission_id'])
            ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->where('is_active', 1)
            ->where('user_id', $user->id)
            ->pluck('permission_id');

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['signin']), $user));
    }

    public function signout(Request $request)
    {
        DB::beginTransaction();

        $token = $this->token($request);
        DB::table('users')->where('token', $token)->update(['token' => null]);

        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['signout']), null));
    }

    public function index(Request $request)
    {
        $result = DB::table('users')->select(['id', 'username', 'is_active']);
        $result = $this->filter($request, $result);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = DB::table('users')->select(['id', 'username', 'is_active'])->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['notFound']), null), 404);
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['found']), $result));
    }

    public function store(StoreUserRequest $request)
    {
        $result = [
            'id' => Str::orderedUuid(),
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];

        DB::beginTransaction();
        DB::table('users')->insert($result);
        DB::commit();

        unset($result['password']);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['store']), $result));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('users')->select(['id', 'username', 'is_active'])->where('id', $id);

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['update']), $result));
    }

    public function activate(Request $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('users')->select(['id', 'username', 'is_active'])->where('id', $id);

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'is_active' => 1,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['activate']), $result));
    }

    public function deactivate(Request $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('users')->select(['id', 'username', 'is_active'])->where('id', $id);

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'is_active' => 0,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['deactivate']), $result));
    }
}
