<?php

namespace App\Http\Controllers;

use App\Models\customer_type;
use App\Models\customers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;

class OrderManagement extends Controller
{
    public function NewOrder(Request $request)
    {
        $customers = DB::table("customers")->where("active", 1)->get();
        $brand = DB::table("brand")->get();
        $store = DB::table("store")->get();
        $products = DB::table("products")->get();
        $customer_type = customer_type::get();
        $id = request("id");
        $order_mst = null;
        $order_det = null;
        if ($id) {
            $order_mst = DB::table("order_mst as a")
                ->select("a.*", "b.customer_type_id as customer_type")
                ->join("customers as b", "a.customer_id", "b.id")
                ->where("a.id", $id)->first();
            $order_det = DB::table("order_det as a")
                ->select("a.*", "b.id as id", "b.name", "b.part_no", "c.name as brand_name", "a.id as order_det_id")
                ->join("products as b", "a.product_id", "b.id")
                ->join("brand as c", "b.brand_id", "c.id")
                ->where("a.mst_id", $id)
                ->where("a.is_delete", 0)
                ->get();
        }


        return view("new-order", compact("customers", "brand", "store", "products", "customer_type", "order_mst", "order_det"));
    }


    public function UploadRequirementList(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return json_encode(['error' => $error]);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $data = [];
            foreach ($csv as $record) {

                if ($record[0] == "brand") {
                    continue;
                }

                // $products =


                //     DB::table("products as a")
                //     ->select("a.*", "b.name as brand_name")
                //     ->join("brand as b", "a.brand_id", "b.id")
                //     ->where('a.part_no', $record[1])->first();


                $products = DB::table('products as a')
                    ->join("brand as c", "a.brand_id", "c.id")
                    ->leftJoin('customer_type_price as b', function ($join) use ($request) {
                        $join->on('a.id', '=', 'b.product_id')
                            ->where('b.customer_type_id', $request->customer_type);
                    })

                    ->where('a.company_id', $request->user->active_inventory)
                    ->where("a.part_no", $record[1])
                    ->select(
                        'a.*',
                        "c.name as brand_name",
                        DB::raw('COALESCE(b.price, a.sale_price) as final_price')
                    )->first();
                if ($products) {
                    $products->qty = $record[3];
                    $products->discount = $record[4];
                }
                $productIds = array_column($data, 'id');
                if (!in_array($products->id, $productIds)) {
                    $data[] = $products;
                }
            }
            return json_encode(['data' => $data]);
        }

