<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next)
    {
        // contoh: kalau pakai kolom 'role' di users
        if (!auth()->check() || auth()->user()->role !== 'customer') {
            // bisa redirect ke login / abort 403
            return redirect()->route('login');
            // atau: abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
