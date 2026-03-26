<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\architect;
use App\Models\beam_angle;
use App\Models\cct;
use App\Models\complain_mst;
use App\Models\customers;
use App\Models\electrician;
use App\Models\fixture_color;
use App\Models\fp_category;
use App\Models\fp_sub_category;
use App\Models\lead_comments;
use App\Models\lead_product;
use App\Models\leads;
use App\Models\property_stage;
use App\Models\quote_mst;
use App\Models\r_color;
use App\Models\series_name;
use App\Models\sources;
use App\Models\status;
use App\Models\users;
use App\Models\wattage;
use App\Services\SmsService;
use App\Services\SmsServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use SendGrid\Mail\Cc;

class saleAppController extends Controller
{


    public function dashboard(Request $request)
    {
        $user_id = $request->user->id;
        $user =   DB::table('mode_of_transport')->where("id", $user_id)->first();

        $RFD =  DB::table("stock_outward_mst")
            ->where("transport_id", $request->user->id)
            ->where("dispatch_status", "final")
            ->where("status", "!=", "delivered")
            ->count();
        $delivered =  DB::table("stock_outward_mst")
            ->where("transport_id", $request->user->id)
            ->where("dispatch_status", "final")
            ->where("status", "=", "delivered")
            ->count();

        $year = now()->year;

        $monthlyData = DB::table("stock_outward_mst")
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->where("transport_id", $request->user->id)
            ->where("dispatch_status", "final")
            ->where("status", "delivered")
            ->whereYear("created_at", $year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        /* Prepare data for all 12 months */
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = $monthlyData[$m] ?? 0;
        }

        return view("salesapp.dashboard", compact("user", "RFD", "delivered", "chartData"));
    }

    public function salesAppLogin(Request $request)
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
            $superAdmin =   DB::table("mode_of_transport")
                ->where("user_name", $request->username)
                ->where("password", $request->password)
                ->first();
            if (!empty($superAdmin)) {
                $token = bin2hex(random_bytes(16));
                $agent = new Agent();
                $browser = $agent->browser();
                $version = $agent->version($browser);
                $platform = $agent->platform();
                DB::table('mode_of_transport')->where("id", $superAdmin->id)->update(array(
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

        return redirect("sales-app/dashboard");
    }

    public function salesApp(Request $request)
    {

        if (!empty(session("token"))) {
            $superAdmin =   DB::table("mode_of_transport")->where("token", session("token"))->first();
            if (!empty($superAdmin)) {
                return redirect("sales-app/dashboard");
            }
        }
        return view("salesapp.login");
    }








    public function logout(Request $request)
    {

        DB::table('mode_of_transport')->where("token", session("token"))->update(array(
            'token' => "",

        ));
        return redirect("sales-app")->with("success", "logout successfully");
    }


    public function myProfile(Request $request)
    {
        $data =  users::with("role")->where("id", $request->user->id)->first();
        return view("salesapp.my-profile", compact("data"));
    }

    public function updateProfile(Request $request)
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

            DB::table('users')->where("id", $request->user->id)->update(array(
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "address" => $request->address,
                "state" => $request->state,
                "city" => $request->city,
                "pincode" => $request->pincode,
                "password" => $request->password,

            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function readyToDeliver(Request $request)
    {


        $status = request("status");
        $fromDate = request("fromDate");
        $toDate = request("toDate");
        $dp =  DB::table("stock_outward_mst as a")
            ->select(
                "a.id",
                "a.outward_id",
                "a.invoice_date",
                "b.order_id",
                "c.company as customer",
                "d.name as user",
                "f.vehicle_name",
                "f.vehicle_no",
                "a.transport_name",
                "a.tracking_no",
                "a.invoice_id",
                "c.address",
                "c.party_code",
                "b.city",
                "b.coordinates",
                "a.transport_date",
                "a.no_of_box",
                "g.vehicle_name as vehicle_name2",
                "g.vehicle_no as vehicle_no2",
                "c.number",
                DB::raw("count(e.id) as item_total"),
                DB::raw("sum(e.qty) as total_qty")
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_outward_det as e", "a.id", "e.mst_id")
            ->leftJoin("mode_of_transport as f", "a.transport_id", "f.id")
            ->leftJoin("mode_of_transport as g", "a.transport_id2", "g.id")
            ->where("a.transport_id", $request->user->id)
            ->where("a.dispatch_status", "final");

        if ($fromDate) {
            $dp->whereDate("a.transport_date", ">=", $fromDate);
        }
        if ($toDate) {
            $dp->whereDate("a.transport_date", "<=", $toDate);
        }

        $dp->where("a.status", $status);

        $dp->whereNotNull("a.transport_id")
            ->groupBy("a.id", "b.order_id", "c.company", "d.name", "a.outward_id", "a.invoice_date", "f.vehicle_name", "f.vehicle_no", "a.tracking_no", "a.transport_name", "a.invoice_id", "c.address", "b.city", "c.party_code", "b.coordinates", "a.transport_date", "a.no_of_box", "g.vehicle_name", "g.vehicle_no", "c.number");
        $dispatch_plan = $dp->orderBy("a.id", "desc")
            ->get();

        // echo "<pre>";
        // print_r($dispatch_plan);
        // die;
        return view("salesapp.ready-to-deliver", compact("dispatch_plan"));
    }

    public function sendOtpSMS(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'id' => 'required',

        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return response()->json(["msg" => $messages, "error" => true]);
        }
        try {

            $data = DB::table("stock_outward_mst as a")
                ->select("a.invoice_id", "c.company")
                ->join("order_mst as b", "a.order_id", "b.id")
                ->join("customers as c", "b.customer_id", "c.id")
                ->where("a.id", $request->id)->first();

            $otp = rand(100000, 999999);


            $message = "Hello {$data->company}, Your order {$data->invoice_id} is ready to be delivered. Delivery OTP: {$otp} Please share this OTP with the delivery executive to complete delivery. CM Automobiles";

            $response = SmsServices::send($request->number, $message);

            session()->put('OTP', $otp);
            return response()->json([
                'error' => false,
                'msg' => 'OTP sent successfully',
                'api_response' => $response,
                'otp' => $otp
            ]);
        } catch (\Throwable $th) {
            return response()->json(["msg" => $th->getMessage(), "error" => true]);
        }
    }

    public function deliveredChallan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'id' => 'required',

        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return response()->json(["msg" => $messages, "error" => true]);
        }

        try {
            if (session("OTP") == $request->otp) {
                DB::table("stock_outward_mst")->where("id", $request->id)->update(array("status" => "delivered"));
                return response()->json([
                    'error' => false,
                    'msg' => 'Save Successfully',

                ]);
            } else {

                return response()->json([
                    'error' => true,
                    'msg' => 'Invalid OTP',

                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(["msg" => $th->getMessage(), "error" => true]);
        }
    }
}
