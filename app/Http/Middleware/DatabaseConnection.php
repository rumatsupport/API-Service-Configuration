<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class DatabaseConnection
{

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
        try {
            DB::connection()->getPdo();
            return $next($request);
        } catch (\PDOException $e) {
            $status   = 500;
            $message  = $e->getMessage();

            $jsonData = [
                'status' => $status,
                'data' => [],
                'message' => $message
            ];
            return response()->json($jsonData,$status);
        }
    }
}
