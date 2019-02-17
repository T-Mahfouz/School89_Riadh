<?php

namespace App\Http\Middleware;

use App\BlockedUser;
use Closure;
use Illuminate\Support\Facades\Auth;

class BlockedMiddleware
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
        $blocked = BlockedUser::where('user_id',Auth::user()->id)->first();

        if($blocked )
        {
            $blockedId = $blocked->user_id;
            if($blockedId == Auth::user()->id)
            {
                if($request->wantsJson())
                    return response()->json([
                        'status_code' => 401,
                        'message' => 'Unauthorized, user blocked.',
                        'data' => []
                    ],401);

                Auth::guard()->logout();
                $request->session()->invalidate();
                return redirect('/');
            }
            
        }
        
        return $next($request);
    }
}
