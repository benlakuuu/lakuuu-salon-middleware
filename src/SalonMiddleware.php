<?php

namespace Lakuuu\SalonMiddleware;

use Closure;
use Illuminate\Http\Request;

class SalonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $context = [];

        if (array_key_exists('LAMBDA_REQUEST_CONTEXT', $_SERVER)) {
            $context = json_decode($_SERVER['LAMBDA_REQUEST_CONTEXT'], true);
        }

        $request->merge([
            'user_id' => $context['user_id'] ?? '',
            'salon_id' => $context['salon_id'] ?? '',
        ]);

        return $next($request);
    }
}