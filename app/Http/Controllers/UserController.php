<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Traits\HasAuth;
use App\Locales\Language;
use Illuminate\Http\Request;
use App\Http\Requests\SigninRequest;
use App\Http\Responses\DefaultResponse;

class UserController extends Controller
{
    use HasAuth;

    public function signin(SigninRequest $request)
    {
        DB::beginTransaction();

        $generateToken = bin2hex(random_bytes(40));
        $user = DB::table('users')->where('username', $request->username);
        $user->update(['token' => $generateToken]);

        DB::commit();

        $language = new Language();
        return response()->json(DefaultResponse::parse('success', $language->get(Language::user['signin']), $user->first()));
    }

    public function signout(Request $request)
    {
        DB::beginTransaction();

        $token = $this->token($request);
        DB::table('users')->where('token', $token)->update(['token' => null]);

        DB::commit();

        $language = new Language();
        return response()->json(DefaultResponse::parse('success', $language->get(Language::user['signout']), null));
    }
}
