<?php

namespace Lakuuu\SalonMiddleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SalonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $data = [];

        if (array_key_exists('LAMBDA_REQUEST_CONTEXT', $_SERVER)) {
            $context = json_decode($_SERVER['LAMBDA_REQUEST_CONTEXT'], true);
            $data = Arr::get($context, 'authorizer.lambda');
        }

        if (env('APP_ENV') === 'local') {
            $data = [
                'user_id' => $request->header('user-id'),
                'salon_id' => $request->header('salon-id')
            ];
        }

        $request->merge([
            'salon_user_id' => $data['user_id'] ?? '',
            'salon_id' => $data['salon_id'] ?? '',
        ]);

        return $next($request);
    }
}