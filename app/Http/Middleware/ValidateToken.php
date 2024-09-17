<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

// helpers
use App\Helpers\CurlHelpers;

class ValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = \str_replace("Bearer ","",$request->header('Authorization'));
        $basic = $request->header('Basic');
        try{
            if(!empty($token)){
                $validate = $this->doValidate($token);

                $request->merge(['scope'=>$validate]);

                return $next($request);
            }

            // only for communication from the auth service
            if ($basic == env('BASIC_BEARER')) {
                return $next($request);
            } else {
                return response()->json(['status' => 401, 'data' => [], 'message' => 'Unauthorized'],401);
            }
        } catch(\Exception $e){
            return response()->json(['status' => $e->getCode() ? $e->getCode() : 500, 'data' => [], 'message' => $e->getMessage()],$e->getCode() ? $e->getCode() : 500);
        }
    }

    private function doValidate($token){
        $baseAuth = env('SERVICE_AUTH','');
        $url = $baseAuth.URL_AUTH['validateToken'];

        $response = CurlHelpers::get($url, [], $token);

        if ($response['status'] != 200) {
            throw new \Exception($response['data']->message, $response['status']);
        } else {
            return $response['data']->data;
        }
    }
}
