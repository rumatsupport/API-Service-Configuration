<?php

namespace App\Http\Middleware;

use Closure;

class BasicToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $basic = $request->header('Basic');
        $jsonData['status'] = 401;
        $jsonData['data'] = null;
        $jsonData['message'] = "Unauthorized";

        if (empty($basic)) {
            return response()->json($jsonData, $jsonData['status']);
        }

        if($basic != env('BASIC_BEARER')) {
            return response()->json($jsonData, $jsonData['status']);
        }

        return $next($request);
    }
}
