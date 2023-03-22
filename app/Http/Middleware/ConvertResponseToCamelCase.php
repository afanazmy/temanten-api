<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Closure;

class ConvertResponseToCamelCase
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
        $response = $next($request);
        $content = $response->getContent();

        if ($content) {
            try {
                $array = json_decode($content, true);
                $array = $this->convertCamelCase($array);
                $response->setContent(json_encode($array));
            } catch (\Exception $e) {
                // you can log an error here if you want
            }
        }

        return $response;
    }

    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Convert array or object key snake_case to camelCase
     *
     * @param array $params
     * @return array $formatted
     */
    public function convertCamelCase($params)
    {
        $formatted = [];
        foreach ($params as $key => $value) {
            $newKey = $key;

            if (!Str::contains($key, "-")) {
                $newKey = Str::camel($key);
            }

            if (is_array($value)) {
                $formatted[$newKey] = Self::convertCamelCase($value);
            } else {
                $newKey = Str::contains($newKey, '_')
                    ? substr_replace($newKey, '', -1)
                    : $newKey;

                $formatted[$newKey] = $this->isJson($value) ? json_decode($value) : $value;
            }
        }
        return $formatted;
    }
}
