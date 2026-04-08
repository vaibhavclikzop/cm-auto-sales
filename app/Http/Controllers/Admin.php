<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;

class Admin extends Controller
{
    public function Dashboard(Request $request)
    {


        $customers = DB::table("customers")->get()->count();
        $vendor = DB::table("vendor")->get()->count();


        $gen_set_mst = DB::table("products")->get()->count();


        $total_delivered = DB::table("order_mst")->where("status", "delivered")->get()->count();
        $total_pending = DB::table("order_mst")->where("status", "pending")->get()->count();
        $this_month_completed = DB::table("order_mst")->where("status", "complete")->get()->count();
        $this_month_delivered = DB::table("order_mst")->where("status", "delivered")->whereMonth("delivery_date", now())->get()->count();


        $products = DB::table("products")->get()->count();

        $order_mst = DB::table("order_mst")->get()->count();


        $minimum_stock = DB::table("current_stock as a")
            ->select("a.*", "b.name as product", "c.name as location", "b.min_stock")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("store as c", "a.location_id", "=", "c.id")
            ->whereRaw("a.stock <= b.min_stock")->get()->count();



        $total_sale_amt = DB::table("order_det")->select(DB::raw("SUM(price * qty) as total_purchase_amt"))
            ->value('total_sale_amt');


        $recent_order = DB::table("order_mst as a")
            ->select("a.*", "c.name as customer")
            ->join("customers as c", "a.customer_id", "c.id")
            ->orderby("id", "desc")->limit(4)->get();

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $complete = DB::table('order_mst')
            ->selectRaw('DATE_FORMAT(created_at, "%M") as month_name, COUNT(*) as total')
            ->where('status', 'complete')
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%M")'))
            ->pluck('total', 'month_name');

        // Fill the months that are not in the database with 0
        $completeResult = collect($months)->mapWithKeys(function ($month) use ($complete) {
            return [$month => $complete->get($month, 0)];
        });


        $delivered = DB::table('order_mst')
            ->selectRaw('DATE_FORMAT(delivery_date, "%M") as month_name, COUNT(*) as total')
            ->where('status', 'delivered')
            ->whereYear('delivery_date', now()->year)
            ->groupBy(DB::raw('DATE_FORMAT(delivery_date, "%M")'))
            ->pluck('total', 'month_name');

        $delivered_result = collect($months)->mapWithKeys(function ($month) use ($delivered) {
            return [$month => $delivered->get($month, 0)];
        });



        $lead_count = DB::table("status as a")
            ->select("a.name", "a.id", DB::raw("count(b.id) as total"))
            ->leftJoin("lead as b", "a.id", "b.status")
            ->groupBy("a.id", "a.name")

            ->get();
        return view("dashboard", compact("customers", "vendor", "products", "order_mst", "total_delivered", "total_pending", "this_month_completed", "this_month_delivered", "recent_order",  "minimum_stock", "months", "delivered_result", "completeResult", "lead_count"));
    }

    public function Users(Request $request)
    {
        $store = DB::table("store")->get();
        $company = DB::table("company")->get();

        $users = DB::table("users as a")
            ->select("a.*", "b.name as user_type")
            ->join("role as b", "a.role_id", "b.id")
            ->where("user_type", "!=", "admin")
            ->whereIn("a.id", $request->userIds)
            ->get();


        $role = DB::table("role")->where("name", "!=", "admin")->get();
        $parents = DB::table("users")->whereIn("id", $request->userIds)->get();
        return view("users", compact("users", "role", "store", "parents", "company"));
    }
    public function SaveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'email' => 'required',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);
                $count++;
            }
        }


        try {
            if (empty($request->id)) {
                DB::table('users')->insertGetId(array(
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "role_id" => $request->role_id,
                    "password" => $request->password,
                    "parent_id" => $request->parent_id,
                    "user_name" => $request->user_name,

                    "inventory_permission" => implode(', ', $request->inventory_permission),
                    "panel_permission" => implode(', ', $request->panel_permission)


                ));
            } else {
                DB::table('users')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "role_id" => $request->role_id,
                    "password" => $request->password,
                    "parent_id" => $request->parent_id,

                    "inventory_permission" => implode(', ', $request->inventory_permission),
                    "panel_permission" => implode(', ', $request->panel_permission)
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function UserRole(Request $request)
    {
        $role = DB::table("role")->where("name", "!=", "admin")->get();
        // $role = DB::table("role")->get();
        return view("user-role", compact("role"));
    }

    public function SaveRole(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }

        try {
            if (empty($request->id)) {
                DB::table('role')->insertGetId(array(
                    "name" => $request->name,
                ));
            } else {
                DB::table('role')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function UserPermission(Request $request, $id)
    {

        $role = DB::table("role")->where("id", $id)->first();


        $permission_mst = DB::table("permission_mst as a")
            ->select("a.*")
            ->whereNotExists(function ($query) use ($role) {
                $query->select(DB::raw(1))
                    ->from("role_permission as b")
                    ->whereColumn("b.permission_id", "a.id")
                    ->where("b.role_id", $role->id);
            })
            ->get();



        $role_permission = DB::table("role_permission as a")
            ->select("a.*", "b.name as permission")
            ->join("permission_mst as b", "a.permission_id", "b.id")
            ->where("a.role_id", $role->id)
            ->get();

        return view("user-permission", compact("role", "permission_mst", "role_permission", "id"));
    }

    public function SaveUserPermission(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
            'permission_id' => 'required',
            'view' => 'required',
            'edit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }

        $role_permission = DB::table("role_permission")->where("role_id", $request->role_id)->where("permission_id", $request->permission_id)->first();
        if ($role_permission) {
            return  redirect()->back()->with("error", "User permission already added");
        }
        try {
            if (empty($request->id)) {
                DB::table('role_permission')->insertGetId(array(
                    "role_id" => $request->role_id,
                    "permission_id" => $request->permission_id,
                    "edit" => $request->edit,
                    "view" => $request->view,
                    "create" => $request->create,
                    "del" => $request->del,
                ));
            } else {
                DB::table('role_permission')->where("id", $request->id)->update(array(

                    "edit" => $request->edit,
                    "view" => $request->view,
                    "create" => $request->create,
                    "del" => $request->del,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function RemovePermission(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',

        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }

        DB::table('role_permission')->where("id", $request->id)->delete();
        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function Profile(Request $request)
    {
        $user = DB::table("users")->where("id", $request->user->id)->first();
        return view("profile", compact("user"));
    }

    public function SaveProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'email' => 'required',
            'phone' => 'required|min:10|max:10',
            'address' => 'required',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }
        DB::table('users')->where("id", $request->user->id)->update(array(
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "address" => $request->address,
            "password" => $request->password,

        ));
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function updateActiveInventory(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'active_inventory' => 'required',

        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }


        try {

            DB::table('users')->where("id", $request->user->id)->update(array(

                "active_inventory" => $request->active_inventory,

            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function updateActivePanel(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'active_panel' => 'required',


        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with('error', $error);

                $count++;
            }
        }
        DB::table('users')->where("id", $request->user->id)->update(array(
            "active_panel" => $request->active_panel,


        ));
        return  redirect()->back()->with("success", "Save Successfully");
    }
}
