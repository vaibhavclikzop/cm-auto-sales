<?php

namespace App\Http\Controllers\Tally;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TallyReportController extends Controller
{
    public function saleReport(Request $request)
    {
        $fromDate = request("fromDt", date("Y-m-d"));
        $toDate = request("toDt", date("Y-m-d"));
        $data = DB::table('stock_outward_mst as a')
            ->select(
                "a.id as voucher_no",
                "a.invoice_convert_date",
                "e.company as customer",
                "e.party_code",
                "a.invoice_id",
                "a.created_at as date",
                "e.gst as gst_no",
                DB::raw("'Sale Report' as particular"),
                DB::raw("'Sales' as voucher_type"),


                DB::raw("ROUND(SUM(b.price * b.qty), 2) as total"),


                DB::raw("
            ROUND(SUM(
                CASE 
                    WHEN a.discount_type = 'price'
                        THEN (b.price * b.qty) * ((d.discount + b.discount) / 100)
                    ELSE (b.price * b.qty) * d.discount / 100
                END
            ), 2) as discount
        "),


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


                DB::raw("
            CASE
                WHEN e.state = f.state THEN 'CGST'
                ELSE 'IGST'
            END AS gst_type
        "),


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


            ->join(DB::raw("
        (SELECT 
            product_id, 
            mst_id, 
            MAX(discount) as discount, 
            MAX(gst) as gst
         FROM order_det
         WHERE is_delete = 0  
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
        $data = $data->groupBy(
            "a.id",
            "a.invoice_convert_date",
            "e.company",
            "e.party_code",
            "a.invoice_id",
            "a.created_at",
            "e.gst",
            "e.state",
            "f.state"
        )->orderBy("a.invoice_id")->get();
        // echo "<pre>";
        // print_r($data);
        // die;
        return view("tally-report.sale-report", compact("data"));
    }

public function purchaseReport(Request $request)
{
    $fromDate = request("fromDt", date("Y-m-d"));
    $toDate = request("toDt", date("Y-m-d"));

    $data = DB::table("stock_inward_mst as a")
        ->select(
            "a.id",
            "a.invoice_date as voucher_date",

            DB::raw("'Purchase' as voucher_type"),

            "a.invoice_no as voucher_no",
            "a.invoice_no as supplier_invoice_no",
            "a.invoice_date as supplier_invoice_date",

            "v.name as party_ledger_name",
            "v.gst as party_gstin",

            // Taxable Amount (After Discount)
            DB::raw("
                ROUND(
                    SUM(
                        (
                            (
                                (b.qty * b.price)
                                * (100 - IFNULL(b.discount,0))
                                / 100
                            )
                            /
                            (1 + (b.gst / 100))
                        )
                    ),
                2) as taxable_amount
            "),

            // IGST Rate
            DB::raw("
                CASE
                    WHEN LEFT(v.gst,2) != LEFT(c.gst_no,2)
                    THEN MAX(b.gst)
                    ELSE 0
                END as igst_rate
            "),

            // IGST Amount (After Discount)
            DB::raw("
                ROUND(
                    SUM(
                        CASE
                            WHEN LEFT(v.gst,2) != LEFT(c.gst_no,2)
                            THEN
                            (
                                (
                                    (b.qty * b.price)
                                    * (100 - IFNULL(b.discount,0))
                                    / 100
                                )
                                -
                                (
                                    (
                                        (b.qty * b.price)
                                        * (100 - IFNULL(b.discount,0))
                                        / 100
                                    )
                                    /
                                    (1 + (b.gst / 100))
                                )
                            )
                            ELSE 0
                        END
                    ),
                2) as output_igst_amount
            "),

            // SGST Rate
            DB::raw("
                CASE
                    WHEN LEFT(v.gst,2) = LEFT(c.gst_no,2)
                    THEN MAX(b.gst)/2
                    ELSE 0
                END as sgst_rate
            "),

            // SGST Amount (After Discount)
            DB::raw("
                ROUND(
                    SUM(
                        CASE
                            WHEN LEFT(v.gst,2) = LEFT(c.gst_no,2)
                            THEN
                            (
                                (
                                    (
                                        (
                                            (b.qty * b.price)
                                            * (100 - IFNULL(b.discount,0))
                                            / 100
                                        )
                                        -
                                        (
                                            (
                                                (b.qty * b.price)
                                                * (100 - IFNULL(b.discount,0))
                                                / 100
                                            )
                                            /
                                            (1 + (b.gst / 100))
                                        )
                                    )
                                ) / 2
                            )
                            ELSE 0
                        END
                    ),
                2) as output_sgst_amount
            "),

            // CGST Rate
            DB::raw("
                CASE
                    WHEN LEFT(v.gst,2) = LEFT(c.gst_no,2)
                    THEN MAX(b.gst)/2
                    ELSE 0
                END as cgst_rate
            "),

            // CGST Amount (After Discount)
            DB::raw("
                ROUND(
                    SUM(
                        CASE
                            WHEN LEFT(v.gst,2) = LEFT(c.gst_no,2)
                            THEN
                            (
                                (
                                    (
                                        (
                                            (b.qty * b.price)
                                            * (100 - IFNULL(b.discount,0))
                                            / 100
                                        )
                                        -
                                        (
                                            (
                                                (b.qty * b.price)
                                                * (100 - IFNULL(b.discount,0))
                                                / 100
                                            )
                                            /
                                            (1 + (b.gst / 100))
                                        )
                                    )
                                ) / 2
                            )
                            ELSE 0
                        END
                    ),
                2) as output_cgst_amount
            "),

            // Total Amount After Discount (Inclusive GST)
            DB::raw("
                ROUND(
                    SUM(
                        (
                            (b.qty * b.price)
                            * (100 - IFNULL(b.discount,0))
                            / 100
                        )
                    ),
                2) as total_amount
            "),

            DB::raw("'' as narration")
        )

        ->join("stock_inward_det as b", "a.id", "b.mst_id")
        ->join("vendor as v", "a.vendor_id", "v.id")
        ->join("company as c", "a.company_id", "c.id")

        ->whereDate("a.invoice_date", ">=", $fromDate)
        ->whereDate("a.invoice_date", "<=", $toDate)

        ->where("a.company_id", $request->user->active_inventory)

        ->groupBy(
            "a.id",
            "a.invoice_date",
            "a.invoice_no",
            "v.name",
            "v.gst",
            "c.gst_no"
        )

        ->orderBy("a.invoice_date", "asc")

        ->get();

    return view("tally-report.purchase-report", compact("data"));
}

    public function saleReturnReport(Request $request)
    {

        $fromDate = request("fromDt", date("Y-m-d"));
        $toDate = request("toDt", date("Y-m-d"));


        $data = DB::table("sale_return_mst as a")
            ->select(

                "a.id",

                "a.return_date as voucher_date",

                DB::raw("'Sales Return' as voucher_type"),

                "a.id as voucher_no",

                "so.invoice_id as original_invoice_no",

                "so.invoice_date as original_invoice_date",

                "c.company as party_ledger_name",

                "c.gst as party_gstin",

                // =========================
                // TAXABLE AMOUNT
                // =========================
                DB::raw("
            SUM(
                ROUND(
                    (
                        CASE
                            WHEN so.discount_type = 'discount'
                            THEN
                                (
                                    (
                                        sod.price
                                        -
                                        ((sod.price / 100) * od.discount)
                                    )
                                    -
                                    (
                                        (
                                            sod.price
                                            -
                                            ((sod.price / 100) * od.discount)
                                        ) / 100
                                    ) * sod.discount
                                )

                            ELSE

                                (
                                    sod.price
                                    -
                                    (
                                        (sod.price / 100)
                                        *
                                        (od.discount + sod.discount)
                                    )
                                )
                        END
                    ) * sr.qty
                ,2)
            ) as taxable_amount
        "),

                // =========================
                // IGST RATE
                // =========================
                DB::raw("
            CASE
                WHEN LEFT(c.gst,2) != LEFT(co.gst_no,2)
                THEN MAX(od.gst)
                ELSE 0
            END as igst_rate
        "),

                // =========================
                // OUTPUT IGST AMOUNT
                // =========================
                DB::raw("
            SUM(
                ROUND(
                    CASE
                        WHEN LEFT(c.gst,2) != LEFT(co.gst_no,2)
                        THEN
                            (
                                (
                                    CASE
                                        WHEN so.discount_type = 'discount'
                                        THEN
                                            (
                                                (
                                                    sod.price
                                                    -
                                                    ((sod.price / 100) * od.discount)
                                                )
                                                -
                                                (
                                                    (
                                                        sod.price
                                                        -
                                                        ((sod.price / 100) * od.discount)
                                                    ) / 100
                                                ) * sod.discount
                                            )

                                        ELSE

                                            (
                                                sod.price
                                                -
                                                (
                                                    (sod.price / 100)
                                                    *
                                                    (od.discount + sod.discount)
                                                )
                                            )
                                    END
                                ) * sr.qty
                            ) * od.gst / 100
                        ELSE 0
                    END
                ,2)
            ) as output_igst_amount
        "),

                // =========================
                // SGST RATE
                // =========================
                DB::raw("
            CASE
                WHEN LEFT(c.gst,2) = LEFT(co.gst_no,2)
                THEN MAX(od.gst)/2
                ELSE 0
            END as sgst_rate
        "),

                // =========================
                // OUTPUT SGST AMOUNT
                // =========================
                DB::raw("
            SUM(
                ROUND(
                    CASE
                        WHEN LEFT(c.gst,2) = LEFT(co.gst_no,2)
                        THEN
                            (
                                (
                                    (
                                        CASE
                                            WHEN so.discount_type = 'discount'
                                            THEN
                                                (
                                                    (
                                                        sod.price
                                                        -
                                                        ((sod.price / 100) * od.discount)
                                                    )
                                                    -
                                                    (
                                                        (
                                                            sod.price
                                                            -
                                                            ((sod.price / 100) * od.discount)
                                                        ) / 100
                                                    ) * sod.discount
                                                )

                                            ELSE

                                                (
                                                    sod.price
                                                    -
                                                    (
                                                        (sod.price / 100)
                                                        *
                                                        (od.discount + sod.discount)
                                                    )
                                                )
                                        END
                                    ) * sr.qty
                                ) * od.gst / 100
                            ) / 2
                        ELSE 0
                    END
                ,2)
            ) as output_sgst_amount
        "),

                // =========================
                // CGST RATE
                // =========================
                DB::raw("
            CASE
                WHEN LEFT(c.gst,2) = LEFT(co.gst_no,2)
                THEN MAX(od.gst)/2
                ELSE 0
            END as cgst_rate
        "),

                // =========================
                // OUTPUT CGST AMOUNT
                // =========================
                DB::raw("
            SUM(
                ROUND(
                    CASE
                        WHEN LEFT(c.gst,2) = LEFT(co.gst_no,2)
                        THEN
                            (
                                (
                                    (
                                        CASE
                                            WHEN so.discount_type = 'discount'
                                            THEN
                                                (
                                                    (
                                                        sod.price
                                                        -
                                                        ((sod.price / 100) * od.discount)
                                                    )
                                                    -
                                                    (
                                                        (
                                                            sod.price
                                                            -
                                                            ((sod.price / 100) * od.discount)
                                                        ) / 100
                                                    ) * sod.discount
                                                )

                                            ELSE

                                                (
                                                    sod.price
                                                    -
                                                    (
                                                        (sod.price / 100)
                                                        *
                                                        (od.discount + sod.discount)
                                                    )
                                                )
                                        END
                                    ) * sr.qty
                                ) * od.gst / 100
                            ) / 2
                        ELSE 0
                    END
                ,2)
            ) as output_cgst_amount
        "),

                // =========================
                // TOTAL AMOUNT
                // =========================
                DB::raw("
            SUM(
                ROUND(
                    (
                        (
                            CASE
                                WHEN so.discount_type = 'discount'
                                THEN
                                    (
                                        (
                                            sod.price
                                            -
                                            ((sod.price / 100) * od.discount)
                                        )
                                        -
                                        (
                                            (
                                                sod.price
                                                -
                                                ((sod.price / 100) * od.discount)
                                            ) / 100
                                        ) * sod.discount
                                    )

                                ELSE

                                    (
                                        sod.price
                                        -
                                        (
                                            (sod.price / 100)
                                            *
                                            (od.discount + sod.discount)
                                        )
                                    )
                            END
                        ) * sr.qty
                    )
                    +
                    (
                        (
                            (
                                CASE
                                    WHEN so.discount_type = 'discount'
                                    THEN
                                        (
                                            (
                                                sod.price
                                                -
                                                ((sod.price / 100) * od.discount)
                                            )
                                            -
                                            (
                                                (
                                                    sod.price
                                                    -
                                                    ((sod.price / 100) * od.discount)
                                                ) / 100
                                            ) * sod.discount
                                        )

                                    ELSE

                                        (
                                            sod.price
                                            -
                                            (
                                                (sod.price / 100)
                                                *
                                                (od.discount + sod.discount)
                                            )
                                        )
                                END
                            ) * sr.qty
                        ) * od.gst / 100
                    )
                ,2)
            ) as total_amount
        "),

                DB::raw("'' as narration")
            )

            ->join("sale_return_det as sr", "a.id", "sr.mst_id")

            ->join("customers as c", "a.customer_id", "c.id")

            ->join("company as co", "a.company_id", "co.id")

            ->join("stock_outward_mst as so", "a.outward_id", "so.id")

            ->join("stock_outward_det as sod", function ($join) {
                $join->on("sod.mst_id", "=", "so.id")
                    ->on("sod.product_id", "=", "sr.product_id");
            })

            ->join("order_det as od", function ($join) {
                $join->on("od.product_id", "=", "sr.product_id")
                    ->on("od.mst_id", "=", "so.order_id");
            })

            ->whereDate("a.return_date", ">=", $fromDate)

            ->whereDate("a.return_date", "<=", $toDate)

            ->where("a.company_id", $request->user->active_inventory)

            ->groupBy(
                "a.id",
                "a.return_date",
                "a.id",
                "so.invoice_id",
                "so.invoice_date",
                "c.company",
                "c.gst",
                "co.gst_no"
            )

            ->get();

        return view("tally-report.sale-return-report", compact("data"));
    }
}
