<?php

namespace Lakuuu\SalonMiddleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SalonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $data = [];

        if (array_key_exists('LAMBDA_REQUEST_CONTEXT', $_SERVER)) {
            $context = json_decode($_SERVER['LAMBDA_REQUEST_CONTEXT'], true);
            $data = Arr::get($context, 'authorizer.lambda');
        }

        if (env('APP_LAKUUU_AUTHORIZER_URL') !== '') {
            $userRequest = Http::post(env('APP_LAKUUU_AUTHORIZER_URL'), [
                'access_token' => $request->header('Authorization')
            ]);
            $userRequest->throw();
            $userResponse = $userRequest->json();

            $data = [
                'user_id' => $userResponse['id'],
                'salon_id' => $userResponse['salon_id'],
            ];
        }

        if (env('APP_ENV') !== 'staging' && env('APP_ENV') !== 'production') {
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
