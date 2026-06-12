<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->role != 'admin'){
            return redirect('/')->with('error','Access denied');
        }

        return $next($request);
    }
}