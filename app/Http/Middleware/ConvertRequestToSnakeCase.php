<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Closure;

class ConvertRequestToSnakeCase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $formatted = $this->convertSnakeCase($request->all());

        $request->replace($formatted);

        return $next($request);
    }

    /**
     * Convert array or object key camelCase to snake_case
     *
     * @param array $params
     * @return array $formatted
     */
    public function convertSnakeCase($params)
    {
        $formatted = [];
        foreach ($params as $key => $value) {
            $newKey = Str::snake($key);
            if (is_array($value)) {
                $formatted[$newKey] = Self::convertSnakeCase($value);
            } else {
                $formatted[$newKey] = $value;
            }
        }
        return $formatted;
    }
}
