<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasAuth
{
    /**
     * Get user token.
     *
     * @param Request $request
     * @return string
     */
    public function token(Request $request)
    {
        $token = $request->header('Authorization') ?? $request->input('token') ?? null;
        return $token;
    }
}
