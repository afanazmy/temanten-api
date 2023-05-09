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

    /**
     * Column to display in index and show.
     *
     * @var array
     */
    public $columns = [
        'id', 'username', 'is_active'
    ];

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function userPermissions($id)
    {
        $userPermissions = DB::table('user_permissions')
            ->select(['permission_id'])
            ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
            ->where('is_active', 1)
            ->where('user_id', $id)
            ->pluck('permission_id');

        return $userPermissions;
    }

    public function signin(SigninRequest $request)
    {
        DB::beginTransaction();

        $token = bin2hex(random_bytes(40));
        $user = DB::table('users')->select(['id', 'username', 'token', 'language'])->where('username', $request->username);
        $user->update(['token' => $token]);

        DB::commit();

        $user = $user->first();
        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['signin']), $user));
    }

    public function auth()
    {
        $user = Auth::user();
        $user->permissions = $this->userPermissions($user->id);

        unset($user->token);
        unset($user->password);
        unset($user->is_active);
        unset($user->created_at);
        unset($user->updated_at);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $user));
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
        $query = DB::table('users')->select($this->columns)->where('usename', '!=', 'superadmin');
        $result = $this->filter($request, $query);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = DB::table('users')->select($this->columns)->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->permissions = $this->userPermissions($id);

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

        $userPermissions = [];
        $permissions = $request->permissions ?? [];

        foreach ($permissions as $key => $value) {
            $userPermissions[$key]['user_id'] = $result['id'];
            $userPermissions[$key]['permission_id'] = $value;
        }

        DB::table('user_permissions')->insert($userPermissions);

        DB::commit();

        unset($result['password']);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['store']), $result));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('users')->select($this->columns)->where('id', $id);

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $values = [
            'username' => $request->username,
            'updated_at' => Date::now(),
        ];

        if ($request->has('password')) {
            $values['password'] = Hash::make($request->password);
        }

        $result->update($values);

        DB::table('user_permissions')->where('user_id', $id)->delete();

        $userPermissions = [];
        $permissions = $request->permissions ?? [];

        foreach ($permissions as $key => $value) {
            $userPermissions[$key]['user_id'] = $id;
            $userPermissions[$key]['permission_id'] = $value;
        }

        DB::table('user_permissions')->where('user_id', $id)->delete();
        DB::table('user_permissions')->insert($userPermissions);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['update']), $result));
    }

    public function activate(Request $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('users')->select($this->columns);

        if ($id) {
            $result = $result->where('id', $id);
        }

        if ($request->ids) {
            $result = $result->whereIn('id', $request->ids);
        }

        if (!$result->first() || (!$id && $request->ids)) {
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

        $result = DB::table('users')->select($this->columns);

        if ($id) {
            $result = $result->where('id', $id);
        }

        if ($request->ids) {
            $result = $result->whereIn('id', $request->ids);
        }

        if (!$result->first() || (!$id && $request->ids)) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'token' => null,
            'is_active' => 0,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::user['deactivate']), $result));
    }

    public function permissions()
    {
        $result = DB::table('permission_groups')->select(['id', 'permission_group_name'])->where('is_active', 1)->get();
        $permissions = DB::table('permissions')->select(['id', 'permission_group_id', 'permission_label'])->where('is_active', 1)->get();

        foreach ($result as $permissionGroup) {
            $permissionGroup->permission_group_name = json_decode($permissionGroup->permission_group_name);
            $permissionGroup->permissions = $permissions
                ->where('permission_group_id', $permissionGroup->id)
                ->each(function ($item) {
                    $item->permission_label = json_decode($item->permission_label);
                })
                ->values()
                ->all();
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }
}
