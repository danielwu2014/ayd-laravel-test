<?php

namespace App\Http\Middleware;

use App\Repositories\RoleRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user || $this->roleRepository->getRoleName($user->role_id) != 'admin') {
            return redirect('login')->with('error', 'You do not have admin access.');
        }
        return $next($request);
    }
}
