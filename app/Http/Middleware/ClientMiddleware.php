<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientMiddleware
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
        if (auth('api')->check() && in_array(auth('api')->user()->user_type, ['client'])) {
            return $next($request);
        } elseif (auth('api')->check() &&  auth('api')->user()->is_active  == 0) {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.messages.account_deactive')], 403);
        } elseif (auth('api')->check() &&  auth('api')->user()->is_ban) {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.messages.account_blocked')], 403);
        } else {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.auth.failed')], 401);
        }
    }
}