        return json_encode(['error' => "No csv file selected for upload"]);
    }


    public function SaveNewOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
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
        $order_id = 'ORD_' . date('dmyhis');

        try {

            if ($request->id) {
                DB::table('order_mst')->where("id", $request->id)->update(array(
                    "customer_id" => $request->customer_id,
                    "delivery_date" => $request->delivery_date,
                    "description" => $request->description,
                    "user_id" => $request->user->id,
                    "company_id" => $request->user->active_inventory,

                    "city" => $request->city,
                    "coordinates" => $request->coordinates,
                    "bill_address" => $request->bill_address,
                    "bill_state" => $request->bill_state,
                    "bill_city" => $request->bill_city,
                    "bill_pincode" => $request->bill_pincode,
                    "ship_address" => $request->ship_address,
                    "ship_state" => $request->ship_state,
                    "ship_city" => $request->ship_city,
                    "ship_pincode" => $request->ship_pincode,


                ));
                $mst_id = $request->id;
            } else {
                $mst_id =  DB::table('order_mst')->insertGetId(array(
                    "customer_id" => $request->customer_id,
                    "delivery_date" => $request->delivery_date,
                    "description" => $request->description,
                    "user_id" => $request->user->id,
                    "company_id" => $request->user->active_inventory,
                    "order_id" => $order_id,
                    "city" => $request->city,
                    "coordinates" => $request->coordinates,
                    "bill_address" => $request->bill_address,
                    "bill_state" => $request->bill_state,
                    "bill_city" => $request->bill_city,
                    "bill_pincode" => $request->bill_pincode,
                    "ship_address" => $request->ship_address,
                    "ship_state" => $request->ship_state,
                    "ship_city" => $request->ship_city,
                    "ship_pincode" => $request->ship_pincode,


                ));
            }


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
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Orders(Request $request)
    {

        $status = request("status");
        $orderTotal = DB::table("order_det as od")
            ->where("od.is_delete", 0)
            ->select(
                "od.mst_id",
                DB::raw("
                ROUND(SUM(
                    ((od.qty * od.price)
                    - ((od.qty * od.price) / 100 * od.discount))
                    +
                    (
                        ((od.qty * od.price)
                        - ((od.qty * od.price) / 100 * od.discount)
                        ) / 100 * od.gst
                    )
                ),2) as totalOrderValue
            ")
            )
            ->groupBy("od.mst_id");

        $ptTotal = DB::table("stock_outward_mst as b")
            ->leftJoin("stock_outward_det as a", "a.mst_id", "b.id")

            ->leftJoinSub(
                DB::table("order_det")
                    ->select(
                        "product_id",
                        "mst_id",
                        DB::raw("MAX(discount) as discount"),
                        DB::raw("MAX(gst) as gst")
                    )
                    ->where("is_delete", 0)
                    ->groupBy("product_id", "mst_id"),
                "f",
                function ($join) {
                    $join->on("f.product_id", "=", "a.product_id")
                        ->on("f.mst_id", "=", "b.order_id");
                }
            )

            ->select(
                "b.order_id",


                DB::raw("
            ROUND(
                SUM(a.price * a.qty)
                -
                SUM(((a.price*a.qty/100)*f.discount))
                -
                SUM(
                    ((a.qty*a.price)
                    - (a.price*a.qty/100)*f.discount)
                    /100 * a.discount
                )
                +
                SUM(
                    (
                        ((a.qty*a.price)
                        - (a.price*a.qty/100)*f.discount)
                        -
                        (
                            ((a.qty*a.price)
                            - (a.price*a.qty/100)*f.discount)
                            /100 * a.discount
                        )
                    )
                    /100 * f.gst
                )
            ,2) as pt_value
        ")
            )

            ->groupBy("b.order_id");

        $order = DB::table("order_mst as a")
            ->select(
                "a.*",
                "b.name as customer",
                "c.name as user",
                "b.company",
                "ot.totalOrderValue as order_value",
                "pt.pt_value"
            )
            ->join("customers as b", "a.customer_id", "b.id")
            ->join("users as c", "a.user_id", "c.id")
            ->leftJoinSub($orderTotal, 'ot', function ($join) {
                $join->on('ot.mst_id', '=', 'a.id');
            })
            ->leftJoinSub($ptTotal, 'pt', function ($join) {
                $join->on('pt.order_id', '=', 'a.id');
            })
            ->where("a.company_id", $request->user->active_inventory)
            ->whereIn("a.user_id", $request->userIds);
        if ($status) {
            $order->where("a.status", $status);
        }
        $orders =  $order->orderBy("id", "desc")
            ->get();


        $totalOrders      = $orders->count();
        $totalOrderValue  = $orders->sum('order_value');
        $totalPtValue  = $orders->sum('pt_value');
        $totalPendingOrderValue = $totalOrderValue - $totalPtValue;
        $totalStockValue = DB::table("order_det as od")
            ->leftJoinSub(
                DB::table("stock_outward_mst as som")
                    ->leftJoin("stock_outward_det as sod", "sod.mst_id", "som.id")
                    ->select(
                        "som.order_id",
                        "sod.product_id",
                        DB::raw("SUM(sod.qty) as dispatched_qty")
                    )
                    ->groupBy("som.order_id", "sod.product_id"),
                "dispatch",
                function ($join) {
                    $join->on("dispatch.order_id", "=", "od.mst_id")
                        ->on("dispatch.product_id", "=", "od.product_id");
                }
            )
            ->leftJoin("current_stock as cs", function ($join) {
                $join->on("cs.product_id", "=", "od.product_id")
                    ->where("cs.location_id", 1);
            })
            ->select(DB::raw("
        ROUND(SUM(
            LEAST(
                GREATEST((od.qty - IFNULL(dispatch.dispatched_qty, 0)), 0),
                IFNULL(cs.stock, 0)
            ) * od.price
        ),2) as total
    "))
            ->where("od.is_delete", 0)
            ->value("total");
        return view("orders", compact(
            "orders",
            "totalOrders",
            "totalOrderValue",
            "totalStockValue",
            "totalPendingOrderValue"
        ));
    }
    public function InitiateOrder(Request $request)
    {

        $booked_qty = 0;
        $pending_qty = 0;
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',

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

        $order_mst = DB::table("order_mst")->where("id", $request->order_id)->first();
        if (!$order_mst) {
            return  redirect()->back()->with("error", "Order not found");
        }
        if ($order_mst->initiate == 0) {
            $order_det = DB::table("order_det")->where("mst_id", $order_mst->id)->get();
            if (!$order_det) {
                return  redirect()->back()->with("error", "Order product not found");
            }
            DB::table("order_mst")->where("id", $request->order_id)->update(array(
                "initiate" => 1,
                "status" => 'processing'
            ));


            $check_require = [];
            foreach ($order_det as $key => $value) {
                $po_id = 'PO_' . date('dmyhis');

                $product = DB::table("products")->where("id", $value->product_id)->first();
                if ($product->vendor_id > 0) {
                    $current_stock = DB::table("current_stock")->where("id", $value->product_id)->first();
                    if (!$current_stock) {

                        if (empty($check_require)) {
                            $mst_id = DB::table('po_mst')->insertGetId(array(
                                "vendor_id" => $product->vendor_id,
                                "user_id" => $request->user->id,
                                "order_id" => $request->order_id,
                                "po_id" => $po_id,
                            ));
                            DB::table('po_det')->insertGetId(array(
                                "mst_id" => $mst_id,
                                "product_id" => $product->id,
                                "qty" => $value->qty,
                                "price" => $product->price,
                            ));
                            $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
                        } else {
                            if (!empty($check_require)) {
                                $find_value = 0;
                                $mst_id = 0;
                                foreach ($check_require as $key) {
                                    if ($key['vendor_id'] == $product->vendor_id) {
                                        $find_value = 1;
                                        $mst_id = $key['mst_id'];
                                        break;
                                    }
                                }
                                if ($find_value == 0) {
                                    $mst_id = DB::table('po_mst')->insertGetId(array(
                                        "vendor_id" => $product->vendor_id,
                                        "user_id" => $request->user->id,
                                        "order_id" => $request->order_id,
                                        "po_id" => $po_id,
                                    ));


                                    DB::table('po_det')->insertGetId(array(
                                        "mst_id" => $mst_id,
                                        "product_id" => $product->id,

                                        "qty" => $value->qty,
                                        "price" => $product->price,
                                    ));

                                    $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
                                } else {
                                    DB::table('po_det')->insertGetId(array(
                                        "mst_id" => $mst_id,
                                        "product_id" => $product->id,

                                        "qty" => $value->qty,
                                        "price" => $product->price,
                                    ));
                                }
                            }
                        }


                        DB::table('order_det')->where("id", $value->id)->update(array(
                            "pending_qty" => $value->qty,
                        ));
                    } else {


                        $booked_qty = $current_stock->stock - $value->qty;

                        if ($booked_qty < 0) {
                            $pending_qty =   $value->qty - $current_stock->stock;

                            if (empty($check_require)) {
                                $mst_id = DB::table('po_mst')->insertGetId(array(
                                    "vendor_id" => $product->vendor_id,
                                    "user_id" => $request->user->id,
                                    "order_id" => $request->order_id,
                                    "po_id" => $po_id,
                                ));
                                DB::table('po_det')->insertGetId(array(
                                    "mst_id" => $mst_id,
                                    "product_id" => $product->id,
                                    "qty" => $value->qty,
                                    "price" => $product->price,
                                ));
                                $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
                            } else {
                                if (!empty($check_require)) {
                                    $find_value = 0;
                                    $mst_id = 0;
                                    foreach ($check_require as $key) {
                                        if ($key['vendor_id'] == $product->vendor_id) {
                                            $find_value = 1;
                                            $mst_id = $key['mst_id'];
                                            break;
                                        }
                                    }
                                    if ($find_value == 0) {
                                        $mst_id = DB::table('po_mst')->insertGetId(array(
                                            "vendor_id" => $product->vendor_id,
                                            "user_id" => $request->user->id,
                                            "order_id" => $request->order_id,
                                            "po_id" => $po_id,
                                        ));


                                        DB::table('po_det')->insertGetId(array(
                                            "mst_id" => $mst_id,
                                            "product_id" => $product->id,

                                            "qty" => $value->qty,
                                            "price" => $product->price,
                                        ));

                                        $check_require[] = array("vendor_id" => $product->vendor_id, "mst_id" => $mst_id);
                                    } else {
                                        DB::table('po_det')->insertGetId(array(
                                            "mst_id" => $mst_id,
                                            "product_id" => $product->id,

                                            "qty" => $value->qty,
                                            "price" => $product->price,
                                        ));
                                    }
                                }
                            }

                            DB::table('current_stock')->where("product_id", $value->product_id)->where("location_id", $order_mst->location_id)->update([
                                'stock' => DB::raw('stock - ' . $current_stock->stock),
                                // other fields to update
                            ]);;

                            DB::table('order_det')->where("id", $value->id)->update(array(
                                "pending_qty" => $pending_qty,
                                "booked_qty" => $current_stock->stock,
                            ));
                        } else {

                            DB::table('current_stock')->where("product_id", $value->product_id)->where("location_id", $order_mst->location_id)->update([
                                'stock' => DB::raw('stock - ' . $value->qty),
                                // other fields to update
                            ]);;

                            DB::table('order_det')->where("id", $value->id)->update(array(

                                "booked_qty" => $value->qty,
                            ));
                        }
                    }
                }
            }
        } else {
            return  redirect()->back()->with("error", "Already Initiated");
        }

        die;
    }

    public function OrderView(Request $request, $id)
    {

        if (empty($id)) {
            return  redirect()->back()->with("error", "ID not found");
        }

        $status = request("status");

        $data = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer_name", "b.number", "b.email", "b.gst", "b.address as bill_address", "b.state", "b.city as bill_city", "b.pincode as bill_pincode", "c.img", "c.name", "c.gst_no", "c.email as c_email", "c.number as c_number", "c.address as c_address", "b.company as company_name", "c.bank_name", "c.branch_name", "c.account_number", "c.ifsc_code", "a.created_at", "c.state as c_state", "b.ship_address", "b.ship_state", "b.ship_city", "b.ship_pincode")
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
        $od->where("a.is_delete", 0);

        $order_det = $od->get();




        return view("order-view", compact("data", "order_det"));
    }

    public function piOrderView(Request $request, $id)
    {
        if (empty($id)) {
            return  redirect()->back()->with("error", "ID not found");
        }
        $order_mst = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer_name", "b.number", "b.email", "b.gst", "b.address", "b.state", "b.city", "b.pincode")
            ->join("customers as b", "a.customer_id", "b.id")
            ->where("a.id", $id)
            ->first();

        $order_det = DB::table("order_det as a")
            ->select(
                "b.name as product",
                "b.article_no",
                "a.qty",
                "a.price",
                "e.stock as cs",
                "a.discount as discount",
                DB::raw("SUM(d.qty) as out_qty")
            )
            ->join("order_mst as f", "a.mst_id", "f.id")
            ->join("products as b", "a.product_id", "b.id")
            ->leftJoin("stock_outward_det as d", function ($join) {
                $join
                    ->on("a.product_id", "=", "d.product_id");
            })
            ->leftJoin("current_stock as e", function ($join) {
                $join->on("a.product_id", "=", "e.product_id")
                    ->on("f.location_id", "=", "e.location_id");
            })
            ->where("a.mst_id", $id)
            ->groupBy("b.name", "b.article_no", "a.qty", "a.price", "e.stock", "a.discount")
            ->get();




        return view("pi-order-view", compact("order_mst", "order_det"));
    }

    public function getCustomer(Request $request)
    {

        return   customers::where("customer_type_id", $request->id)
            ->where("company_id", $request->user->active_inventory)
            ->get();
    }

    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
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

            DB::table("order_mst")->where("id", $request->order_id)->update(array(
                "status" => "cancel"
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function UploadPORequirementList(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return json_encode(['error' => $error]);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            // $data = [];
            foreach ($csv as $record) {

                if ($record[0] == "brand") {
                    continue;
                }
                $part_no = $record[1];
                $name    = $record[2];
                $qty     = $record[3];

                $products = DB::table("products as a")
                    ->select("a.*", "b.name as brand_name")
                    ->join("brand as b", "a.brand_id", "b.id")
                    ->where('a.part_no', $record[1])->first();
                if ($products) {
                    // $products->qty = $record[3];
                    $data[] = [
                        'id' => $products->id,
                        'name' => $products->name,
                        'part_no' => $products->part_no,
                        'qty' => $qty,
                        'purchase_price' => $products->purchase_price,
                        'gst' => $products->gst,
                        'found' => true
                    ];
                } else {

                    // ✅ NOT FOUND CASE
                    $data[] = [
                        'id' => null,
                        'name' => $name, // CSV se
                        'part_no' => $part_no,
                        'qty' => $qty,
                        'purchase_price' => null,
                        'gst' => null,
                        'found' => false
                    ];
                }

                // $productIds = array_column($data, 'id');

                // $data[] = $products;
            }
            return json_encode(['data' => $data]);
        }

        return json_encode(['error' => "No csv file selected for upload"]);
    }


    public function scrapOrder(Request $request)
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

            DB::table("order_mst")->where("id", $request->id)->update(array(
                "status" => "complete"
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }
}
