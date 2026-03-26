<?php

namespace App\Http\Middleware;

use App\Models\company;
use App\Models\mode_of_transport;
use App\Models\users;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class salesAppMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty(session('token'))) {
            session()->flash('error', 'Session expired');
            return redirect("sales-app");
        } else {

            $user = mode_of_transport::where('token', session('token'))->first();

            if (empty($user)) {
                session()->flush();
                session()->flash('error', 'Session expired or someone login your account');
                return redirect("sales-app");
            } else {



                $request->merge(["user" => $user]);

                return $next($request);
            }
        }
    }
}
