<?php

namespace App\Http\Controllers;

use App\Models\warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class InwardStock extends Controller
{
    public function InwardStock(Request $request)
    {
        $vendor = DB::table("vendor")->where("store_id", $request->user->active_inventory)->get();
        $store = DB::table("store")->get();
        $warehouse =  warehouse::where("company_id", $request->user->active_inventory)->get();

        $brand = DB::table("brand")->get();
        return view("inward-stock", compact("vendor", "store", "warehouse", "brand"));
    }

    public function SaveInwardStock(Request $request)
    {



        $inward_id = 'Inward_' . date('dmyhis');

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

            $exits = DB::table("stock_inward_mst")->where("company_id", $request->user->active_inventory)
                ->where("invoice_no", $request->invoice_no)->exists();
            if ($exits) {
                return redirect()->back()->with('error', "Invoice no. already added");
            }

            $mst_id = DB::table('stock_inward_mst')->insertGetId(array(
                "vendor_id" => $request->vendor_id,
                "po_id" => $request->po_id,
                "location_id" => $request->location_id,
                "invoice_no" => $request->invoice_no,
                "invoice_date" => $request->invoice_date,
                "received_material_date" => $request->received_material_date,
                "description" => $request->description,
                "user_id" => $request->user->id,
                "company_id" => $request->user->active_inventory,
                "adj_amt_type"=>$request->adj_amt_type,
                "adj_amt"=>$request->adj_amt,
                "discount"=>$request->totalDiscount,


            ));
            $status = 0;
            foreach ($prod_list as $key => $value) {

                $det_id = DB::table('stock_inward_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "price" => $value->price,
                    "discount" => $value->discount,

                ));



                DB::table('po_det')->where("mst_id", $request->po_id)->where("product_id", $value->product_id)->increment("received_qty", $value->qty);

                $current_stock = DB::table("current_stock")->where("product_id", $value->product_id)->where("location_id", $request->location_id)->first();

                if ($current_stock) {
                    DB::table('current_stock')->where("id", $current_stock->id)->update([
                        'stock' => DB::raw('stock + ' . $value->qty)
                    ]);
                } else {
                    DB::table('current_stock')->insertGetId(array(
                        "location_id" => $request->location_id,
                        "product_id" => $value->product_id,
                        "stock" => $value->qty,
                    ));
                }
            }


            $status = 0;
            $po_det = DB::table('po_det')->where("mst_id", $request->po_id)->get();
            foreach ($po_det as $value) {
                if ($value->received_qty < $value->qty) {
                    $status = 1;
                }
            }


            if ($status == 1) {
                DB::table('po_mst')->where("id", $request->po_id)->update(array(
                    "status" => "partial",
                ));
            } else {
                DB::table('po_mst')->where("id", $request->po_id)->update(array(
                    "status" => "complete",
                ));
            }
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', "Save Successfully");
    }

    public function InwardReport(Request $request)
    {

        $stock_inward_mst =   DB::table("stock_inward_mst as a")
            ->select("a.*", "b.name as vendor", "c.po_id as po_name", "d.name as location", "e.name as user")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->leftJoin("po_mst as c", "a.po_id", "c.id")
            ->join("store as d", "a.location_id", "d.id")
            ->join("users as e", "a.user_id", "e.id")
            ->where("a.company_id", $request->user->active_inventory)
            ->orderBy("a.id", "desc")
            ->get();
        return view("inward-report", compact("stock_inward_mst"));
    }

    public function updateStockInward(Request $request)
    {
        DB::table('stock_inward_mst')
            ->where('id', $request->id)
            ->update([
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'received_material_date' => $request->received_material_date,
                'description' => $request->description
            ]);

        return redirect()->back()->with('success', 'MRN Updated Successfully');
    }
    public function InwardReportView(Request $request, $id)
    {

        $stock_inward_mst =   DB::table("stock_inward_mst as a")
            ->select("a.*", "a.invoice_no as inv", "b.name as vendor", "c.name as po_name", "f.name as location", "e.name as user", "d.*", "g.name as warehouse", "a.id")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->leftJoin("po_mst as c", "a.po_id", "c.id")
            ->join("company as d", "a.company_id", "d.id")
            ->join("users as e", "a.user_id", "e.id")
            ->join("store as f", "a.location_id", "f.id")
            ->join("warehouse as g", "f.warehouse_id", "g.id")
            ->where("a.id", $id)
            ->where("a.company_id", $request->user->active_inventory)
            ->first();


        $stock_inward_det = DB::table("stock_inward_det as a")
            ->select("a.*", "b.name as product_name", "b.part_no", "b.hsn_code", "c.name as uom", "b.product_location", "d.name as brand")
            ->join("products as b", "a.product_id", "b.id")
            ->join("unit_type as c", "b.uom", "c.id")
            ->join("brand as d", "b.brand_id", "d.id")

            ->where("a.mst_id", $id)
            ->get();



        return view("inward-report-view", compact("stock_inward_mst", "stock_inward_det"));
    }

    public function deleteStockInward(Request $request)
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
        DB::beginTransaction();

        try {

            $stock_inward_mst =  DB::table("stock_inward_mst")->where("id", $request->id)->first();
            $stock_inward_det =  DB::table("stock_inward_det")->where("mst_id", $request->id)->get();
            foreach ($stock_inward_det as $key => $value) {
                DB::table("current_stock")
                    ->where("product_id", $value->product_id)
                    ->where("location_id", $stock_inward_mst->location_id)
                    ->decrement("stock", $value->qty);

                DB::table("po_det")
                    ->where("product_id", $value->product_id)
                    ->where("mst_id", $stock_inward_mst->po_id)
                    ->decrement("received_qty", $value->qty);
            }
            DB::table("po_mst")->where("id", $stock_inward_mst->po_id)->update(array("status" => "partial"));
            DB::table("stock_inward_mst")->where("id", $request->id)->delete();
            DB::table("stock_inward_det")->where("mst_id", $request->id)->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', "Save Successfully");
    }

    public function inwardProductWise(Request $request)
    {

        $data =  DB::table("stock_inward_mst as a")
            ->select("a.*", "b.name as vendor", "c.po_id as po_name", "d.name as location", "e.name as user", "g.name as product_name", "g.part_no", "f.price", "f.qty")
            ->join("vendor as b", "a.vendor_id", "b.id")
            ->leftJoin("po_mst as c", "a.po_id", "c.id")
            ->join("store as d", "a.location_id", "d.id")
            ->join("users as e", "a.user_id", "e.id")
            ->join("stock_inward_det as f", "a.id", "f.mst_id")
            ->join("products as g", "f.product_id", "g.id")
            ->where("a.company_id", $request->user->active_inventory)
            ->orderBy("a.id", "desc")
            ->get();


        return view("inward-product-wise", compact("data"));
    }
}
