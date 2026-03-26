<?php

namespace App\Http\Controllers;

use App\Mail\OrderDeliveredMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class sendEmail extends Controller
{
    public function sendEmail(Request $request)
    {
        $id = $request->id;
        $data =  DB::table("stock_outward_mst as a")
            ->select("a.*", "c.name as customer_name", "c.address", "c.state", "c.city", "c.pincode", "c.email", "c.number", "c.gst", "b.delivery_date", "d.name as user", "e.*", "e.address as c_address", "e.email as c_email", "e.name as company_name")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company as e", "b.company_id", "e.id")
            ->where("a.id", $id)
            ->first();
        $order_det = DB::table("stock_outward_det as a")
            ->select("a.*", "b.name as product", "b.part_no as part_code", "e.name as brand", "b.hsn_code", "f.discount")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("stock_outward_mst as c", "a.mst_id", "=", "c.id")
            ->join("order_mst as d", "c.order_id", "=", "d.id")
            ->join("brand as e", "b.brand_id", "=", "e.id")
            ->join("order_det as f", function ($join) {
                $join->on("a.product_id", "=", "f.product_id")
                    ->on("d.id", "=", "f.mst_id");
            })

            ->where("a.mst_id", $id)
            ->get();

 


  
        Mail::to($request->email)
            ->send(new OrderDeliveredMail($data, $order_det));
    }
}
