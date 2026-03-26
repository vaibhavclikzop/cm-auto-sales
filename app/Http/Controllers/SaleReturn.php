<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleReturn extends Controller
{
    public function SaleReturnList(Request $request)
    {
        $customers =  DB::table("customers")->get();

        $data =  DB::table("sale_return_mst as a")
            ->select("a.*", "b.company as customer", "d.order_id", "e.name as user", "c.outward_id")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("stock_outward_mst as c", "a.outward_id", "c.id")
            ->join("order_mst as d", "c.order_id", "d.id")
            ->join("users as e", "a.user_id", "e.id")
            ->join("company as f", "a.company_id", "f.id")
            ->orderBy("a.id", "desc")
            ->get();

        return view("sale-return", compact("customers", "data"));
    }

    public function GetOutwardChallan(Request $request)
    {
        $data = DB::table("order_mst as a")
            ->select("a.*", "b.id as id", "b.invoice_id as outward_id")
            ->join("stock_outward_mst as b", "a.id", "b.order_id")
            ->where("a.customer_id", $request->id)
            ->where("b.is_invoice", 1)
            ->get();
        return $data;
    }

    public function GetOutwardChallanProducts(Request $request)
    {

        $data = DB::table("stock_outward_det as a")
            ->select("a.*", "b.name as product", DB::raw("a.qty-a.return_qty as qty"), "b.part_no")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.mst_id", $request->id)->get();
        return $data;
    }

    public function SaveSaleReturn(Request $request)
    {
        $po_id = 'PO_' . date('dmyhis');

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'outward_id' => 'required',

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
            $mst_id = DB::table('sale_return_mst')->insertGetId(array(
                "customer_id" => $request->customer_id,
                "user_id" => $request->user->id,
                "outward_id" => $request->outward_id,
                "return_date" => $request->return_date,
                "description" => $request->description,
                "company_id" => $request->user->active_inventory,


            ));
            $stock_outward_mst =  DB::table("stock_outward_mst")->where("id", $request->outward_id)->first();

            foreach ($prod_list as $key => $value) {
                DB::table('sale_return_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "type" => $value->type,

                ));


                DB::table('stock_outward_det')->where("mst_id", $request->outward_id)->where("product_id", $value->product_id)->increment("return_qty", $value->qty);
                if ($value->type == "scrap") {
                    $defective_stock =  DB::table("defective_stock")->where("product_id", $value->product_id)->first();
                    if ($defective_stock) {
                        DB::table("defective_stock")->where("product_id", $value->product_id)->where("location_id", $stock_outward_mst->location_id)->increment("qty", $value->qty);
                    } else {
                        DB::table("defective_stock")->insertGetId(array(
                            "product_id" => $value->product_id,
                            "qty" => $value->qty,
                            "location_id" => $stock_outward_mst->location_id,
                        ));
                    }
                } else {
                    DB::table("current_stock")->where("product_id", $value->product_id)->where("location_id", $stock_outward_mst->location_id)->increment("stock", $value->qty);
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }
    public function SaleReturnChallanView(Request $request, $id)
    {
        $po_mst =   DB::table("sale_return_mst as a")
            ->select("a.*", "b.company as customer", "d.name as user", "b.address", "b.state", "b.city", "b.pincode", "b.number", "b.email", "b.gst", "e.img", "e.name", "e.gst_no", "f.invoice_id", "e.state as c_state", "f.discount_type")
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as d", "a.user_id", "d.id")
            ->join("company as e", "a.company_id", "e.id")
            ->join("stock_outward_mst as f", "a.outward_id", "f.id")
            ->where("a.id", $id)
            ->first();

        $po_det = DB::table("sale_return_det as a")
            ->select(
                "a.*",
                "h.name as brand",
                "b.name as product_name",
                "b.part_no as part_code",
                "b.hsn_code",
                "c.name as uom",
                "f.price",
                "g.discount",

                "f.discount as special_discount",
                "g.gst"
            )
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("brand as h", "b.brand_id", "=", "h.id")
            ->join("unit_type as c", "b.uom", "=", "c.id")
            ->join("sale_return_mst as d", "a.mst_id", "=", "d.id")
            ->join("stock_outward_mst as e", "d.outward_id", "=", "e.id")

            ->join("stock_outward_det as f", function ($join) {
                $join->on("f.mst_id", "=", "e.id")
                    ->on("f.product_id", "=", "a.product_id");
            })

               ->join("order_det as g", function ($join) {
        $join->on("g.product_id", "=", "a.product_id")
             ->on("g.mst_id", "=", "e.order_id"); // 🔥 fix here
    })

            ->where("a.mst_id", $id)
            ->get();

        return view("sale-return-challan-view", compact("po_mst", "po_det"));
    }
}
