<?php

namespace App\Http\Controllers;

use App\Models\special_offer;
use App\Models\store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class AjaxCall extends Controller
{
    public function GetVendorProducts(Request $request)
    {
        $products = DB::table("products as a")
            ->select("a.*", "b.name as brand")
            ->join("brand as b", "a.brand_id", "b.id")
            ->whereRaw("FIND_IN_SET(?, a.vendor_id)", [$request->id])

            ->where("a.active", 1)->get();
        return $products;
    }

    public function GetPO(Request $request)
    {
        $po_mst = DB::table('po_mst')
            ->where('vendor_id', $request->id)
            ->where(function ($query) {
                $query->where('status', 'partial')
                    ->orWhere('status', 'generated');
            })
            ->get();
        return $po_mst;
    }

    public function GetPODet(Request $request)
    {

        $po_det = DB::table("po_det as a")
            ->select("a.*", "b.name as product_name", "b.part_no as article_no", "b.id as product_id", "c.name as brand", "b.product_location")
            ->join("products as b", "a.product_id", "b.id")
            ->join("brand as c", "b.brand_id", "c.id")
            ->where("mst_id", $request->id)->get();
        return $po_det;
    }

    public function GetCustomerOrder(Request $request)
    {
        $order_mst =  DB::table("order_mst")->where("customer_id", $request->id)
            ->whereNot("status", "complete")
            ->get();
        return $order_mst;
    }

    public function GetOrderDet(Request $request)
    {
        $order_det = DB::table("order_det as a")
            ->select(
                "a.*",
                "b.name as product",
                "b.part_no as article_no",
                "z.name as brand",
                DB::raw("a.out_qty as out_qty"),
                DB::raw("IFNULL(d.stock, 0) as stock")
            )
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("order_mst as c", "a.mst_id", "=", "c.id")
            ->join("brand as z", "b.brand_id", "=", "z.id")
            ->leftJoin("current_stock as d", function ($join) use ($request) {
                $join->on("a.product_id", "=", "d.product_id")
                    ->where("d.location_id", "=", $request->location_id);
            })
            ->where("a.mst_id", $request->id)
            ->where("a.is_delete", 0)
            ->whereRaw("a.qty > a.out_qty")
            ->get();

        return $order_det;
    }
    public function GetUserDetails(Request $request)
    {
        $user = DB::table("users")->where("id", $request->id)->first();;
        return $user;
    }

    public function getSpecialOffer(Request $request)
    {

        $special_offer = special_offer::where("product_id", $request->id)->first();

        $current_stock = DB::table("current_stock")
            ->select(DB::raw("SUM(stock) as stock"))
            ->where("product_id", $request->id)
            ->groupBy("product_id")
            ->first();

        $order_mst = DB::table("order_mst as a")
            ->select(DB::raw("SUM(b.qty - b.booked_qty) as qty"))
            ->join("order_det as b", "a.id", "=", "b.mst_id")
            ->whereIn("a.status", ["pending", "processing"])
            ->where("b.product_id", $request->id)
            ->groupBy("b.product_id")
            ->first();

        return response()->json([
            "specialOffer" => $special_offer->discount ?? 0,
            "cs"           => $current_stock->stock ?? 0,
            "pending_qty"  => $order_mst->qty ?? 0
        ]);
    }

    public function getLocation(Request $request)
    {

        return   store::where("warehouse_id", $request->id)->get();
    }
}
