<?php

namespace App\Http\Middleware;

use Closure;

class CheckHacienda
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hacienda = $request->route('hacienda');
        if (is_null($hacienda) || ($hacienda != '343' && $hacienda != '344')
        ) {
            return redirect('/');
        }
        return $next($request);
    }
}
