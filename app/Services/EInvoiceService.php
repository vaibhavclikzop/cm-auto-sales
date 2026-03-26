<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\DB;

class EInvoiceService
{
    public function generate($invoiceId)
    {

        try {
            $response = Http::post("https://prod-api.mastersindia.co/api/v1/token-auth/", [
                "username" => "mahesh@cmindia.net",
                "password" => "Cmindia@321#"
            ]);
            if (!$response->successful()) {

                return response()->json([
                    'status' => true,
                    'message' => 'API returned error',
                    'error' => $response->json() ?? $response->body()
                ], 500);
            }

            $data = $response->json();

            if (!isset($data['token'])) {

                return response()->json([
                    'status' => true,
                    'message' => 'Invalid API response',
                    'error' => $data
                ], 500);
            }
            $token = $data['token'];
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => $th->getMessage(),
                'error' => $data
            ], 500);
        }


        return  $this->geneRateEInvoice($invoiceId, $token);
    }

    private function geneRateEInvoice($invoiceId, $token)
    {


        try {

            $orderDetSub = DB::table('order_det')
                ->select(
                    'product_id',
                    'mst_id',
                    DB::raw('MAX(discount) as discount'),
                    DB::raw('MAX(gst) as gst')
                )
                ->groupBy('product_id', 'mst_id');

            $invoiceMst =  DB::table('stock_outward_mst as a')
                ->select("a.invoice_id",    DB::raw("COALESCE(a.invoice_convert_date, a.invoice_date) as invoice_date"), "b.company", "b.gst", "b.address", "b.state", "b.city", "b.pincode", "b.city1", "a.discount_type")
                ->join("customers as b", "a.customer_id", "b.id")
                ->where("a.id", $invoiceId)->first();
            $invoiceDet =  DB::table("stock_outward_det as a")
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
                ->where("a.mst_id", $invoiceId)
                ->get();

            $itemList = [];
            $totalAssessableValue = 0;
            $totalIgstValue = 0;
            $totalInvoiceValue = 0;
            $serial = 1;
            $specialDiscount = 0;
            $specialDiscountAmount = 0;
            $igstAmount = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;
            $totalCgstValue = 0;
            $totalSgstValue = 0;

            foreach ($invoiceDet as $row) {

                $qty = (float) $row->qty;
                $price = (float) $row->price;
                $discountPercent = (float) $row->discount;
                $gstRate = (float) $row->gst;
                $specialDiscount = (float) $row->special_discount;


                $totalAmount = round($price, 2);
                if ($invoiceMst->discount_type == "discount") {



                    $discountAmount = round(($totalAmount * $discountPercent) / 100, 2);

                    $specialDiscountAmount =  round((($totalAmount - $discountAmount) * $specialDiscount) / 100, 2);

                    $assessableValue = round($totalAmount - $discountAmount - $specialDiscountAmount, 2);
                } else {
       
                    $assessableValue = round(($totalAmount-($totalAmount/100) * ($discountPercent+$specialDiscount)), 2);
                }

 

                if ("03" == substr($invoiceMst->gst, 0, 2)) {
                    $cgstAmount = round((($assessableValue * $gstRate) / 100) / 2, 2);
                    $sgstAmount = round((($assessableValue * $gstRate) / 100) / 2, 2);
                } else {
                    $igstAmount = round(($assessableValue * $gstRate) / 100, 2);
                }


                $totalItemValue = round($assessableValue + $igstAmount + $cgstAmount + $sgstAmount, 2);


                $totalAssessableValue += $assessableValue * $row->qty;
                $totalIgstValue += $igstAmount * $row->qty;
                $totalCgstValue += $cgstAmount * $row->qty;
                $totalSgstValue += $sgstAmount * $row->qty;
                $totalInvoiceValue += $totalItemValue * $row->qty;

                $itemList[] = [
                    "item_serial_number" => (string) $serial++,
                    "product_description" => $row->product,
                    "is_service" => "N",
                    "hsn_code" => $row->hsn_code,
                    "quantity" => $qty,
                    "unit" => "PCS",
                    "unit_price" => round($assessableValue, 2),
                    "total_amount" => round($assessableValue * $row->qty, 2),
                    "discount" => 0,
                    "other_charge" => 0,
                    "assessable_value" => round($assessableValue * $row->qty, 2),
                    "gst_rate" => $gstRate,
                    "igst_amount" => round($igstAmount * $row->qty, 2),
                    "cgst_amount" => round($cgstAmount * $row->qty, 2),
                    "sgst_amount" => round($sgstAmount * $row->qty, 2),
                    "cess_rate" => 0,
                    "cess_amount" => 0,
                    "cess_nonadvol_amount" => 0,
                    "state_cess_rate" => 0,
                    "state_cess_amount" => 0,
                    "state_cess_nonadvol_amount" => 0,
                    "total_item_value" => round($totalItemValue * $row->qty, 2)
                ];
            }
            
            $totalAssessableValue = round($totalAssessableValue, 2);
            $totalIgstValue = round($totalIgstValue, 2);
            $totalCgstValue = round($totalCgstValue, 2);
            $totalSgstValue = round($totalSgstValue, 2);
            $totalInvoiceValue = round($totalInvoiceValue, 2);


            $payload = [
                "user_gstin" => "03AACCC6127F1Z6",
                "data_source" => "erp",

                "transaction_details" => [
                    "supply_type" => "B2B",
                    "charge_type" => "N"
                ],

                "document_details" => [
                    "document_type" => "INV",
                    "document_number" => $invoiceMst->invoice_id,
                    "document_date" => date("d/m/Y", strtotime($invoiceMst->invoice_date))
                ],

                "seller_details" => [
                    "gstin" => "03AACCC6127F1Z6",
                    "legal_name" => "C M AUTOMOBILES PRIVATE LIMITED",
                    "address1" => "PLOT NO B-64, INDUSTRIAL AREA PHASE 7, MOHALI, SAS Nagar, Punjab, 160055",
                    "location" => "MOHALI",
                    "pincode" => 160055,
                    "state_code" => "03"
                ],

                "buyer_details" => [
                    "gstin" => $invoiceMst->gst,
                    "legal_name" => $invoiceMst->company,
                    "address1" => $invoiceMst->address,
                    "location" => $invoiceMst->city,
                    "pincode" => $invoiceMst->pincode,
                    "state_code" => substr($invoiceMst->gst, 0, 2),
                    "place_of_supply" => substr($invoiceMst->gst, 0, 2)
                ],

                "item_list" =>  $itemList,

                "value_details" => [
                    "total_assessable_value" => $totalAssessableValue,
                    "total_igst_value" => $totalIgstValue,
                    "total_cgst_value" => $totalCgstValue,
                    "total_sgst_value" => $totalSgstValue,
                    "total_cess_value" => 0,
                    "total_cess_value_of_state" => 0,
                    "total_discount" => 0,
                    "total_other_charge" => 0,
                    "round_off_amount" => 0,
                    "total_invoice_value" => $totalInvoiceValue
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'JWT ' . $token,
                'Content-Type' => 'application/json'
            ])
                ->post("https://prod-api.mastersindia.co/api/v1/einvoice/", $payload);


            if (!$response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'API Error',
                    'error' => $response->json() ?? $response->body()
                ], 500);
            }

            $data = $response->json();

            if (isset($data['message'])) {

                return response()->json([
                    'status' => true,
                    'message' => $data['message'],
                    'error' => $data
                ], 500);
            }

            if (isset($data['results']['status']) && $data['results']['status'] === "Failed") {

                return response()->json([
                    'status' => true,
                    'message' => $data["results"]["errorMessage"],
                    'error' => $data
                ], 500);
            }


            if (isset($data['results']['status']) && $data['results']['status'] === "Success") {

                DB::table("stock_outward_mst")->where("id", $invoiceId)->update(array(
                    "is_e_invoice" => 1,
                    "AckNo" => $data["results"]["message"]["AckNo"],
                    "AckDt" => $data["results"]["message"]["AckDt"],
                    "Irn" => $data["results"]["message"]["Irn"],
                    "SignedInvoice" => $data["results"]["message"]["SignedInvoice"],
                    "SignedQRCode" => $data["results"]["message"]["SignedQRCode"],
                    "QRCodeUrl" => $data["results"]["message"]["QRCodeUrl"],
                    "EinvoicePdf" => $data["results"]["message"]["EinvoicePdf"],
                    "e_invoice_response" => $data,

                ));

                return response()->json([
                    'status' => false,
                    'message' => 'Success',
                    'error' => $data
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => $th->getMessage(),
                'error' => ""
            ], 500);
        }
    }
}
