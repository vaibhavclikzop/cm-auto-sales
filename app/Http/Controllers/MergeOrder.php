<?php

namespace App\Http\Controllers;

use App\Models\customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MergeOrder extends Controller
{
    public function MergeOrder(Request $request)
    {
        $customers =  customers::get();
        $brand =  DB::table("brand")->get();
        return view("merge-order", compact("customers", "brand"));
    }

    public function getPendingOrder(Request $request)
    {
        return  DB::table('order_mst')->where("customer_id", $request->id)->whereIn("status", ["pending", "processing"])->get();
    }
    public function getPendingOrderDetails(Request $request)
    {
        return  DB::table("order_det as a")
            ->select(DB::raw("sum(a.qty-a.out_qty) as qty"), "b.name", "c.name as brand", "b.id as product_id", "a.discount", "a.price", "b.part_no")
            ->join("products as b", "a.product_id", "b.id")
            ->join("brand as c", "b.brand_id", "c.id")
            ->whereColumn("a.qty", ">", "a.out_qty")
            ->where("a.is_delete",0)
            ->whereIn("a.mst_id", $request->order_id)
            ->groupBy("b.name", "c.name", "b.id", "a.discount", "a.price", "b.part_no")
            ->get();
    }

    public function saveMergeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'order_ids' => 'required',


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
        try {


            $customers = DB::table("customers")->where("id", $request->customer_id)->first();
           
            DB::table("order_mst")->whereIn("id", $request->order_ids)->update(array(
                "status" => "complete"
            ));
            $order_id = 'ORD_' . date('dmyhis');
            $mst_id =  DB::table('order_mst')->insertGetId(array(
                "customer_id" => $request->customer_id,
                "delivery_date" => $request->delivery_date,
                "description" => $request->description,
                "user_id" => $request->user->id,
                "company_id" => $request->user->active_inventory,
                "order_id" => $order_id,
                "city" => $customers->city,
                "coordinates" => $request->coordinates,
                "bill_address" => $customers->address,
                "bill_state" => $customers->state,
                "bill_city" => $customers->city,
                "bill_pincode" => $customers->pincode,
                "ship_address" => $customers->ship_address,
                "ship_state" => $customers->ship_state,
                "ship_city" => $customers->ship_city,
                "ship_pincode" => $customers->ship_pincode,
                "is_merge" => 1,
                "order_ids" => implode(", ", $request->order_ids),


            ));
            foreach ($prod_list as $key => $value) {
                if ($value->order_det_id) {

                    $incomingIds = collect($prod_list)
                        ->pluck('order_det_id')
                        ->filter()
                        ->toArray();

                    DB::table('order_det')
                        ->where('mst_id', $mst_id)
                        ->whereNotIn('id', $incomingIds)
                        ->update(array(
                            "is_delete" => 1
                        ));
                    $det = DB::table("order_det")->where("id", $value->order_det_id)->first();
                    $discount = 0;
                    if ($value->discount) {
                        $discount = $value->discount;
                    }
                    if (!$det) {
                        DB::table('order_det')->insertGetId(array(
                            "mst_id" => $mst_id,
                            "product_id" => $value->id,
                            "qty" => $value->qty,
                            "price" => $value->price,
                            "discount" => $discount,
                        ));
                    }
                } else {
                    $discount = 0;
                    if ($value->discount) {
                        $discount = $value->discount;
                    }

                    DB::table('order_det')->insertGetId(array(
                        "mst_id" => $mst_id,
                        "product_id" => $value->id,
                        "qty" => $value->qty,
                        "price" => $value->price,
                        "discount" => $discount,
                    ));
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
        return redirect()->back()->with('success', "Save Successfully");
    }
}
