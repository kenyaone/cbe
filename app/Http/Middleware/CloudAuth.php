<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CloudAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/admin/login')->with('message', 'Admin authentication required');
        }

        if (Auth::user()->role !== 'admin') {
            return response('Unauthorized', 403);
        }

        return $next($request);
    }
}
