<?php

namespace App\Http\Middleware;

use App\Http\Responses\DefaultResponse;
use App\Locales\Language;
use App\Traits\HasAuth;
use Closure;
use Illuminate\Support\Facades\DB;

class AppsMiddleware
{
    use HasAuth;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $this->token($request);
        $apps = DB::table('personal_access_clients')->where('token', $token)->first();

        if ($apps) {
            $response = $next($request);
            return $response;
        }

        $language = new Language();
        $message = $language->get(Language::user['unauthenticated']);

        if (!$request->expectsJson()) {
            return response($message, 401);
        }

        return response()->json(DefaultResponse::parse('failed', $message), 401);
    }
}
