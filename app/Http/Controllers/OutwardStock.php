<?php

namespace App\Http\Controllers;

use App\Models\warehouse;
use App\Services\SmsServices;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class OutwardStock extends Controller
{

    public function OutwardStock(Request $request)
    {
        $customers =   DB::table("customers")->where("company_id", $request->user->active_inventory)->get();

        $warehouse =  warehouse::where("company_id", $request->user->active_inventory)->get();

        $out_id = request("out_id");

        $outward_mst = null;
        $outward_det = null;
        if (request("out_id")) {
            $id = request("out_id");
            $outward_mst = DB::table("stock_outward_mst")->where("id", $id)->first();


            $orderSub = DB::table('order_det')
                ->select(
                    'product_id',
                    DB::raw('sum(qty) as qty'),
                    DB::raw('SUM(out_qty) as out_qty'),
                    "id"
                )
                ->where('mst_id', $outward_mst->order_id)
                ->groupBy('product_id', "id");

            $outward_det = DB::table("stock_outward_det as a")
                ->select(
                    "a.*",
                    "b.name as product",
                    "b.part_no as article_no",
                    "c.out_qty",
                    "c.qty",
                    "z.name as brand",
                    DB::raw("IFNULL(j.stock, 0) as stock"),
                    DB::raw("a.qty as outward_qty")
                )
                ->join("products as b", "a.product_id", "=", "b.id")
                ->joinSub($orderSub, 'c', function ($join) {
                    $join->on("a.product_id", "=", "c.product_id");
                    $join->on("c.id", "=", "a.order_det_id");
                })
                ->join("brand as z", "b.brand_id", "=", "z.id")
                ->leftJoin("current_stock as j", function ($join) use ($outward_mst) {
                    $join->on("a.product_id", "=", "j.product_id")
                        ->where("j.location_id", "=", $outward_mst->location_id);
                })
                ->where("a.mst_id", $id)
                ->get();
        }

        return view("outward-stock", compact("customers", "warehouse", "outward_mst", "outward_det"));
    }

    public function SaveOutward(Request $request)
    {
        $outward_id = 'PT_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'order_id' => 'required',
            'invoice_date' => 'required',
            'discount_type' => 'required',

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


        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }
        DB::beginTransaction();

        $order_mst =  DB::table("order_mst")->where("id", $request->order_id)->first();

        try {

            if (!$request->id) {

                $invoice =   DB::table("company")->where("id", $request->user->active_inventory)->first();
                $inv = $invoice->pt_prefix . $invoice->pt_no;



                $mst_id = DB::table('stock_outward_mst')->insertGetId(array(
                    "customer_id" => $request->customer_id,
                    "order_id" => $request->order_id,
                    "invoice_date" => $request->invoice_date,
                    "description" => $request->description,
                    "outward_id" => $inv,
                    "user_id" => $request->user->id,
                    "jon_no" => $request->jon_no,
                    "store_id" => $request->user->active_inventory,
                    "transport_mode" => $request->transport_mode,
                    "tracking_id" => $request->tracking_id,
                    "location_id" => $request->location_id,
                    "warehouse_id" => $request->warehouse_id,
                    "discount" => $request->discount,
                    "discount_type" => $request->discount_type,

                ));
                DB::table("company")->where("id", $request->user->active_inventory)->increment("pt_no");
            } else {
                DB::table('stock_outward_mst')->where("id", $request->id)->update(array(
                    "description" => $request->description,
                    "discount" => $request->discount,

                ));

                $mst_id = $request->id;
                $som = DB::table("stock_outward_mst")->where("id", $mst_id)->first();
                $sod = DB::table("stock_outward_det")->where("mst_id", $mst_id)->get();

                foreach ($sod as $key => $value) {
                    DB::table("order_det")
                        ->where("mst_id", $som->order_id)
                        ->where("product_id", $value->product_id)
                        ->where("id", $value->order_det_id)
                        ->decrement("out_qty", $value->qty);

                    DB::table('current_stock')
                        ->where("location_id", $request->location_id)
                        ->where("product_id", $value->product_id)
                        ->update([
                            "stock" => DB::raw("stock + $value->qty")
                        ]);
                    DB::table("stock_outward_det")->where("id", $value->id)->delete();
                }
            }


            // DB::table("stock_outward_det")->where("mst_id", $mst_id)->delete();


            $status = 0;

            foreach ($prod_list as $key => $value) {

                $qty = 0;
                $stock = DB::table("stock_outward_det")
                    ->where("mst_id", $mst_id)
                    ->where("product_id", $value->product_id)
                    ->where("order_det_id", $value->order_det_id)
                    ->first();
                if ($stock) {
                    $qty = $value->qty - $stock->qty;
                    DB::table("stock_outward_det")->where("id", $stock->id)->delete();
                } else {

                    $qty = $value->qty;
                }


                DB::table('stock_outward_det')->insert(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "order_det_id" => $value->order_det_id,
                ));



                DB::table("order_det")->where("mst_id", $request->order_id)->where("product_id", $value->product_id)->increment("out_qty", $value->qty);
                DB::table('current_stock')
                    ->where("location_id", $request->location_id)
                    ->where("product_id", $value->product_id)
                    ->update([
                        "stock" => DB::raw("stock - $qty")
                    ]);
            }

            $order_det =  DB::table('order_det')->where("mst_id", $request->order_id)->whereRaw("qty > out_qty")->get();
            if ($order_det->isEmpty()) {
                $order_det =  DB::table('order_mst')->where("id", $request->order_id)->update([
                    "status" => "complete"
                ]);
            } else {
                $order_det =  DB::table('order_mst')->where("id", $request->order_id)->update([
                    "status" => "processing"
                ]);
            }


            DB::commit();
            //  die;
        } catch (\Throwable $th) {

            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', "Save Successfully");
    }

    public function OutwardOrderList(Request $request)
    {

        $status = request()->status;
        $id = request()->id;


        $out =  DB::table("stock_outward_mst as a")
            ->select(
                "a.id",
                "b.order_id",
                "a.outward_id",
                "a.invoice_date",
                "a.status",
                "c.company as customer",
                "d.name as user",
                "a.is_invoice",
                "a.dispatch_status",
                DB::raw("
    SUM((e.qty*e.price - ((e.qty*e.price)/100*f.discount))) AS sub_total
"),
                DB::raw("
    round(SUM((e.qty*e.price - ((e.qty*e.price)/100*f.discount))) +
    (SUM((e.qty*e.price - ((e.qty*e.price)/100*f.discount))) * 18 / 100),2)
    AS total
")

            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_outward_det as e", "a.id", "e.mst_id")
            ->join("order_det as f", function ($join) {
                $join->on("e.product_id", "=", "f.product_id")
                    ->on("b.id", "=", "f.mst_id");
            });
        if ($id) {
            $out->where("a.order_id", $id);
        }

        if ($status == "complete") {
            $out->where("a.is_invoice", 1);
        } else {
            $out->where("a.status", $status)->where("a.is_invoice", 0);
        }

        $outward = $out
            ->where("b.company_id", $request->user->active_inventory)
            ->groupBy("a.id", "b.order_id",   "c.company", "d.name", "a.outward_id", "a.invoice_date", "a.status", "a.is_invoice", "a.dispatch_status")
            ->orderBy("a.id", "desc")->get();


        return view("outward-order-list", compact("outward"));
    }
    public function DispatchChallan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

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

            $mst_id = DB::table('stock_outward_mst')->where("id", $request->id)->update(array(
                "dispatch_status" => "processing",


            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function DeliveredChallan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

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

            $mst_id = DB::table('stock_outward_mst')->where("id", $request->id)->update(array(
                "status" => "delivered",


            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function OutwardChallanView(Request $request, $id)
    {
        $data =  DB::table("stock_outward_mst as a")
            ->select("a.*", "c.company as customer_name", "c.address", "c.state", "c.city", "c.pincode", "c.email", "c.number", "c.gst", "b.delivery_date", "d.name as user", "e.img", "e.name", "e.gst_no", "e.address as c_address", "e.email as c_email", "e.name as company_name", "c.state as bill_state", "c.city as bill_city", "c.address as bill_address", "c.pincode as bill_pincode", "c.address as ship_address", "c.ship_state", "c.ship_city", "c.ship_pincode")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company as e", "b.company_id", "e.id")
            ->where("a.id", $id)
            ->first();
        $order_det = DB::table("stock_outward_det as a")
            ->select("a.*", "b.name as product", "b.product_location", "b.part_no as part_code", "e.name as brand", "b.hsn_code", DB::raw("IFNULL(j.stock, 0) as stock"))
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("stock_outward_mst as c", "a.mst_id", "=", "c.id")
            ->join("order_mst as d", "c.order_id", "=", "d.id")
            ->join("brand as e", "b.brand_id", "=", "e.id")
            ->leftJoin("current_stock as j", function ($join) use ($request, $data) {
                $join->on("a.product_id", "=", "j.product_id")
                    ->where("j.location_id", "=", $data->location_id);
            })
            ->where("a.mst_id", $id)
            ->get();


        $nextProduct = DB::table("stock_outward_mst")
            ->where("id", ">", $id)
            ->orderBy("id", "asc")
            ->first();

        // Get the previous record
        $previousProduct = DB::table("stock_outward_mst")
            ->where("id", "<", $id)
            ->orderBy("id", "desc")
            ->first();
        return view("outward-challan-view", compact("data", "order_det", "nextProduct", "previousProduct"));
    }

    public function convertToInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

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
            $invoice =   DB::table("company")->where("id", $request->user->active_inventory)->first();
            $inv = $invoice->invoice_prefix . $invoice->invoice_no;

            DB::table('stock_outward_mst')->where("id", $request->id)->update(array(
                "is_invoice" => 1,
                "invoice_id" => $inv,
                "discount" => $request->discount,
                "invoice_convert_date" => now()


            ));
            DB::table("company")->where("id", $request->user->active_inventory)->increment("invoice_no");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function invoices(Request $request)
    {


        $out =  DB::table("stock_outward_mst as a")
            ->select("a.*", "b.order_id",   "c.company as customer", "d.name as user", "c.email")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id");
        if (request("status")) {
            $out->where("a.status", request("status"));
        } else {
            $out->where("a.status", "pending");
        }

        $outward = $out->where("a.is_invoice", 1)
            ->where("b.company_id", $request->user->active_inventory)
            ->orderBy("a.id", "desc")->get();



        return view("invoices", compact("outward"));
    }


    public function invoiceView(Request $request, $id)
    {

        $data =  DB::table("stock_outward_mst as a")
            ->select("a.*", "c.company as customer_name", "c.address", "c.state", "c.city", "c.pincode", "c.email", "c.number", "c.gst", "b.delivery_date", "d.name as user", "e.gst_no", "e.img", "e.name", "e.address as c_address", "e.email as c_email", "e.name as company_name", "e.state as c_state", "c.state as bill_state", "c.city as bill_city", "c.address as bill_address", "c.pincode as bill_pincode", "c.ship_address", "c.ship_state", "c.ship_city", "c.ship_pincode")
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company as e", "b.company_id", "e.id")
            ->where("a.id", $id)
            ->first();
        $orderDetSub = DB::table('order_det')
            ->select(
                'product_id',
                'mst_id',
                DB::raw('MAX(discount) as discount'),
                DB::raw('MAX(gst) as gst')
            )
            ->groupBy('product_id', 'mst_id');

        $order_det = DB::table("stock_outward_det as a")
            ->select(
                "a.*",
                "b.name as product",
                "b.part_no as part_code",
                "e.name as brand",
                "b.hsn_code",
                "f.discount",
                "f.gst",
                "a.discount as special_discount"
            )
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("stock_outward_mst as c", "a.mst_id", "=", "c.id")
            ->join("order_mst as d", "c.order_id", "=", "d.id")
            ->join("brand as e", "b.brand_id", "=", "e.id")
            ->joinSub($orderDetSub, 'f', function ($join) {
                $join->on("a.product_id", "=", "f.product_id")
                    ->on("d.id", "=", "f.mst_id");
            })
            ->where("a.mst_id", $id)
            ->get();
        $gst = DB::table("gst")->get();

        return view("invoice-view", compact("data", "order_det", "gst"));
    }


    public function updateOutwardDetValue(Request $request)
    {

        DB::table("stock_outward_det")->where("id", $request->id)
            ->update(array(
                $request->field => $request->value
            ));
        return true;
    }

    public function cancelOutwardChallan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

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
            $som = DB::table("stock_outward_mst")->where("id", $request->id)->first();
            $sod = DB::table("stock_outward_det")->where("mst_id", $request->id)->get();

            foreach ($sod as $key => $value) {
                DB::table("order_det")
                    ->where("mst_id", $som->order_id)
                    ->where("product_id", $value->product_id)
                    ->decrement("out_qty", $value->qty);


                DB::table('current_stock')
                    ->where("location_id", $som->location_id)
                    ->where("product_id", $value->product_id)
                    ->update([
                        "stock" => DB::raw("stock + $value->qty")
                    ]);
            }

            DB::table("stock_outward_mst")->where("id", $request->id)->update(array(
                "status" => "cancel"
            ));

            DB::table("order_mst")->where("id", $som->order_id)->update(array(
                "status" => "processing"
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function dispatchPlan(Request $request)
    {

        $data =  DB::table("stock_outward_mst as a")
            ->select(
                "a.id",
                "a.outward_id",
                "a.invoice_date",
                "b.order_id",
                "c.company as customer",
                "d.name as user",

                "a.invoice_id",
                "c.address",
                "c.party_code",
                "b.city",
                "b.coordinates",
                "a.transport_date",
                DB::raw("count(e.id) as item_total"),
                DB::raw("sum(e.qty) as total_qty")
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_outward_det as e", "a.id", "e.mst_id")
            ->where("b.company_id", $request->user->active_inventory)
            ->where("a.dispatch_status", "processing")
            ->whereNull("a.transport_id")
            ->groupBy("a.id", "b.order_id", "c.company", "d.name", "a.outward_id", "a.invoice_date", "a.outward_id", "a.invoice_date",   "a.invoice_id", "c.address", "b.city", "c.party_code", "b.coordinates", "a.transport_date")
            ->orderBy("a.id", "desc")
            ->get();




        $dispatch_plan =  DB::table("stock_outward_mst as a")
            ->select(
                "a.id",
                "a.outward_id",
                "a.invoice_date",
                "b.order_id",
                "c.company as customer",
                "d.name as user",
                "f.vehicle_name",
                "g.vehicle_name as vehicle_name2",
                "f.vehicle_no",
                "g.vehicle_no as vehicle_no2",
                "a.transport_name",
                "a.tracking_no",
                "a.invoice_id",
                "c.address",
                "c.party_code",
                "b.city",
                "b.coordinates",
                "a.transport_date",
                "a.no_of_box",
                DB::raw("count(e.id) as item_total"),
                DB::raw("sum(e.qty) as total_qty")
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_outward_det as e", "a.id", "e.mst_id")
            ->leftJoin("mode_of_transport as f", "a.transport_id", "f.id")
            ->leftJoin("mode_of_transport as g", "a.transport_id2", "g.id")
            ->where("b.company_id", $request->user->active_inventory)
            ->where("a.dispatch_status", "processing")

            ->whereNotNull("a.transport_id")
            ->groupBy("a.id", "b.order_id", "c.company", "d.name", "a.outward_id", "a.invoice_date", "f.vehicle_name", "f.vehicle_no", "a.tracking_no", "a.transport_name", "a.invoice_id", "c.address", "b.city", "c.party_code", "b.coordinates", "a.transport_date", "a.no_of_box", "g.vehicle_name", "g.vehicle_no")
            ->orderBy("a.id", "desc")
            ->get();
        $mot =  DB::table("mode_of_transport")->get();

        return view("dispatch-plan", compact("data", "mot", "dispatch_plan"));
    }

    public function updateDispatchPlan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ids' => 'required',
            'transport_id' => 'required',
            'date' => 'required',

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
            DB::table("stock_outward_mst")
                ->whereIn("id", explode(", ", $request->ids))
                ->update(array(
                    "transport_id" => $request->transport_id,
                    "transport_id2" => $request->transport_id2,
                    "transport_name" => $request->transport_name,
                    "tracking_no" => $request->tracking_no,
                    "transport_date" => $request->date,
                    "no_of_box" => $request->no_of_box,
                    "transport_remarks" => $request->remarks,
                ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function finalDispatchPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',


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
            DB::table("stock_outward_mst")
                ->whereDate("transport_date", $request->date)
                ->update(array(
                    "dispatch_status" => "final",

                ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function dispatchMgmt(Request $request)
    {


        $status = request("status");
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
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
                "c.number",
                "g.vehicle_name as vehicle_name2",
                "g.vehicle_no as vehicle_no2",
                "a.transport_remarks",
                "a.transport_id",
                "a.dispatch_file",
                DB::raw("count(e.id) as item_total"),
                DB::raw("sum(e.qty) as total_qty")
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("stock_outward_det as e", "a.id", "e.mst_id")
            ->leftJoin("mode_of_transport as f", "a.transport_id", "f.id")
            ->leftJoin("mode_of_transport as g", "a.transport_id2", "g.id")
            ->where("b.company_id", $request->user->active_inventory)
            ->where("a.dispatch_status", "final")
            ->whereDate("a.transport_date", ">=", $fromDate)
            ->whereDate("a.transport_date", "<=", $toDate);
        if ($status == "delivered") {
            $dp->where("a.status", "delivered");
        } else {
            $dp->where("a.status", $status);
        }
        $dp->whereNotNull("a.transport_id")
            ->groupBy("a.id", "b.order_id", "c.company", "d.name", "a.outward_id", "a.invoice_date", "f.vehicle_name", "f.vehicle_no", "a.tracking_no", "a.transport_name", "a.invoice_id", "c.address", "b.city", "c.party_code", "b.coordinates", "a.transport_date", "a.no_of_box", "g.vehicle_name", "g.vehicle_no", "c.number", "a.transport_remarks", "a.transport_id", "a.dispatch_file");
        $dispatch_plan = $dp->orderBy("a.id", "desc")
            ->get();

        return view("dispatch", compact("dispatch_plan"));
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
            session()->put('OTP', $otp);

            return  $message = "Hello {$data->company}, Your order {$data->invoice_id} is ready to be delivered. Delivery OTP: {$otp} Please share this OTP with the delivery executive to complete delivery. CM Automobiles";

            $response = SmsServices::send($request->number, $message);


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

    public function deliveredChallans(Request $request)
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

    public function uploadDispatchFile(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'id' => 'required',
            'remarks' => 'required',

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



        $file = "";
        if ($request->hasFile('file')) {
            $file = time() . '.' . $request->file('file')->extension();
            $request->file('file')->move('dispatch files', $file);
        }


        try {

            DB::table('stock_outward_mst')->where("id", $request->id)->update(array(
                "dispatch_file" => $file,
                "transport_remarks" => $request->remarks,
                "status" => "delivered",
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function updateWithPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'id' => 'required',
            'remarks' => 'required',
            'transaction_password' => 'required',

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


        $exits =  DB::table("company_settings")->where("transaction_password", $request->transaction_password)->exists();
        if (!$exits) {
            return redirect()->back()->with('error', "Incorrect Password");
        }

        try {

            DB::table('stock_outward_mst')->where("id", $request->id)->update(array(

                "transport_remarks" => $request->remarks,
                "status" => "delivered",
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    // public function downloadInvoice(Request $request, $id)
    // {
    //     $data =  DB::table("stock_outward_mst as a")
    //         ->select("a.*", "c.company as customer_name", "c.address", "c.state", "c.city", "c.pincode", "c.email", "c.number", "c.gst", "b.delivery_date", "d.name as user", "e.gst_no", "e.img", "e.name", "e.address as c_address", "e.email as c_email", "e.name as company_name", "e.state as c_state", "c.state as bill_state", "c.city as bill_city", "c.address as bill_address", "c.pincode as bill_pincode", "c.ship_address", "c.ship_state", "c.ship_city", "c.ship_pincode")
    //         ->join("order_mst as b", "a.order_id", "b.id")
    //         ->join("customers as c", "b.customer_id", "c.id")
    //         ->join("users as d", "a.user_id", "d.id")
    //         ->join("company as e", "b.company_id", "e.id")
    //         ->where("a.id", $id)
    //         ->first();
    //     $orderDetSub = DB::table('order_det')
    //         ->select(
    //             'product_id',
    //             'mst_id',
    //             DB::raw('MAX(discount) as discount'),
    //             DB::raw('MAX(gst) as gst')
    //         )
    //         ->groupBy('product_id', 'mst_id');

    //     $order_det = DB::table("stock_outward_det as a")
    //         ->select(
    //             "a.*",
    //             "b.name as product",
    //             "b.part_no as part_code",
    //             "e.name as brand",
    //             "b.hsn_code",
    //             "f.discount",
    //             "f.gst",
    //             "a.discount as special_discount"
    //         )
    //         ->join("products as b", "a.product_id", "=", "b.id")
    //         ->join("stock_outward_mst as c", "a.mst_id", "=", "c.id")
    //         ->join("order_mst as d", "c.order_id", "=", "d.id")
    //         ->join("brand as e", "b.brand_id", "=", "e.id")
    //         ->joinSub($orderDetSub, 'f', function ($join) {
    //             $join->on("a.product_id", "=", "f.product_id")
    //                 ->on("d.id", "=", "f.mst_id");
    //         })
    //         ->where("a.mst_id", $id)
    //         ->get();
    //     $gst = DB::table("gst")->get();
    //     $type = "with";
    //     // $pdf = Pdf::loadView('pdf.invoice-pdf', compact("data", "order_det", "gst"));
    //     $pdf = Pdf::loadView('pdf.invoice-pdf', compact("data", "order_det", "gst", "type"))->setPaper('a4', 'portrait')
    //         ->setOption('isRemoteEnabled', true);

    //     return $pdf->stream('invoice.pdf');
    // }
    public function downloadInvoice(Request $request, $id)
    {
        $type = $request->type;

        $data =  DB::table("stock_outward_mst as a")
            ->select(
                "a.*",
                "c.company as customer_name",
                "c.address",
                "c.state",
                "c.city",
                "c.pincode",
                "c.email",
                "c.number",
                "c.gst",
                "b.delivery_date",
                "d.name as user",
                "e.gst_no",
                "e.img",
                "e.name",
                "e.address as c_address",
                "e.email as c_email",
                "e.name as company_name",
                "e.state as c_state",
                "c.state as bill_state",
                "c.city as bill_city",
                "c.address as bill_address",
                "c.pincode as bill_pincode",
                "c.ship_address",
                "c.ship_state",
                "c.ship_city",
                "c.ship_pincode"
            )
            ->join("order_mst as b", "a.order_id", "b.id")
            ->join("customers as c", "b.customer_id", "c.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company as e", "b.company_id", "e.id")
            ->where("a.id", $id)
            ->first();

        $orderDetSub = DB::table('order_det')
            ->select(
                'product_id',
                'mst_id',
                DB::raw('MAX(discount) as discount'),
                DB::raw('MAX(gst) as gst')
            )
            ->groupBy('product_id', 'mst_id');

        $order_det = DB::table("stock_outward_det as a")
            ->select(
                "a.*",
                "b.name as product",
                "b.part_no as part_code",
                "e.name as brand",
                "b.hsn_code",
                "f.discount",
                "f.gst",
                "a.discount as special_discount"
            )
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("stock_outward_mst as c", "a.mst_id", "=", "c.id")
            ->join("order_mst as d", "c.order_id", "=", "d.id")
            ->join("brand as e", "b.brand_id", "=", "e.id")
            ->joinSub($orderDetSub, 'f', function ($join) {
                $join->on("a.product_id", "=", "f.product_id")
                    ->on("d.id", "=", "f.mst_id");
            })
            ->where("a.mst_id", $id)
            ->get();

        $gst = DB::table("gst")->get();


        $qr_code = null;

        if ($data->is_e_invoice) {

            $qr_url = "https://router.mastersindia.co/api/v1/einvoice/qrcode/amFuX21hcl8yMDI1LTI2-699d9c1aff8a237edc26a3da/";

            try {
                $qr_image = file_get_contents($qr_url);

                if ($qr_image) {
                    $qr_code = 'data:image/png;base64,' . base64_encode($qr_image);
                }
            } catch (\Exception $e) {
                $qr_code = null;
            }
        }



        $filename = str_replace(['/', '\\'], '-', $data->invoice_id);

        $pdf = Pdf::loadView('pdf.invoice-pdf', compact("data", "order_det", "gst", "type","qr_code"))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('invoice-' . $filename . '.pdf');
    }
}
