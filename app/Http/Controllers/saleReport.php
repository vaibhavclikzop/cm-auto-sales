<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\products;
use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class saleReport extends Controller
{
    public function saleReportTally(Request $request)
    {

        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));

        $data = DB::table('stock_outward_mst as a')
            ->select(
                "a.id as voucher_no",
                "a.invoice_date",
                "e.company as customer",
                "a.invoice_id",
                "a.created_at as date",
                "e.gst as gst_no",
                DB::raw("'Sale Report' as particular"),
                DB::raw("'Sales' as voucher_type"),
                DB::raw("
                sum(
                ((b.price*b.qty)-(b.price*b.qty/100)*d.discount)
                ) as total
                "),
                DB::raw("
   round( SUM(
        (
            (b.price * b.qty)
            - ((b.price * b.qty) / 100 * d.discount)
        )
        -
        (
            (
                (b.price * b.qty)
                - ((b.price * b.qty) / 100 * d.discount)
            ) / 100 * b.discount
        )
    ),2) AS taxable_amount
"),
                DB::raw("
    CASE
        WHEN e.state = f.state THEN 'CGST'
        ELSE 'IGST'
    END AS gst_type
"),
                DB::raw("
   round( SUM(
        (((
            (b.price * b.qty)
            - ((b.price * b.qty) / 100 * d.discount)
        )
        -
        (
            (
                (b.price * b.qty)
                - ((b.price * b.qty) / 100 * d.discount)
            ) / 100 * b.discount
        )))/100*d.gst
             
    ) ,2)AS gst
"),



            )
            ->join('stock_outward_det as b', 'a.id', '=', 'b.mst_id')
            ->join('order_mst as c', 'a.order_id', '=', 'c.id')
            ->join('order_det as d', function ($join) {
                $join->on('d.product_id', '=', 'b.product_id')
                    ->on('d.mst_id', '=', 'c.id');
            })
            ->join("customers as e", "c.customer_id", "e.id")
            ->join("company as f", "a.store_id", "f.id")
            ->where("a.is_invoice", 1)
            ->whereDate("a.invoice_date", ">=", $fromDate)
            ->whereDate("a.invoice_date", "<=", $toDate)
            ->where("a.store_id", $request->user->active_inventory)
            ->groupBy("a.id", "a.invoice_date", "e.company", "a.invoice_id", "a.created_at", "e.gst", "e.state", "f.state")
            ->get();


        return view("sale-report-tally", compact("data"));
    }


    public function saleReport(Request $request)
    {

        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $customer_id = request("customer_id");
        $city1 = request("city");

        $customers =  customers::get();
        $city =  DB::table("customers")->select("city1")->distinct("city1")->get();
        $data1 = DB::table('stock_outward_mst as a')
            ->select(
                "a.id as voucher_no",
                "a.invoice_convert_date",
                "e.company as customer",
                "a.invoice_id",
                "a.created_at as date",
                "e.gst as gst_no",
                DB::raw("'Sale Report' as particular"),
                DB::raw("'Sales' as voucher_type"),

                // ✅ TOTAL
                DB::raw("ROUND(SUM(b.price * b.qty), 2) as total"),

                // ✅ DISCOUNT (COMBINED LOGIC)
                DB::raw("
            ROUND(SUM(
                CASE 
                    WHEN a.discount_type = 'price'
                        THEN (b.price * b.qty) * ((d.discount + b.discount) / 100)
                    ELSE (b.price * b.qty) * d.discount / 100
                END
            ), 2) as discount
        "),

                // ✅ DISCOUNT 2 (ONLY WHEN discount_type = discount)
                DB::raw("
            ROUND(SUM(
                CASE 
                    WHEN a.discount_type = 'price'
                        THEN 0
                    ELSE (
                        (
                            (b.price * b.qty)
                            - ((b.price * b.qty) * d.discount / 100)
                        ) * b.discount / 100
                    )
                END
            ), 2) as discount2
        "),

                // ✅ TAXABLE
                DB::raw("
            ROUND(SUM(
                ROUND(
                    CASE 
                        WHEN a.discount_type = 'price'
                            THEN
                                (b.price * b.qty)
                                - (
                                    (b.price * b.qty) * ((d.discount + b.discount) / 100)
                                )
                        ELSE
                            (
                                (b.price * b.qty)
                                - ((b.price * b.qty) * d.discount / 100)
                                - (
                                    (
                                        (b.price * b.qty)
                                        - ((b.price * b.qty) * d.discount / 100)
                                    ) * b.discount / 100
                                )
                            )
                    END
                , 2)
            ), 2) as taxable_amount
        "),

                // ✅ GST TYPE
                DB::raw("
            CASE
                WHEN e.state = f.state THEN 'CGST'
                ELSE 'IGST'
            END AS gst_type
        "),

                // ✅ GST (NO DOUBLE ROUND)
                DB::raw("
            ROUND(SUM(
                CASE 
                    WHEN a.discount_type = 'price'
                        THEN
                            (
                                (b.price * b.qty)
                                - (
                                    (b.price * b.qty) * ((d.discount + b.discount) / 100)
                                )
                            ) * d.gst / 100
                    ELSE
                        (
                            (
                                (b.price * b.qty)
                                - ((b.price * b.qty) * d.discount / 100)
                                - (
                                    (
                                        (b.price * b.qty)
                                        - ((b.price * b.qty) * d.discount / 100)
                                    ) * b.discount / 100
                                )
                            ) * d.gst / 100
                        )
                END
            ), 2) as gst
        ")
            )

            ->join('stock_outward_det as b', 'a.id', '=', 'b.mst_id')

            ->join('order_mst as c', 'a.order_id', '=', 'c.id')

            // ✅ FIX DUPLICATION
            ->join(DB::raw("
        (SELECT 
            product_id, 
            mst_id, 
            MAX(discount) as discount, 
            MAX(gst) as gst
         FROM order_det
         GROUP BY product_id, mst_id
        ) as d
    "), function ($join) {
                $join->on('d.product_id', '=', 'b.product_id')
                    ->on('d.mst_id', '=', 'c.id');
            })

            ->join("customers as e", "c.customer_id", "e.id")
            ->join("company as f", "a.store_id", "f.id")

            ->where("a.is_invoice", 1)

            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween(DB::raw("DATE(a.invoice_convert_date)"), [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->whereNull("a.invoice_convert_date")
                            ->whereBetween(DB::raw("DATE(a.invoice_date)"), [$fromDate, $toDate]);
                    });
            })

            ->where("a.store_id", $request->user->active_inventory);

        // FILTERS
        if ($customer_id) {
            $data1->where("e.id", $customer_id);
        }

        if ($city1) {
            $data1->where("e.city1", $city1);
        }

        // GROUP
        $data = $data1->groupBy(
            "a.id",
            "a.invoice_convert_date",
            "e.company",
            "a.invoice_id",
            "a.created_at",
            "e.gst",
            "e.state",
            "f.state"
        )->get();




        // echo "<pre>";
        // print_r($data);
        // die;




        $monthlyData1 = DB::table('stock_outward_mst as a')
            ->select(
                DB::raw("MONTH(a.invoice_convert_date) as month_no"),
                DB::raw("MONTHNAME(a.invoice_convert_date) as month_name"),

                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN a.discount_type = 'price'
                THEN
                    (
                        -- taxable
                        (b.price * b.qty)
                        - ((b.price * b.qty) * ((d.discount + b.discount) / 100))
                    )
                    +
                    (
                        -- gst
                        (
                            (b.price * b.qty)
                            - ((b.price * b.qty) * ((d.discount + b.discount) / 100))
                        ) * d.gst / 100
                    )

                ELSE
                    (
                        -- taxable
                        (b.price * b.qty)
                        - ((b.price * b.qty) * d.discount / 100)
                        - (
                            (
                                (b.price * b.qty)
                                - ((b.price * b.qty) * d.discount / 100)
                            ) * b.discount / 100
                        )
                    )
                    +
                    (
                        -- gst
                        (
                            (
                                (b.price * b.qty)
                                - ((b.price * b.qty) * d.discount / 100)
                                - (
                                    (
                                        (b.price * b.qty)
                                        - ((b.price * b.qty) * d.discount / 100)
                                    ) * b.discount / 100
                                )
                            ) * d.gst / 100
                        )
                    )
            END
        ),2) AS grand_total
        ")
            )

            ->join('stock_outward_det as b', 'a.id', '=', 'b.mst_id')
            ->join('order_mst as c', 'a.order_id', '=', 'c.id')

            // ✅ FIX DUPLICATE ISSUE
            ->join(DB::raw("
        (SELECT 
            product_id, 
            mst_id, 
            MAX(discount) as discount, 
            MAX(gst) as gst
         FROM order_det
         GROUP BY product_id, mst_id
        ) as d
    "), function ($join) {
                $join->on('d.product_id', '=', 'b.product_id')
                    ->on('d.mst_id', '=', 'c.id');
            })

            ->join("customers as e", "c.customer_id", "e.id")

            ->where("a.is_invoice", 1)

            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween(DB::raw("DATE(a.invoice_convert_date)"), [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->whereNull("a.invoice_convert_date")
                            ->whereBetween(DB::raw("DATE(a.invoice_date)"), [$fromDate, $toDate]);
                    });
            })

            ->where("a.store_id", $request->user->active_inventory);

        // FILTERS
        if ($customer_id) {
            $monthlyData1->where("c.customer_id", $customer_id);
        }

        if ($city1) {
            $monthlyData1->where("e.city1", $city1);
        }

        // GROUP BY MONTH
        $monthlyData = $monthlyData1
            ->groupBy(
                DB::raw("MONTH(a.invoice_convert_date)"),
                DB::raw("MONTHNAME(a.invoice_convert_date)")
            )
            ->orderBy(DB::raw("MONTH(a.invoice_convert_date)"))
            ->get();

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $graphData = [];

        foreach ($months as $monthNo => $monthName) {
            $record = $monthlyData->firstWhere('month_no', $monthNo);

            $graphData[] = [
                'month' => $monthName,
                'total' => $record ? (float)$record->grand_total : 0
            ];
        }

        return view("sale-report", compact("data", "customers", "graphData", "city"));
    }

    public function customerWiseSaleReport(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $customer_id = request("customer_id");

        $customers =  customers::get();

        $data1 = DB::table('stock_outward_mst as a')
            ->select(
                "a.customer_id",
                "e.company as customer",
                "e.gst as gst_no",
                DB::raw("'Sale Report' as particular"),
                DB::raw("'Sales' as voucher_type"),

                // ✅ TOTAL
                DB::raw("ROUND(SUM(b.price * b.qty), 2) as total"),

                // ✅ DISCOUNT
                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN a.discount_type = 'price'
                    THEN (b.price * b.qty) * ((d.discount + b.discount) / 100)
                ELSE (b.price * b.qty) * d.discount / 100
            END
        ), 2) as discount
        "),

                // ✅ DISCOUNT 2
                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN a.discount_type = 'price'
                    THEN 0
                ELSE (
                    (
                        (b.price * b.qty)
                        - ((b.price * b.qty) * d.discount / 100)
                    ) * b.discount / 100
                )
            END
        ), 2) as discount2
        "),

                // ✅ TAXABLE
                DB::raw("
        ROUND(SUM(
            ROUND(
                CASE 
                    WHEN a.discount_type = 'price'
                        THEN
                            (b.price * b.qty)
                            - ((b.price * b.qty) * ((d.discount + b.discount) / 100))
                    ELSE
                        (
                            (b.price * b.qty)
                            - ((b.price * b.qty) * d.discount / 100)
                            - (
                                (
                                    (b.price * b.qty)
                                    - ((b.price * b.qty) * d.discount / 100)
                                ) * b.discount / 100
                            )
                        )
                END
            ,2)
        ), 2) as taxable_amount
        "),

                // ✅ GST TYPE
                DB::raw("
        CASE
            WHEN e.state = f.state THEN 'CGST'
            ELSE 'IGST'
        END AS gst_type
        "),

                // ✅ GST (NO INNER ROUND)
                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN a.discount_type = 'price'
                    THEN
                        (
                            (b.price * b.qty)
                            - ((b.price * b.qty) * ((d.discount + b.discount) / 100))
                        ) * d.gst / 100
                ELSE
                    (
                        (
                            (b.price * b.qty)
                            - ((b.price * b.qty) * d.discount / 100)
                            - (
                                (
                                    (b.price * b.qty)
                                    - ((b.price * b.qty) * d.discount / 100)
                                ) * b.discount / 100
                            )
                        ) * d.gst / 100
                    )
            END
        ), 2) as gst
        ")
            )

            ->join('stock_outward_det as b', 'a.id', '=', 'b.mst_id')
            ->join('order_mst as c', 'a.order_id', '=', 'c.id')

            // ✅ FIX DUPLICATE ISSUE
            ->join(DB::raw("
        (SELECT 
            product_id, 
            mst_id, 
            MAX(discount) as discount, 
            MAX(gst) as gst
         FROM order_det
         GROUP BY product_id, mst_id
        ) as d
    "), function ($join) {
                $join->on('d.product_id', '=', 'b.product_id')
                    ->on('d.mst_id', '=', 'c.id');
            })

            ->join("customers as e", "c.customer_id", "e.id")
            ->join("company as f", "a.store_id", "f.id")

            ->where("a.is_invoice", 1)
            ->where("a.store_id", $request->user->active_inventory)
            ->whereDate("a.invoice_convert_date", ">=", $fromDate)
            ->whereDate("a.invoice_convert_date", "<=", $toDate);

        // FILTER
        if ($customer_id) {
            $data1->where("e.id", $customer_id);
        }

        // GROUP
        $data = $data1->groupBy(
            "e.company",
            "e.gst",
            "e.state",
            "f.state",
            "a.customer_id"
        )->get();
        $pieData = [];

        foreach ($data as $row) {
            $pieData[] = [
                'customer' => $row->customer,
                'total' => round(($row->taxable_amount + $row->gst), 2)
            ];
        }


        return view("customer-wise-sale-report", compact("customers", "data", "pieData"));
    }

    public function productWiseSaleReport(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $dt =  DB::table("stock_outward_det as a")
            ->select(
                "c.name",
                "c.part_no",
                DB::raw("round(sum(a.price),2) as price"),
                DB::raw("round(sum(a.price*a.qty),2) as amount"),
                DB::raw("sum(a.qty) as qty"),
                DB::raw("round(sum(((a.price*a.qty/100)*f.discount)),2) as first_discount"),
                DB::raw("round(sum(((a.qty*a.price)-(a.price*a.qty/100)*f.discount)/100*a.discount),2) as discount"),
                DB::raw("round(sum((((a.qty*a.price)-(a.price*a.qty/100)*f.discount)-(((a.qty*a.price)-(a.price*a.qty/100)*f.discount)/100*a.discount))/100*f.gst),2) as gst"),
            )
            ->join("stock_outward_mst as b", "a.mst_id", "b.id")
            ->join("products as c", "a.product_id", "c.id")
            ->join('order_mst as d', 'b.order_id', '=', 'd.id')
            ->join('order_det as f', function ($join) {
                $join->on('f.product_id', '=', 'a.product_id')
                    ->on('f.mst_id', '=', 'd.id');
            });


        if (request("type") != null) {
            $dt->where("b.is_invoice", request("type"));
        }

        $data = $dt
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween(DB::raw("DATE(b.invoice_convert_date)"), [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->whereNull("b.invoice_convert_date")
                            ->whereBetween(DB::raw("DATE(b.invoice_date)"), [$fromDate, $toDate]);
                    });
            })


            ->where("b.store_id", $request->user->active_inventory)
            ->groupBy("a.product_id", "c.name", "c.part_no")
            ->get();
        // echo "<pre>";
        // print_r($data);
        // die;

        return view("product-wise-sale-report", compact("data"));
    }

    public function customerProductReport(Request $request)
    {

        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $customer_id = request("customer_id");
        $user_id = request("user_id");
        $search = request("search");

        $customers =  customers::get();
        $dt = DB::table("stock_outward_det as a")
            ->select(
                "b.invoice_date as pt_date",
                "b.invoice_convert_date as invoice_date",
                "d.created_at as order_date",
                "c.name",
                "c.part_no",
                "x.company as company",
                "b.invoice_id",
                "u.name as user",
                "x.city1",
                "x.city",

                // ✅ PRICE
                DB::raw("ROUND(SUM(a.price),2) as price"),

                // ✅ AMOUNT
                DB::raw("ROUND(SUM(a.price * a.qty),2) as amount"),

                // ✅ QTY
                DB::raw("SUM(a.qty) as qty"),

                // ✅ FIRST DISCOUNT
                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN b.discount_type = 'price'
                    THEN (a.price * a.qty) * ((f.first_discount + a.discount) / 100)
                ELSE (a.price * a.qty) * f.first_discount / 100
            END
        ),2) as first_discount
        "),

                // ✅ SECOND DISCOUNT
                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN b.discount_type = 'price'
                    THEN 0
                ELSE (
                    (
                        (a.price * a.qty)
                        - ((a.price * a.qty) * f.first_discount / 100)
                    ) * a.discount / 100
                )
            END
        ),2) as discount
        "),

                // ✅ GST
                DB::raw("
        ROUND(SUM(
            CASE 
                WHEN b.discount_type = 'price'
                THEN
                    (
                        (a.price * a.qty)
                        - ((a.price * a.qty) * ((f.first_discount + a.discount) / 100))
                    ) * f.gst / 100

                ELSE
                    (
                        (
                            (a.price * a.qty)
                            - ((a.price * a.qty) * f.first_discount / 100)
                            - (
                                (
                                    (a.price * a.qty)
                                    - ((a.price * a.qty) * f.first_discount / 100)
                                ) * a.discount / 100
                            )
                        ) * f.gst / 100
                    )
            END
        ),2) as gst
        ")
            )

            ->join("stock_outward_mst as b", "a.mst_id", "b.id")
            ->join("products as c", "a.product_id", "c.id")
            ->join("order_mst as d", "b.order_id", "d.id")

            // ✅ FIXED JOIN (NO DUPLICATE)
            ->joinSub(
                DB::table("order_det")
                    ->select(
                        "product_id",
                        "mst_id",
                        DB::raw("MAX(discount) as first_discount"),
                        DB::raw("MAX(gst) as gst")
                    )
                    ->groupBy("product_id", "mst_id"),
                "f",
                function ($join) {
                    $join->on("f.product_id", "=", "a.product_id")
                        ->on("f.mst_id", "=", "d.id");
                }
            )

            ->join("customers as x", "b.customer_id", "x.id")
            ->join("company as y", "b.store_id", "y.id")
            ->leftJoin("users as u", "x.dsr", "u.user_name")

            ->where("b.is_invoice", 1)

            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween(DB::raw("DATE(b.invoice_convert_date)"), [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->whereNull("b.invoice_convert_date")
                            ->whereBetween(DB::raw("DATE(b.invoice_date)"), [$fromDate, $toDate]);
                    });
            })

            ->where("b.store_id", $request->user->active_inventory);

        // FILTERS
        if ($customer_id) {
            $dt->where("x.id", $customer_id);
        }

        if ($user_id) {
            $dt->where("b.user_id", $user_id);
        }

        if ($search) {
            $dt->where(function ($q) use ($search) {
                $q->where('c.part_no', 'LIKE', "%{$search}%")
                    ->orWhere('c.name', 'LIKE', "%{$search}%")
                    ->orWhere('c.hsn_code', 'LIKE', "%{$search}%");
            });
        }

        // GROUP
        $data = $dt->groupBy(
            "a.product_id",
            "c.name",
            "c.part_no",
            "x.company",
            "b.invoice_id",
            "u.name",
            "b.invoice_date",
            "b.invoice_convert_date",
            "d.created_at",
            "x.city",
            "x.city1"
        )->get();

        // echo "<pre>";
        // print_r($data);
        // die;

        $users = users::get();
        return view("customer-product-wise-sale-report", compact("data", "customers", "users"));
    }

    public function dsrReport(Request $request)
    {

        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $customer_id = request("customer_id");
        $user_id = request("user_id");
        $search = request("search");

        $customers =  customers::get();
        $dt =  DB::table("stock_outward_det as a")
            ->select(

                "u.name as user",
                DB::raw("round(sum(a.price),2) as price"),
                DB::raw("round(sum(a.price*a.qty),2) as amount"),
                DB::raw("sum(a.qty) as qty"),
                DB::raw("round(sum(((a.price*a.qty/100)*f.discount)),2) as first_discount"),
                DB::raw("round(sum(((a.qty*a.price)-(a.price*a.qty/100)*f.discount)/100*a.discount),2) as discount"),
                DB::raw("round(sum((((a.qty*a.price)-(a.price*a.qty/100)*f.discount)-(((a.qty*a.price)-(a.price*a.qty/100)*f.discount)/100*a.discount))/100*f.gst),2) as gst"),
            )
            ->join("stock_outward_mst as b", "a.mst_id", "b.id")
            ->join("products as c", "a.product_id", "c.id")
            ->join('order_mst as d', 'b.order_id', '=', 'd.id')
            ->join('order_det as f', function ($join) {
                $join->on('f.product_id', '=', 'a.product_id')
                    ->on('f.mst_id', '=', 'd.id');
            })
            ->join("customers as x", "b.customer_id", "x.id")
            ->join("company as y", "b.store_id", "y.id")
            ->join("users as u", "b.user_id", "u.id")
            ->where("b.is_invoice", 1)
            ->whereDate("b.invoice_convert_date", ">=", $fromDate)
            ->whereDate("b.invoice_convert_date", "<=", $toDate)
            ->where("b.store_id", $request->user->active_inventory);
        if ($customer_id) {
            $dt->where("x.id", $customer_id);
        }
        if ($user_id) {
            $dt->where("b.user_id", $user_id);
        }
        if ($search) {
            $dt->where(function ($q) use ($search) {
                $q->where('c.part_no', 'LIKE', "%{$search}%")
                    ->orWhere('c.name', 'LIKE', "%{$search}%")
                    ->orWhere('c.hsn_code', 'LIKE', "%{$search}%");
            });
        }



        $data = $dt->groupBy("u.name", "b.user_id")
            ->get();

        $users = users::get();
        return view("dsr-wise-report", compact("data", "users"));
    }

    public function orderVsStock(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));

        $outward = DB::table("stock_outward_mst as d")
            ->select(
                "d.order_id",
                "d.status",
                "e.product_id",
                DB::raw("SUM(CASE WHEN d.is_invoice = 1 THEN e.qty ELSE 0 END) as invoice_qty"),
                DB::raw("SUM(CASE WHEN d.is_invoice = 0 and d.status !='cancel' THEN e.qty ELSE 0 END) as picked_qty"),
                DB::raw("SUM(CASE WHEN d.status = 'cancel' and d.is_invoice=0 THEN e.qty ELSE 0 END) as cancel_qty")
            )
            ->join("stock_outward_det as e", "d.id", "e.mst_id")
            ->groupBy("d.order_id", "d.status", "e.product_id");
        $stock = DB::table("current_stock")
            ->select(
                "product_id",
                DB::raw("SUM(stock) as stock")
            )
            ->groupBy("product_id");


        $data = DB::table("order_det as a")
            ->select(
                "a.product_id",
                "b.name",
                "b.part_no",
                "x.created_at",
                "y.company as customer",
                "z.name as type",
                "y.party_code",
                "x.order_id",
                "b.product_location",
                "b.sale_price",
                "x.status",
                DB::raw("SUM(a.qty) as ordered_qty"),
                DB::raw("SUM(a.out_qty) as order_invoice_qty"),
                DB::raw("SUM(a.qty - a.out_qty) as pending_qty"),
                DB::raw("COUNT(DISTINCT x.id) as total_order"),
                DB::raw("COALESCE(s.stock, 0) as stock"),
                DB::raw("COALESCE(o.invoice_qty, 0) as invoice_qty"),
                DB::raw("COALESCE(o.picked_qty, 0) as picked_qty"),
                DB::raw("COALESCE(o.cancel_qty, 0) as cancel_qty")
            )
            ->join("products as b", "a.product_id", "b.id")
            ->join("order_mst as x", "a.mst_id", "x.id")
            ->join("customers as y", "x.customer_id", "y.id")
            ->join("customer_type as z", "y.customer_type_id", "z.id")
            ->leftJoinSub($outward, "o", function ($join) {
                $join->on("x.id", "=", "o.order_id")
                    ->on("a.product_id", "=", "o.product_id");
            })
            ->leftJoinSub($stock, "s", function ($join) {
                $join->on("a.product_id", "=", "s.product_id");
            })


            ->where("a.is_delete", 0)
            ->whereDate("x.created_at", ">=", $fromDate)
            ->whereDate("x.created_at", "<=", $toDate)
            ->where("x.company_id", $request->user->active_inventory)
            ->where("y.party_code", "!=", 161)

            ->groupBy(
                "a.product_id",
                "b.name",
                "b.part_no",
                "x.created_at",
                "y.company",
                "z.name",
                "y.party_code",
                "x.order_id",
                "b.product_location",
                "b.sale_price",
                "x.status",
                "s.stock",
                "o.invoice_qty",
                "o.picked_qty",
                "o.cancel_qty"
            )
            ->get();




        // echo "<pre>";
        // print_r($data);
        // die;

        return view("order-vs-stock", compact("data"));
    }

    public function slowFastMovingProducts(Request $request)
    {

        $type = request("type", "desc");

        $data =    DB::table("stock_outward_det as a")
            ->select(
                "a.product_id",
                "b.name",
                "b.part_no",
                DB::raw("COALESCE(sum(a.qty),0) as qty")
            )
            ->rightJoin("products as b", "a.product_id", "b.id")
            ->where("b.company_id", $request->user->active_inventory)
            ->groupBy("a.product_id", "b.name", "b.part_no")
            ->orderBy("qty", $type)
            ->get();
        // echo "<pre>";
        // print_r($data);
        // die;

        return view("slow-fast-moving-products", compact("data"));
    }

    public function categoryWiseReport(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $data =  DB::table("stock_outward_det as a")
            ->select(
                "fp.name",

                DB::raw("round(sum(a.price),2) as price"),
                DB::raw("round(sum(a.price*a.qty),2) as amount"),
                DB::raw("sum(a.qty) as qty"),
                DB::raw("round(sum(((a.price*a.qty/100)*f.discount)),2) as first_discount"),
                DB::raw("round(sum(((a.qty*a.price)-(a.price*a.qty/100)*f.discount)/100*a.discount),2) as discount"),
                DB::raw("round(sum((((a.qty*a.price)-(a.price*a.qty/100)*f.discount)-(((a.qty*a.price)-(a.price*a.qty/100)*f.discount)/100*a.discount))/100*f.gst),2) as gst"),
            )
            ->join("stock_outward_mst as b", "a.mst_id", "b.id")
            ->join("products as c", "a.product_id", "c.id")
            ->join('order_mst as d', 'b.order_id', '=', 'd.id')
            ->join('order_det as f', function ($join) {
                $join->on('f.product_id', '=', 'a.product_id')
                    ->on('f.mst_id', '=', 'd.id');
            })
            ->join("category as fp", "c.category_id", "fp.id")
            ->where("b.is_invoice", 1)
            ->whereDate("b.invoice_convert_date", ">=", $fromDate)
            ->whereDate("b.invoice_convert_date", "<=", $toDate)
            ->where("b.store_id", $request->user->active_inventory)
            ->groupBy("fp.name", "fp.id")
            ->get();
        // echo "<pre>";
        // print_r($data);
        // die;
        return view("category-wise-report", compact("data"));
    }

    public function purchaseReport(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $data = DB::table("stock_inward_mst as a")
            ->select(
                "a.id",
                "c.name",
                "a.invoice_no",
                "a.invoice_date",
                "a.received_material_date",
                DB::raw("sum(b.qty*b.price) as amount"),
                DB::raw("sum(b.qty) as qty"),
                DB::raw("sum(b.price) as price")
            )
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->whereDate("a.invoice_date", ">=", $fromDate)
            ->whereDate("a.invoice_date", "<=", $toDate)
            ->where("a.company_id", $request->user->active_inventory)
            ->groupBy("a.id", "c.name", "a.invoice_no", "a.invoice_date", "a.received_material_date")
            ->get();
        return view("purchase-report", compact("data"));
    }

    public function purchaseReportProductWise(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $data = DB::table("stock_inward_mst as a")
            ->select(
                "a.id",
                "c.name",
                "a.invoice_no",
                "a.invoice_date",
                "a.received_material_date",
                "d.name as product",
                "b.qty",
                "b.price",
                "d.part_no as part_code"


            )
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("products as d", "b.product_id", "d.id")
            ->whereDate("a.invoice_date", ">=", $fromDate)
            ->whereDate("a.invoice_date", "<=", $toDate)
            ->where("a.company_id", $request->user->active_inventory)

            ->get();
        return view("purchase-report-product-wise", compact("data"));
    }

    public function purchaseReturnReport(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $data = DB::table("purchase_return_mst as a")
            ->select(
                "a.id",
                "c.name",
                "a.id as invoice_id",
                "a.return_date as invoice_date",
                "c.name as product",
                "f.invoice_no as invoice_no",
                "d.name as product",
                "d.part_no as part_no",
                DB::raw("sum(b.qty) as qty")
            )
            ->join("purchase_return_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("products as d", "b.product_id", "d.id")
            ->join("stock_inward_mst as f", "a.inward_id", "f.id")
            ->whereDate("a.return_date", ">=", $fromDate)
            ->whereDate("a.return_date", "<=", $toDate)
            ->where("a.company_id", $request->user->active_inventory)
            ->groupBy("a.id", "c.name", "a.id", "a.return_date", "d.name", "f.invoice_no", "d.name", "d.part_no")
            ->get();
        return view("purchase-return-report", compact("data"));
    }

    public function saleReturnReport(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $data = DB::table("sale_return_mst as a")
            ->select(
                "a.id",
                "c.company as name",
                "a.id as invoice",
                "a.return_date as invoice_date",
                "c.name as product",
                "f.invoice_id as invoice_id",
                "d.name as product",
                "d.part_no as part_no",
                DB::raw("sum(b.qty) as qty")
            )
            ->join("sale_return_det as b", "a.id", "b.mst_id")
            ->join("customers as c", "a.customer_id", "c.id")
            ->join("products as d", "b.product_id", "d.id")
            ->join("stock_outward_mst as f", "a.outward_id", "f.id")
            ->whereDate("a.return_date", ">=", $fromDate)
            ->whereDate("a.return_date", "<=", $toDate)
            ->where("a.company_id", $request->user->active_inventory)
            ->groupBy("a.id", "c.name", "c.company", "a.id", "a.return_date", "d.name", "f.invoice_id", "d.name", "d.part_no")
            ->get();
        return view("sale-return-report", compact("data"));
    }


    public function productLedger(Request $request)
    {
        $products = products::get();

        $product_id = request("product_id");
        $purchase = DB::table("stock_inward_mst as a")
            ->select(
                "c.name as party",
                "a.invoice_no as invoice_id",
                "a.invoice_date as invoice_date",
                "d.name as location",
                "e.name as product",
                "e.part_no as part_code",
                "b.qty as qty",
                "b.id as product_id",
                "a.created_at as created_at",
                DB::raw("'purchase' as type")
            )
            ->join("stock_inward_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("store as d", "a.location_id", "d.id")
            ->join("products as e", "b.product_id", "e.id")
            ->where("b.product_id", $product_id)
            ->where("a.company_id", $request->user->active_inventory)
            ->get();

        $purchaseReturn =   DB::table('purchase_return_mst as a')
            ->select(
                "c.name as party",
                "a.id as invoice_id",
                "a.created_at as invoice_date",
                "e.name as location",
                "f.name as product",
                "f.part_no as part_code",
                "b.qty as qty",
                "b.id as product_id",
                "a.created_at as created_at",
                DB::raw("'purchase return' as type")
            )
            ->join("purchase_return_det as b", "a.id", "b.mst_id")
            ->join("vendor as c", "a.vendor_id", "c.id")
            ->join("stock_inward_mst as d", "a.inward_id", "d.id")
            ->join("store as e", "d.location_id", "e.id")
            ->join("products as f", "b.product_id", "f.id")
            ->where("a.company_id", $request->user->active_inventory)
            ->where("b.product_id", $product_id)->get();


        $sale = DB::table("stock_outward_mst as a")
            ->select(
                "c.company as party",
                "a.invoice_id as invoice_id",
                "a.invoice_date as invoice_date",
                "d.name as location",
                "e.name as product",
                "e.part_no as part_code",
                "b.qty as qty",
                "b.id as product_id",
                "a.created_at as created_at",
                DB::raw("'sale' as type")
            )
            ->join("stock_outward_det as b", "a.id", "b.mst_id")
            ->join("customers as c", "a.customer_id", "c.id")
            ->join("store as d", "a.location_id", "d.id")
            ->join("products as e", "b.product_id", "e.id")
            ->where("b.product_id", $product_id)
            ->where("a.is_invoice", 1)
            ->where("a.store_id", $request->user->active_inventory)
            ->get();


        $saleReturn = DB::table("sale_return_mst as a")
            ->select(
                "c.company as party",
                "a.id as invoice_id",
                "a.created_at as invoice_date",
                "f.name as location",
                "d.name as product",
                "d.part_no as part_code",
                "b.qty as qty",
                "b.id as product_id",
                "a.created_at as created_at",
                DB::raw("'sale return' as type")
            )
            ->join("sale_return_det as b", "a.id", "b.mst_id")
            ->join("customers as c", "a.customer_id", "c.id")
            ->join("products as d", "b.product_id", "d.id")
            ->join("stock_outward_mst as e", "a.outward_id", "e.id")
            ->join("store as f", "e.store_id", "f.id")
            ->where("a.company_id", $request->user->active_inventory)
            ->where("b.product_id", $product_id)->get();

        $ledger = collect()
            ->merge($purchase)
            ->merge($purchaseReturn)
            ->merge($sale)
            ->merge($saleReturn);
        $ledger = $ledger->sortBy('created_at')->values();

        $balance = 0;

        $ledger = $ledger->map(function ($row) use (&$balance) {

            if (in_array($row->type, ['purchase', 'sale return'])) {
                $balance += $row->qty;
            } else if (in_array($row->type, ['purchase return', 'sale'])) {
                $balance -= $row->qty;
            }

            $row->balance_qty = $balance;

            return $row;
        });



        // echo "<pre>";
        // print_r($ledger);
        // die;

        return view("product-ledger", compact("products", "ledger"));
    }
    public function orderVsInvoice(Request $request)
    {
        $fromDate = request("fromDate", date("Y-m-d"));
        $toDate = request("toDate", date("Y-m-d"));
        $customer_id = request("customer_id");
        $user_id = request("user_id");
        $search = request("search");



        $orderTotal = DB::table("order_det as od")
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


        /* -----------------------------------------
            2️⃣ MAIN QUERY (BASE = ORDER)
            ------------------------------------------*/

        $dt = DB::table("order_mst as d")

            ->select(
                "x.company",
                "d.order_id",
                "d.status",
                DB::raw("DATE(d.created_at) as order_date"),

                /* Order Total */
                "ot.totalOrderValue as orderValue",

                /* PT Details */
                "b.invoice_date",
                "b.outward_id",

                DB::raw("ROUND(SUM(a.price * a.qty),2) as pt_amount"),

                /* Invoice Details */
                "b.invoice_convert_date",
                "b.invoice_id",
                "b.is_invoice",


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
            ,2) as invoice_amount
        ")
            )

            /* ORDER TOTAL JOIN */
            ->leftJoinSub($orderTotal, 'ot', function ($join) {
                $join->on('ot.mst_id', '=', 'd.id');
            })

            /* PT JOIN */
            ->leftJoin("stock_outward_mst as b", "b.order_id", "d.id")
            ->leftJoin("stock_outward_det as a", "a.mst_id", "b.id")

            /* Order Det for discount/gst reference */
            ->leftJoin("order_det as f", function ($join) {
                $join->on('f.product_id', '=', 'a.product_id')
                    ->on('f.mst_id', '=', 'd.id');
            })

            ->leftJoin("customers as x", "d.customer_id", "x.id")

            ->where("d.company_id", $request->user->active_inventory);


        if ($customer_id) {
            $dt->where("x.id", $customer_id);
        }

        if ($fromDate) {
            $dt->whereDate("d.created_at", ">=", $fromDate);
        }

        if ($toDate) {
            $dt->whereDate("d.created_at", "<=", $toDate);
        }


        $data = $dt->groupBy(
            "x.company",
            "d.order_id",
            "d.created_at",
            "ot.totalOrderValue",
            "b.invoice_date",
            "b.outward_id",
            "d.status",
            "b.invoice_convert_date",
            "b.invoice_id",
            "b.is_invoice"
        )->get();



        // echo "<pre>";
        // print_r($data);
        // die;

        return view("order-vs-invoice", compact("data"));
    }

    public function inOutReport(Request $request)
    {
        $data = DB::table(
            DB::raw('(SELECT product_id, SUM(qty) as in_qty 
                  FROM stock_inward_det 
                  GROUP BY product_id) as i')
        )
            ->select(
                'c.name',
                'c.part_no',
                DB::raw('i.in_qty'),
                DB::raw('COALESCE(o.out_qty, 0) as out_qty'),
                DB::raw('(i.in_qty - COALESCE(o.out_qty, 0)) as current_stock')
            )
            ->leftJoin(
                DB::raw('(SELECT product_id, SUM(qty) as out_qty 
                  FROM stock_outward_det 
                  GROUP BY product_id) as o'),
                'i.product_id',
                '=',
                'o.product_id'
            )
            ->join('products as c', 'i.product_id', '=', 'c.id')
            ->get();

        return view("in-out-report", compact("data"));
    }
}
