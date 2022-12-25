<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Factory as Auth;

use App\Locales\Language;
use App\Http\Responses\DefaultResponse;

use Closure;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            $language = new Language();
            $message = $language->get(Language::user['unauthenticated']);

            if (!$request->expectsJson()) {
                return response($message, 401);
            }

            return response()->json(DefaultResponse::parse('failed', $message), 401);
        }

        return $next($request);
    }
}
