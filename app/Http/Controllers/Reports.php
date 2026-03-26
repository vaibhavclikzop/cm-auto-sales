<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;


class Reports extends Controller
{
    public function CurrentStock(Request $request)
    {
        $location = $request->input("location");
        $search = $request->input("search");
        $record = $request->input("record", 50);
        $where = "";

        $where = DB::table("current_stock as a")
            ->select("a.*", "b.name as product", "c.name as location", "b.part_no as article_no", "d.name as brand","b.purchase_price","b.product_location")
            ->join("products as b", "a.product_id", "b.id")
            ->join("store as c", "a.location_id", "c.id")
            ->join("brand as d", "b.brand_id", "d.id")
            ->where("b.company_id", $request->user->active_inventory);
        if ($location) {
            $where->where("a.location_id", $location);
        }
        if ($search) {
            $where->where("b.name", 'like', '%' . $search . '%');
            $where->orWhere("b.part_no", 'like', '%' . $search . '%');
        }


        if ($record === "All") {
            $all = $where->get();
            $total = $all->count();
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = $total; // Show all
            $current_stock = new LengthAwarePaginator(
                $all,
                $total,
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $current_stock = $where->paginate((int) $record);
        }


        $location = DB::table("store")->get();
        $user_type = $request->user->user_type;

        return view("current-stock", compact("current_stock", "location", "user_type"));
    }

    public function SaveStock(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'qty' => 'required',


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

            DB::table('current_stock')->where("id", $request->id)->increment("stock", $request->qty);

            DB::table('stock_adjustment')->insertGetId(array(

                "cs_id" => $request->id,
                "qty" => $request->qty,

            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }

    public function GetStockAdjustmentHistory(Request $request)
    {
        $stock_adjustment =  DB::table("stock_adjustment")->where("cs_id", $request->id)->get();
        return $stock_adjustment;
    }

    public function NearMinimumStock(Request $request)
    {

        $location = $request->input("location");
        $where = "";

        $where = DB::table("current_stock as a")
            ->select("a.*", "b.name as product", "c.name as location", "b.part_no as article_no", "b.min_stock")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("store as c", "a.location_id", "=", "c.id")
            ->whereRaw("a.stock <= b.min_stock");
        if ($location) {
            $where->where("a.location_id", $location);
        }
        $current_stock = $where->get();
        $location = DB::table("store")->get();
        return view("near-by-minimum-stock", compact("current_stock", "location"));
    }

    public function AuditSetting(Request $request)
    {
        $location = DB::table("store")->get();
        return view("audit-setting", compact("location"));
    }

    public function GetCSProducts(Request $request)
    {
        $location = DB::table("current_stock as a")
            ->select("a.*", "b.name", "b.article_no")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->where("a.location_id", $request->id)->get();
        return $location;
    }


    public function SaveAuditReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required',
            'check' => 'required',


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

            $mst_id =  DB::table('audit_report_mst')->insertGetId(array(
                "date" => now(),
                "remarks" => $request->remarks,
                "user_id" => $request->user->id,
                "location_id" => $request->location_id,

            ));


            foreach ($request->check as $key => $value) {

                $current_stock =  DB::table('current_stock')->where('id', $value)->first();

                DB::table('audit_report_det')->insertGetId(array(
                    "mst_id" => $mst_id,
                    "product_id" => $current_stock->product_id,
                    "location_id" => $current_stock->location_id,
                    "current_stock" => $current_stock->stock,


                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }


    public function AuditReport(Request $request)
    {
        $audit_report_mst = DB::table("audit_report_mst as a")
            ->select("a.*", "b.name", "c.name as location")
            ->join("users as b", "a.user_id", "=", "b.id")
            ->join("store as c", "a.location_id", "=", "c.id")
            ->orderBy("a.id", "desc")
            ->get();
        return view("audit-report", compact("audit_report_mst"));
    }

    public function AuditReportView(Request $request, $id)
    {
        $audit_report_det =   DB::table("audit_report_det as a")
            ->select("a.*", "b.name as product", "c.name as location", "d.name as user")
            ->join("products as b", "a.product_id", "=", "b.id")
            ->join("store as c", "a.location_id", "=", "c.id")
            ->leftJoin("users as d", "a.user_id", "=", "d.id")
            ->where("a.mst_id", $id)
            ->get();
        return view("audit-report-view", compact("audit_report_det"));
    }

    public function SaveAudit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'stock' => 'required',


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

            DB::table('audit_report_det')->where("id", $request->id)->update(array(
                "stock" => $request->stock,
                "user_id" => $request->user->id,
                "status" => "audit",
            ));

            $audit_report_det =  DB::table("audit_report_det")->where("id", $request->id)->first();
            $audit_report =  DB::table("audit_report_det")->where("mst_id", $audit_report_det->mst_id)
                ->where("status", "pending")
                ->get();
            if ($audit_report->isEmpty()) {
                DB::table('audit_report_mst')->where("id", $audit_report_det->mst_id)->update(array(
                    "status" => "complete",



                ));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with("success", "Save successfully");
    }



    public function DefectiveStock(Request $request)

    {
        $defective_stock = DB::table("defective_stock as a")
            ->select("a.*", "b.name as product_name", "b.part_no as article_no", "c.name as location")
            ->join("products as b", "a.product_id", "b.id")
            ->join("store as c", "a.location_id", "c.id")
            ->get();
        return view("defective-stock", compact("defective_stock"));
    }

    public function AddDefectiveStock(Request $request)
    {
        $location = DB::table("store")->get();



        return view("add-defective-stock", compact("location"));
    }

    public function GetCurrentStock(Request $request)
    {
        $current_stock =   DB::table("current_stock as a")
            ->select("a.*", "b.name as product_name", "b.article_no")
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.location_id", $request->location_id)
            ->where("a.stock", ">", 0)
            ->get();
        return $current_stock;
    }

    public function SaveDefectiveStock(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'location_id' => 'required',

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



        foreach ($prod_list as $key => $value) {

            $defective_stock = DB::table("defective_stock")->where("location_id", $request->location_id)->where("product_id", $value->product_id)->first();
            if ($defective_stock) {

                DB::table("defective_stock")->where("id", $defective_stock->id)->increment("qty", $value->qty);
            } else {
                DB::table('defective_stock')->insertGetId(array(

                    "product_id" => $value->product_id,
                    "qty" => $value->qty,
                    "location_id" => $request->location_id,

                ));
            }

            DB::table("current_stock")->where("product_id", $value->product_id)->where("location_id", $request->location_id)->decrement("stock", $value->qty);
        }

        return redirect()->back()->with('success', "Save successfully");
    }




    public function ScrapStock(Request $request)
    {
        $scrap = DB::table("order_det as a")
            ->select("a.*", "c.name as product", "d.name as location")
            ->join("order_mst as b", "a.mst_id", "b.id")
            ->join("products as c", "a.product_id", "c.id")
            ->join("store as d", "b.location_id", "d.id")

            ->where("a.scrap_qty", ">", 0)
            ->orWhere("a.defective_qty", ">", 0)
            ->orWhere("a.inward_qty", ">", 0)
            ->get();
        return view("scrap-stock", compact("scrap"));
    }

    public function AddScrapStock(Request $request)
    {


        $location = DB::table("store")->get();

        return view("add-scrap-stock", compact("location"));
    }

    public function GetGenSet(Request $request)
    {
        $gen_set_mst = DB::table("order_mst as a")
            ->select("a.*", "b.name as customer")
            ->join("customers as b", "a.customer_id", "b.id")
            ->where("a.location_id", $request->location_id)
            ->where("a.status", "complete")
            ->get();

        return $gen_set_mst;
    }

    public function GetGenSetProducts(Request $request)
    {

        $gen_set_det = DB::table("order_det as a")
            ->select("a.*", "b.name as product", "b.article_no",  DB::raw("(a.qty - (a.scrap_qty + a.defective_qty + a.inward_qty)) as qty"))
            ->join("products as b", "a.product_id", "b.id")
            ->where("a.mst_id", $request->id)
            ->get();

        return $gen_set_det;
    }

    public function SaveScrapProducts(Request $request)
    {

        $prod_list = json_decode($request->prod_list);
        if (!$prod_list) {
            return redirect()->back()->with('error', "Select at least one product");
        }
        foreach ($prod_list as $key => $value) {

            // Handle file upload if file is provided for this product
            if ($request->hasFile("files.{$value->id}")) {
                $file = $request->file("files.{$value->id}");

                // Create a unique name for the file using the current timestamp
                $image = time() . '.' . $file->extension();

                // Move the file to the 'public/scrap files' directory
                $file->move(public_path('scrap files'), $image);
                DB::table("order_det")->where("id", $value->id)->update(array(
                    "file" => $image
                ));
            } else {
                echo "File not found for product ID: " . $value->id . "<br>";
            }


            $gen_set_det = DB::table("order_det")->where("id", $value->id)->first();
            DB::table("order_det")->where("id", $value->id)->increment("scrap_qty", $value->scrap_qty);
            DB::table("order_det")->where("id", $value->id)->increment("defective_qty", $value->defective_qty);
            DB::table("order_det")->where("id", $value->id)->increment("inward_qty", $value->inward_qty);


            if ($value->inward_qty > 0) {
                DB::table("current_stock")->where("location_id", $request->location_id)->where("product_id", $gen_set_det->product_id)->increment("stock", $value->inward_qty);
            }
            if ($value->defective_qty > 0) {

                $defective_stock = DB::table("defective_stock")->where("location_id", $request->location_id)->where("product_id", $gen_set_det->product_id)->first();
                if ($defective_stock) {
                    DB::table("defective_stock")->where("location_id", $request->location_id)->where("product_id", $gen_set_det->product_id)->increment("qty", $value->defective_qty);
                } else {
                    DB::table('defective_stock')->insertGetId(array(

                        "product_id" => $gen_set_det->product_id,
                        "qty" => $value->defective_qty,
                        "location_id" => $request->location_id,

                    ));
                }
            }
        }
        return redirect()->back()->with('success', "Save successfully");
    }


    public function BulkImportCS(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
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
        $count_d = 0;
        $count_e = "";
        $error = 0;
        $customer_id = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers

            foreach ($csv as $record) {

                $location =  DB::table("store")->where("name", $record[0])->first();
                $product =  DB::table("products")->where("article_no", $record[2])->first();
                if ($product) {

                    if ($location) {


                        $current_stock = DB::table('current_stock')->where("location_id", $location->id)->where("product_id", $product->id)->first();

                        if ($current_stock) {
                            DB::table('current_stock')->where("location_id", $location->id)->where("product_id", $product->id)->increment("stock", $record[3]);
                        } else {
                            DB::table('current_stock')->insertGetId(array(

                                "location_id" => $location->id,
                                "product_id" => $product->id,
                                "stock" => $record[3],


                            ));
                        }
                    } else {
                        $count_e .= "Location not found";
                        $error++;
                    }
                } else {
                    $count_e .= "Product not found";
                    $error++;
                }
                $count_d++;
            }

            return redirect()->back()->with('warning', 'CSV file uploaded and processed. Total : ' . $count_d . " Error : " . $error . " Success : " . $count_d - $error . "msg : " . $count_e);
        }

        return redirect()->back()->with('error', 'No CSV file selected for upload.');
    }

    public function poReport(Request $request)
    {
        $subStock = DB::table('current_stock')
            ->select('product_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('product_id');

        $subOrder = DB::table('order_det')
            ->select('product_id', DB::raw('SUM(qty-out_qty) as qty'))
            ->groupBy('product_id');

        $data = DB::table('po_det as a')
            ->select(
                'a.product_id',
                'b.name',
                DB::raw('SUM(a.qty - a.received_qty) as demand_qty'),
                DB::raw('COALESCE(s.total_stock, 0) as current_stock'),
                DB::raw('COALESCE(o.qty, 0) as order_qty')
            )
            ->join('products as b', 'a.product_id', '=', 'b.id')
            ->leftJoinSub($subStock, 's', 'a.product_id', '=', 's.product_id')
            ->leftJoinSub($subOrder, 'o', 'a.product_id', '=', 'o.product_id')
            ->groupBy('a.product_id', 'b.name', "s.total_stock", "o.qty")
            ->get();



        return view("po-report", compact("data"));
    }


    
}
