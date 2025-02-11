<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $admin = Role::where('id',$user->role_id)->first();

        if ($admin->name !== 'admin') {
            return response()->json(['message' => 'Unauthorized for mengakses/manipulate role data'], 403);
        }
        return $next($request);
    }
}
