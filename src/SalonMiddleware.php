<?php

namespace Lakuuu\SalonMiddleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        if (!empty(env('APP_LAKUUU_AUTHORIZER_URL'))) {
            $userRequest = Http::withHeaders([
                'Authorization' => $request->header('Authorization')
            ])->get(env('APP_LAKUUU_AUTHORIZER_URL'));

            if ($userRequest->clientError()) {
                return new Response('Unauthorized', 403);
            }

            if ($userRequest->serverError()) {
                return new Response('Lakuuu Authorizer not Available', 500);
            }

            $userResponse = $userRequest->json();

            $data = [
                'user_id' => $userResponse['id'] ?? '',
                'client_id' => $userResponse['client_id'] ?? '',
                'salon_id' => $userResponse['salon_id'] ?? '',
            ];
        }

        if (env('APP_ENV') !== 'staging' && env('APP_ENV') !== 'production') {
            $data = [
                'user_id' => $request->header('user-id') ?? '',
                'client_id' => $request->header('client-id') ?? '',
                'salon_id' => $request->header('salon-id') ?? ''
            ];
        }

        $request->merge([
            'salon_user_id' => $data['user_id'] ?? ($request->get('user_id', '')),
            'client_id' => $data['client_id'] ?? ($request->get('client_id', '')),
            'salon_id' => $data['salon_id'] ?? ($request->get('salon_id', '')),
        ]);

        return $next($request);
    }
}
