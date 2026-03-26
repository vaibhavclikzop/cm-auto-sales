<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Jenssegers\Agent\Agent;

class leadAppController extends Controller
{
    public function leadApp(Request $request)
    {
        return view("lead-app.login");
    }


    public function saveLeadLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            session()->flash("error", "Enter username or password");
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        try {
            $superAdmin =   DB::table("users")
                ->where("user_name", $request->username)
                ->where("password", $request->password)
                ->first();
            if (!empty($superAdmin)) {
                $token = bin2hex(random_bytes(16));
                $agent = new Agent();
                $browser = $agent->browser();
                $version = $agent->version($browser);
                $platform = $agent->platform();
                DB::table('users')->where("id", $superAdmin->id)->update(array(
                    'token' => $token,
                    "last_ip" => $_SERVER['REMOTE_ADDR'],
                    'last_login' => date("Y-m-d H:m:s"),
                    'platform' => $browser . " / " . $version . ' / ' . $platform,
                ));
                session()->put('token', $token);
                session()->put('user', $superAdmin);
            } else {
                return redirect()->back()->with('error', "Incorrect Username or Password");
            }
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }
        session()->flash('success', "login successfully");

        return redirect("lead-app/dashboard");
    }


    public function dashboard(Request $request)
    {

        $user = DB::table("users")->where("id", $request->user->id)->first();
        $totalLeads = DB::table('lead')->whereIn("user_id", $request->userIds)->count();
        $leadStatusCount = DB::table('status as a')
            ->leftJoin('lead as b', function ($join) use ($request) {
                $join->on('a.id', '=', 'b.status')
                    ->whereIn('b.user_id', $request->userIds);
            })
            ->select(
                'a.id',
                'a.name as status',
                DB::raw('COUNT(b.id) as lead_count'),
                DB::raw($totalLeads . ' as total_lead')
            )
            ->groupBy('a.id', 'a.name')
            ->get();
        $monthlyLeadsRaw = DB::table('lead')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(id) as total')
            )
            ->whereIn('user_id', $request->userIds)
            ->whereYear('created_at', date('Y')) // current year
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month'); // [month => count]
        $monthlyLeads = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyLeads[] = $monthlyLeadsRaw[$m] ?? 0;
        }

 

        return view("lead-app.dashboard", compact("user", "leadStatusCount", "totalLeads", "monthlyLeads"));
    }

    public function addLead(Request $request)
    {

        return view("lead-app.add-lead");
    }

    public function saveLead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|min:10|max:10',

            'remarks' => 'required',
            'status' => 'required|integer',

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
        $lead_id = 0;
        try {
            if (empty($request->id)) {
                $lead_id =    DB::table('lead')->insertGetId(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "classification" => $request->classification,
                    "status" => $request->status,
                    "remind_date" => $request->remind_date,
                    "remind_time" => $request->remind_time,
                    "remarks" => $request->remarks,
                    "user_id" => $request->user->id,
                    "store_id" => $request->user->active_inventory,

                ));
            } else {
                DB::table('lead')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,

                    "classification" => $request->classification,
                    "status" => $request->status,
                    "remind_date" => $request->remind_date,
                    "remind_time" => $request->remind_time,
                    "remarks" => $request->remarks,
                    "user_id" => $request->user->id,
                    "store_id" => $request->user->active_inventory,

                ));

                $lead_id = $request->id;
            }

            DB::table('lead_remarks')->insertGetId(array(

                "lead_id" => $lead_id,
                "status" => $request->status,
                "remind_date" => $request->remind_date,
                "remind_time" => $request->remind_time,
                "remarks" => $request->remarks,
                "user_id" => $request->user->id,

            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect("lead-app/leads")->with("success", "Save Successfully");
    }

    public function leads(Request $request)
    {
        $lead = DB::table("lead as a")
            ->select("a.*", "c.name as status_name", "d.name as user_name")

            ->join("status as c", "a.status", "c.id")
            ->join("users as d", "a.user_id", "d.id");

        $data = $lead->where("a.store_id", $request->user->active_inventory)
            ->where("a.user_id", $request->user->id)
            ->orderBy("a.id", "desc")
            ->get();


        return view("lead-app.leads", compact("data"));
    }

    public function createOrder(Request $request)
    {
        $customer_type = DB::table("customer_type")->get();
        $brand = DB::table("brand")->get();
        return view("lead-app.create-order", compact("customer_type", "brand"));
    }

    public function orders(Request $request)
    {
        $status = request("status");

        $order = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer", "c.name as user", "b.company")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")
            ->where("a.company_id", $request->user->active_inventory)
            ->whereIn("a.user_id", $request->userIds);
        if ($status) {
            $order->where("a.status", $status);
        }
        $orders =  $order->orderBy("id", "desc")
            ->get();
        return view("lead-app/orders", compact("orders"));
    }

    public function OrderView(Request $request, $id)
    {

        if (empty($id)) {
            return  redirect()->back()->with("error", "ID not found");
        }

        $status = request("status");

        $data = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer_name", "b.number", "b.email", "b.gst", "b.address", "b.state", "b.city", "b.pincode", "c.img", "c.name", "c.gst_no", "c.email as c_email", "c.number as c_number", "c.address as c_address", "b.company as company_name", "a.created_at", "c.state as c_state")
            ->join("customers as b", "a.customer_id", "b.id", "c.*")
            ->join("company as c", "a.company_id", "c.id")
            ->where("a.id", $id)
            ->first();

        $od = DB::table("order_det as a")
            ->select(
                "b.name as product",
                "b.part_no",
                "a.qty",
                "a.price",
                "b.part_no as part_code",
                "a.discount as discount",
                "b.hsn_code",
                "c.name as brand"
            )
            ->join("order_mst as f", "a.mst_id", "f.id")
            ->join("products as b", "a.product_id", "b.id")
            ->join("brand as c", "b.brand_id", "c.id");
        if ($status == "pending") {
            $od->addSelect(DB::raw("(a.qty - a.out_qty) as qty"));
            $od->whereColumn("a.qty", ">", "a.out_qty");
        }
        $od->where("a.mst_id", $id);

        $order_det = $od->get();




        return view("lead-app.order-view", compact("data", "order_det"));
    }

    public function myProfile(Request $request)
    {

        $data =  users::with("role")->where("id", $request->user->id)->first();
        return view("lead-app.my-profile", compact("data"));
    }

    public function logout(Request $request)
    {

        DB::table('mode_of_transport')->where("token", session("token"))->update(array(
            'token' => "",

        ));
        return redirect("lead-app")->with("success", "logout successfully");
    }
}
