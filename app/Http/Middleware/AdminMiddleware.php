<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        if(Auth::user()->role->name == 'admin')
            return $next($request);
            
        else
        {
            if($request->wantsJson())
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized.',
                'data' => []
            ],200);

            return redirect('/home');
        }

    }
}
