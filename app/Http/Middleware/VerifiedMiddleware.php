<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $verified = false;
        if($user->status == 1)
            $verified = true;
        if(!$verified)
        {
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Unauthorized, user not verified.',
                    'data' => []
                ],401);
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('/');
        }
        

        
        return $next($request);
    }
}
