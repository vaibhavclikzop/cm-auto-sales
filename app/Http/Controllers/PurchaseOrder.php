<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class PurchaseOrder extends Controller
{
    public function GeneratePO(Request $request)
    {


        $id = request("id");
        $data = collect();
        $det = collect();
        if ($id) {
            $data = DB::table("po_mst")->where("id", $id)->first();
            $det = DB::table("po_det as a")
                ->select("a.*", "b.name",  "b.hsn_code", "b.part_no")
                ->join("products as b", "a.product_id", "b.id")
                ->where("a.mst_id", $id)->get();
        }


        $vendor = DB::table("vendor")
            ->where("store_id", $request->user->active_inventory)
            ->get();
        $gst = DB::table("gst")->get();
        return view("generate-po", compact("vendor", "gst", "data", "det"));
    }

    public function SavePO(Request $request)
    {

        $company_settings =  DB::table("company_settings")->where("id", 1)->first();

        $po_id = $company_settings->invoice_prefix . $company_settings->invoice_no;

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
       

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

        try {
            DB::beginTransaction();

            if ($request->id) {
               DB::table('po_mst')->where("id", $request->id)->update(array(
                    "vendor_id" => $request->vendor_id,
                    "user_id" => $request->user->id,
                    "po_id" => $po_id,
                    "name" => $request->name,
                    "description" => $request->description,
                    "company_id" => $request->user->active_inventory,
                    "status" => "pending",
                    "gst_type" => "CGST",
                ));
                  $mst_id =$request->id;
            } else {
                $mst_id = DB::table('po_mst')->insertGetId(array(
                    "vendor_id" => $request->vendor_id,
                    "user_id" => $request->user->id,
                    "po_id" => $po_id,
                    "name" => $request->name,
                    "description" => $request->description,
                    "company_id" => $request->user->active_inventory,
                    "status" => "pending",
                    "gst_type" => "CGST",
                ));
            }


            DB::table("po_det")->where("mst_id",$mst_id)->delete();


            foreach ($prod_list as $key => $value) {
                DB::table('po_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "gst" => $value->gst,
   
                ));
            }
            $company_settings =  DB::table("company_settings")->where("id", 1)->increment("invoice_no", 1);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect("/purchase-order-view/$mst_id")->with('success', "Save Successfully");
    }

    public function PurchaseOrder(Request $request, $status)
    {


        $po_mst = DB::table("po_mst as a")
            ->select("a.*", "b.name as vendor_name", "c.name as user_name", "d.packing_date", 'd.delivery_date', "e.name as customer_name")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")
            ->leftJoin("order_mst as d", "a.order_id", "d.id")
            ->leftJoin("customers as e", "d.customer_id", "e.id")
            ->where("a.status", $status)

            ->whereIn("a.user_id", $request->userIds)
            ->where("a.company_id", $request->user->active_inventory)
            ->orderBy("a.id", "desc")
            ->get();
        return view("purchase-order", compact("po_mst", "status"));
    }

    public function PurchaseOrderView(Request $request, $id)
    {
        $data = DB::table("po_mst as a")
            ->select("a.*","a.created_at", "b.name as vendor_name", "b.number as vendor_number", "b.email as vendor_email", "b.address as vendor_address", "b.state as vendor_state", "b.city as vendor_city", "b.pincode as vendor_pincode", "b.gst as vendor_gst", "b.company as company", "c.name","c.img","c.address","c.number","c.email","c.gst_no","c.state")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->join("company as c", "a.company_id", "c.id")
            ->where("a.id", $id)->first();
        $po_det = DB::table("po_det as a")
            ->select("a.*", "b.name as name", "b.hsn_code as hsn_code", "c.name as uom", "b.description", "b.part_no", "d.name as brand")
            ->join("products as b", "a.product_id", "b.id")
            ->join("brand as d", "b.brand_id", "d.id")
            ->join("unit_type as c", "b.uom", "c.id")
            ->where("a.mst_id", $id)
            ->get();

        return view("purchase-order-view", compact("data", "po_det"));
    }

    public function UpdateCharges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'freight_charges' => 'required',
            'loading_charges' => 'required',

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
            $mst_id = DB::table('po_mst')->where("id", $request->id)->update(array(
                "freight_charges" => $request->freight_charges,
                "loading_charges" => $request->loading_charges,

            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function SaveGeneratePO(Request $request)
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




        try {
            DB::table('po_mst')->where("id", $request->id)->update(array(
                "status" => "generated",


            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function DeletePOProduct(Request $request)
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
        try {
            DB::table('po_det')->where("id", $request->id)->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function SavePOProduct(Request $request)
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
        try {

            $po_det =  DB::table("po_det")->where("mst_id", $request->id)->where("product_id", $request->product_id)->first();
            if ($po_det) {
                return redirect()->back()->with('error', "Product already added");
            }

            DB::table('po_det')->insert(array(
                "mst_id" => $request->id,
                "product_id" => $request->product_id,
                "qty" => $request->qty,
                "price" => $request->price,
                "gst" => $request->gst,
                "gst_type" => $request->gst_type,
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }

    public function deletePO(Request $request)
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
        try {



            DB::table('po_mst')->where("id", $request->id)->delete();
            DB::table('po_det')->where("mst_id", $request->id)->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }
}
