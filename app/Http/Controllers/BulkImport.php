<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use League\Csv\Reader;

class BulkImport extends Controller
{

    function generateRandomNumber($length = 12)
    {
        $number = '';
        while (strlen($number) < $length) {
            $number .= mt_rand(0, 9);
        }
        return substr($number, 0, $length);
    }

    public function ImportProducts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {
                $brand_id = "";
                $category_id = "";
                $sub_category_id = "";
                $unit_type_id = "";
                try {

                    if ($record[0] == "brand") {
                        $count++;
                        continue;
                    }


                    if (!$record[0]) {
                        $count++;
                        continue;
                    }
                    if (!$record[1]) {
                        $count++;
                        continue;
                    }
                    if (!$record[2]) {
                        $count++;
                        continue;
                    }

                    $brand = DB::table("brand")->where("name", $record[0])->first();
                    if ($brand) {
                        $brand_id = $brand->id;
                    } else {
                        $brand_id =  DB::table('brand')->insertGetId(array(
                            "name" => $record[0],

                        ));
                    }

                    $category = DB::table("category")->where("name", $record[1])->first();
                    if ($category) {
                        $category_id = $category->id;
                    } else {
                        $category_id =  DB::table('category')->insertGetId(array(
                            "name" => $record[1],
                            "brand_id" => $brand_id,

                        ));
                    }
                    $sub_category = DB::table("sub_category")->where("name", $record[2])->first();
                    if ($sub_category) {
                        $sub_category_id = $sub_category->id;
                    } else {
                        $sub_category_id =  DB::table('sub_category')->insertGetId(array(
                            "name" => $record[2],
                            "category_id" => $category_id,

                        ));
                    }

                    $unit_type = DB::table("unit_type")->where("name", $record[10])->first();
                    if ($unit_type) {
                        $unit_type_id = $unit_type->id;
                    } else {
                        $unit_type_id =  DB::table('unit_type')->insertGetId(array(
                            "name" => $record[10],
                        ));
                    }



                    $products = DB::table("products")->where("part_no", $record[4])->first();
                    if ($products) {

                        $name = mb_convert_encoding(
                            $record[3],
                            'UTF-8',
                            ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252']
                        );

                        $description = mb_convert_encoding(
                            $record[12],
                            'UTF-8',
                            ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252']
                        );

                        DB::table('products')->where("id", $products->id)->update(array(
                            "brand_id" => $brand_id,
                            "category_id" => $category_id,
                            "sub_category_id" => $sub_category_id,
                            "name" => $name,
                            "part_no" => $record[4],
                            "hsn_code" => $record[5],
                            "price" => $record[6],
                            "sale_price" => $record[7],
                            "purchase_price" => $record[8],
                            "min_stock" => $record[9],
                            "uom" => $unit_type_id,
                            "warranty_days" => $record[11],
                            "description" => $description,
                            "company_id" => $request->user->active_inventory,
                            "product_location" => $record[13],
                        ));
                        $success++;
                    } else {
                        $barcode = $this->generateRandomNumber(10);
                        $name = mb_convert_encoding(
                            $record[3],
                            'UTF-8',
                            ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252']
                        );

                        $description = mb_convert_encoding(
                            $record[12],
                            'UTF-8',
                            ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252']
                        );

                        $product =  DB::table('products')->insertGetId(array(
                            "brand_id" => $brand_id,
                            "category_id" => $category_id,
                            "sub_category_id" => $sub_category_id,
                            "name" => $name,
                            "part_no" => $record[4],
                            "hsn_code" => $record[5],
                            "price" => $record[6],
                            "sale_price" => $record[7],
                            "purchase_price" => $record[8],
                            "min_stock" => $record[9],
                            "uom" => $unit_type_id,
                            "warranty_days" => $record[11],
                            "description" => $description,
                            "company_id" => $request->user->active_inventory,
                            "bar_code" => $barcode,
                            "product_location" => $record[13],


                        ));
                        $success++;
                    }
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count .  $th->getMessage() . "<br>";
                    $error_count++;
                }
                $count++;
            }

            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }

    public function importSpecialOffer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {
                $brand_id = "";

                try {

                    if ($record[0] == "brand") {
                        $count++;
                        continue;
                    }



                    $brand = DB::table("brand")->where("name", $record[0])->first();
                    if ($brand) {
                        $brand_id = $brand->id;
                    } else {
                        $error .= "Raw ID " . $count . "Brand Not found <br>";
                        $duplicate++;
                    }



                    $products = DB::table("products")->where("part_no", trim($record[1]))->where("brand_id", $brand_id)->first();
                    if (!$products) {
                        $error .= "Raw ID " . $count . "  Part no not found. <br>";
                        $duplicate++;
                    } else {

                        $exits = DB::table("special_offer")->where("product_id", $products->id)->first();

                        if ($exits) {
                            $error .= "Raw ID " . $count . " Already added <br>";
                            $error_count++;
                        }


                        DB::table('special_offer')->insert(array(
                            "product_id" => $products->id,
                            "discount" => $record[2],
                            "expire_date" => $record[3],
                        ));
                        $success++;
                    }
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count .  $th->getMessage() . "<br>";
                    $error_count++;
                }
                $count++;
            }

            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }


    public function ImportCustomers(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);


        if ($validator->fails()) {
            $messages = $validator->errors();
            $count = 0;
            foreach ($messages->all() as $error) {
                if ($count == 0)
                    return redirect()->back()->with("error", $error);

                $count++;
            }
        }

        $count_d = 0;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('csv', 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            // $csv->setHeaderOffset(0); // Assuming the first row contains headers
            $brand = "";
            $duplicate = 0;
            $error = "";
            $error_count = 0;
            $success = 0;
            $count = 1;
            foreach ($csv as $record) {
                $brand_id = "";

                try {

                    if ($record[0] == "Party Type") {
                        $count++;
                        continue;
                    }



                    $customer_type = DB::table("customer_type")->where("name", $record[0])->first();
                    if ($customer_type) {
                        $customer_type_id = $customer_type->id;
                    } else {
                        $error .= "Raw ID " . $count . " Party Type Not Found <br>";
                        $error++;
                        $count++;
                        continue;
                    }


                    $users = DB::table("users")->where("user_name", $record[19])->first();
                    if ($users) {
                        $user_id = $users->id;
                    } else {
                        $error .= "Raw ID " . $count . " Manager Username not found <br>";
                        $error++;
                        $count++;
                        continue;
                    }



                    $customers = DB::table("customers")->where("party_code", trim($record[16]))->first();
                    if ($customers) {
                        DB::table('customers')->where("id", $customers->id)->update(array(
                            "customer_type_id" => $customer_type_id,
                            "customer_type_id" => $customer_type_id,
                            "company" => $record[1],
                            "name" => $record[2],
                            "number" => $record[3],
                            "email" => $record[4],
                            "gst" => $record[5],
                            "address" => $record[6],
                            "state" => $record[7],
                            "city" => $record[8],
                            "city1" => $record[9],
                            "pincode" => $record[10],
                            "ship_address" => $record[11],
                            "ship_state" => $record[12],
                            "ship_district" => $record[13],
                            "ship_city" => $record[14],
                            "ship_pincode" => $record[15],
                            "party_code" => $record[16],
                            "discount" => $record[17],
                            "dsr" => $record[18],
                            "manager_id" => $user_id,
                            "company_id" => $request->user->active_inventory,
                        ));
                    } else {

                        DB::table('customers')->insert(array(
                            "customer_type_id" => $customer_type_id,
                            "customer_type_id" => $customer_type_id,
                            "company" => $record[1],
                            "name" => $record[2],
                            "number" => $record[3],
                            "email" => $record[4],
                            "gst" => $record[5],
                            "address" => $record[6],
                            "state" => $record[7],
                            "city" => $record[8],
                            "city1" => $record[9],
                            "pincode" => $record[10],
                            "ship_address" => $record[11],
                            "ship_state" => $record[12],
                            "ship_district" => $record[13],
                            "ship_city" => $record[14],
                            "ship_pincode" => $record[15],
                            "party_code" => $record[16],
                            "discount" => $record[17],
                            "dsr" => $record[18],
                            "manager_id" => $user_id,
                            "company_id" => $request->user->active_inventory,
                        ));
                
                    }
                            $success++;
                } catch (\Throwable $th) {
                    $error .= "Raw ID " . $count .  $th->getMessage() . "<br>";
                    $error_count++;
                }
                $count++;
            }

            return redirect()->back()->with("success", "Save successfully - Total : " . $count - 1 . " Success : " . $success . "  Duplicate : " . $duplicate . " Error : " . $error_count)->with("msg", $error);
        }

        return redirect()->back()->with("error", "No csv file selected for upload");
    }
}
