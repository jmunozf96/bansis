<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckRecursoPerfil
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
        $recursoID = $request->route('idRecurso');
        $valida_recurso = DB::table('SEG_PERFILES as per')
            ->join('SEG_RECURSOS as rec', 'per.RecursoID', '=', 'rec.ID')
            ->where('per.recursoID', '=', $recursoID)
            ->where('per.id', '=', Auth::user()->ID)->first();

        if (is_null($valida_recurso) || empty($valida_recurso)) {
            return redirect('/');
        }

        return $next($request);
    }
}
