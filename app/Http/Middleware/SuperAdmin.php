<?php

namespace App\Http\Middleware;

use App\Models\users;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\View;

class SuperAdmin
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
            return redirect('/');
        } else {

            $user = users::where('token', session('token'))->first();
            if (empty($user)) {
                session()->flush();
                session()->flash('error', 'Session expired or someone login your account');
                return redirect('/');
            } else {
                $child_ids = [];
                $iterable = [];


                array_push($iterable, $user->id);


                while (is_countable($iterable) && sizeof($iterable) > 0) {

                    $child_ids = array_merge($child_ids, $iterable);

                    try {

                        $users = DB::table("users")->whereIn("parent_id", $iterable)->get();


                        $iterable = [];


                        foreach ($users as $value) {
                            array_push($iterable, $value->id);
                        }
                    } catch (\Throwable $th) {

                        echo $msg = $th->getMessage();
                        break;
                    }
                }




                $child_id = implode(', ', $child_ids);
                $child_id = explode(',', $child_id);
                $rolePermissions = DB::table("role_permission as a")
                    ->select("a.*", "b.permission_name")
                    ->join("permission_mst as b", "a.permission_id", "b.id")
                    ->where("role_id", $user->role_id)->get();

                $headerStore =  DB::table("company")->whereIn("id", explode(", ", $user->inventory_permission))->get();
                View::share('rolePermissions', $rolePermissions);
                View::share('headerStore', $headerStore);
                View::share('active_inventory', $user->active_inventory);
                View::share('panel_permission', $user->panel_permission);
                View::share('active_panel', $user->active_panel);


                $request->merge(["user" => $user, "userIds" => $child_id]);

                return $next($request);
            }
        }
    }
}
