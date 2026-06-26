<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLearnerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('learner')->check()) {
            return redirect('/learn/login');
        }

        return $next($request);
    }
}
