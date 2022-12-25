<?php

namespace App\Http\Middleware;

use App\Http\Responses\DefaultResponse;
use App\Locales\Language;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        // Pre-Middleware Action
        $permission = explode(",", $permission);
        $permissions = DB::table('user_permissions')->select(['permission_id'])
            ->where('user_id', Auth::user()->id ?? null)
            ->pluck('permission_id')
            ->toArray();
        $containsAllValues = !array_diff($permission, $permissions);

        if (!$containsAllValues) {
            $language = new Language(Auth::user());
            return response()->json(DefaultResponse::parse('failed', $language->get(Language::user['unauthenticated'])), 403);
        }

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
