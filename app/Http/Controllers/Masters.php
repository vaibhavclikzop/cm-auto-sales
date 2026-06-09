<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\customer_type;
use App\Models\customer_type_price;
use App\Models\customers;
use App\Models\products;
use App\Models\special_offer;
use App\Models\store;
use App\Models\warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Masters extends Controller
{


    function generateRandomNumber($length = 12)
    {
        $number = '';
        while (strlen($number) < $length) {
            $number .= mt_rand(0, 9);
        }
        return substr($number, 0, $length);
    }

    public function GetCity(Request $request)
    {
        $state_city = DB::table("state_city")->distinct("state")->where("state", $request->state)->get();;
        return $state_city;
    }


    public function GetCategory(Request $request)
    {
        $category = DB::table("category")->where("brand_id", $request->id)->get();
        return $category;
    }


    public function GetSubCategory(Request $request)
    {
        $sub_category = DB::table("sub_category")->where("category_id", $request->id)->get();;
        return $sub_category;
    }

    public function GetProducts(Request $request)
    {

        $brand_id = $request->brand_id;

        $products = DB::table('products as a')
            ->join("brand as c", "a.brand_id", "c.id")
            ->join("unit_type as d", "a.uom", "d.id")
            // ->leftJoin('customer_type_price as b', function ($join) use ($request) {
            //     $join->on('a.id', '=', 'b.product_id')
            //         ->where('b.customer_type_id', $request->customer_type);
            // })

            // ->where('a.company_id', $request->user->active_inventory)
            ->select(
                'a.*',
                "c.name as brand",
                "d.name as uom",
                "d.order_count as order_count",
                DB::raw('COALESCE(a.sale_price) as final_price')
            );

        if ($brand_id) {
            $products->where("a.brand_id", $brand_id);
        }

        $prod = $products->get();

        return $prod;
    }

    public function GetProducts1(Request $request)
    {

        $brand_id = $request->brand_id;

        return DB::table('products')->where("brand_id", $brand_id)->get();
    }

    public function Company(Request $request)
    {
        $store = DB::table("company")->get();
        return view("company", compact('store'));
    }

    public function SaveCompany(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'state' => 'required',

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

            $file = "";
            if ($request->hasFile('file')) {
                $file = time() . '.' . $request->file('file')->extension();
                $request->file('file')->move('logo', $file);
            } else {
                if (!empty($request->id)) {
                    $products = DB::table("company")->where("id", $request->id)->first();
                    $file = $products->img;
                }
            }

            if (empty($request->id)) {
                DB::table('company')->insertGetId(array(

                    "name" => $request->name,
                    "address" => $request->address,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst_no" => $request->gst_no,
                    "invoice_prefix" => $request->invoice_prefix,
                    "invoice_no" => $request->invoice_no,
                    "state" => $request->state,
                    "img" => $file,
                    "pt_prefix" => $request->pt_prefix,
                    "pt_no" => $request->pt_no,
                    "bank_name" => $request->bank_name,
                    "branch_name" => $request->branch_name,
                    "account_number" => $request->account_number,
                    "ifsc_code" => $request->ifsc_code,


                ));
            } else {
                DB::table('company')->where("id", $request->id)->update(array(

                    "name" => $request->name,
                    "address" => $request->address,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst_no" => $request->gst_no,
                    "invoice_prefix" => $request->invoice_prefix,
                    "invoice_no" => $request->invoice_no,
                    "state" => $request->state,
                    "img" => $file,
                    "pt_prefix" => $request->pt_prefix,
                    "pt_no" => $request->pt_no,
                    "bank_name" => $request->bank_name,
                    "branch_name" => $request->branch_name,
                    "account_number" => $request->account_number,
                    "ifsc_code" => $request->ifsc_code,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function Customers(Request $request)
    {
        $customer_type = customer_type::get();
        $customers = customers::with("customerType")
            ->where("company_id", $request->user->active_inventory)
            // ->whereIn("manager_id", $request->userIds)
            ->get();

        $users = DB::table("users")->get();

        return view("customers", compact('customers', "users", "customer_type"));
    }


    public function SaveCustomer(Request $request)
    {



        $validator = Validator::make($request->all(), [

            'number' => 'required',
            'name' => 'required',
            'party_code' => 'required',


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
            if (empty($request->id)) {
                DB::table('customers')->insertGetId(array(
                    "company" => $request->company,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    "company_id" => $request->user->active_inventory,
                    "customer_type_id" => $request->customer_type_id,
                    "manager_id" => $request->manager_id,
                    "dsr" => $request->dsr,
                    "party_code" => $request->party_code,
                    "coordinates" => $request->coordinates,
                    "city1" => $request->city1,

                    "ship_address" => $request->ship_address,
                    "ship_state" => $request->ship_state,
                    "ship_district" => $request->ship_district,
                    "ship_pincode" => $request->ship_pincode,
                    "ship_city" => $request->ship_city,

                ));
            } else {
                DB::table('customers')->where("id", $request->id)->update(array(
                    "company" => $request->company,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    "company_id" => $request->user->active_inventory,
                    "customer_type_id" => $request->customer_type_id,
                    "manager_id" => $request->manager_id,
                    "dsr" => $request->dsr,
                    "party_code" => $request->party_code,
                    "coordinates" => $request->coordinates,
                    "city1" => $request->city1,
                    "ship_address" => $request->ship_address,
                    "ship_state" => $request->ship_state,
                    "ship_district" => $request->ship_district,
                    "ship_pincode" => $request->ship_pincode,
                    "ship_city" => $request->ship_city,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function VendorType(Request $request)
    {

        $vendor_type = DB::table("vendor_type")->get();
        return view("vendor-type", compact('vendor_type'));
    }


    public function SaveVendorType(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

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
            if (empty($request->id)) {
                DB::table('vendor_type')->insertGetId(array(

                    "name" => $request->name,

                ));
            } else {
                DB::table('vendor_type')->where("id", $request->id)->update(array(

                    "name" => $request->name,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Vendor(Request $request)
    {

        $vendor = DB::table("vendor")
            ->where("store_id", $request->user->active_inventory)
            ->get();

        return view("vendor", compact('vendor'));
    }


    public function SaveVendor(Request $request)
    {



        $validator = Validator::make($request->all(), [

            'number' => 'required',
            'name' => 'required',

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
            if (empty($request->id)) {
                DB::table('vendor')->insertGetId(array(
                    "company" => $request->company,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    "store_id" => $request->user->active_inventory,
                ));
            } else {
                DB::table('vendor')->where("id", $request->id)->update(array(
                    "company" => $request->company,
                    "name" => $request->name,
                    "number" => $request->number,
                    "email" => $request->email,
                    "gst" => $request->gst,
                    "address" => $request->address,
                    "state" => $request->state,
                    "city" => $request->city,
                    "pincode" => $request->pincode,
                    "active" => $request->active,
                    "store_id" => $request->user->active_inventory,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }



    public function getWarehouse(Request $request)
    {

        return warehouse::where("company_id", $request->id)->get();
    }


    public function StoreLocation(Request $request)
    {
        $company = company::get();
        $store = store::with("warehouse")->get();
        return view("store-location", compact('store', "company"));
    }


    public function SaveStoreLocation(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

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

            $file = "";
            if ($request->hasFile('file')) {
                $file = time() . '.' . $request->file('file')->extension();
                $request->file('file')->move('logo', $file);
            } else {
                if (!empty($request->id)) {
                    $products = DB::table("store")->where("id", $request->id)->first();
                    $file = $products->img;
                }
            }

            if (empty($request->id)) {
                DB::table('store')->insertGetId(array(

                    "warehouse_id" => $request->warehouse_id,
                    "name" => $request->name,
                    "address" => $request->address,


                ));
            } else {
                DB::table('store')->where("id", $request->id)->update(array(
                    "warehouse_id" => $request->warehouse_id,
                    "name" => $request->name,
                    "address" => $request->address,


                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function UnitType(Request $request)
    {

        $unit_type = DB::table("unit_type")->get();
        return view("unit-type", compact('unit_type'));
    }

    public function SaveUnitType(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',
            "order_count" => 'required',

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
            if (empty($request->id)) {
                DB::table('unit_type')->insertGetId(array(

                    "name" => $request->name,
                    "order_count" => $request->order_count,


                ));
            } else {
                DB::table('unit_type')->where("id", $request->id)->update(array(

                    "name" => $request->name,
                    "order_count" => $request->order_count,


                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Brand(Request $request)
    {

        $brand = DB::table("brand")->get();
        return view("brand", compact('brand'));
    }

    public function SaveBrand(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

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
            if (empty($request->id)) {
                DB::table('brand')->insertGetId(array(
                    "name" => $request->name,
                    "max_discount" => $request->max_discount,
                ));
            } else {
                DB::table('brand')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "max_discount" => $request->max_discount,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function Category(Request $request)
    {

        $category = DB::table("category as a")
            ->select("a.*", "b.name as brand")
            ->join("brand as b", "a.brand_id", "b.id")
            ->get();
        $brand = DB::table("brand")->get();
        return view("category", compact('category', "brand"));
    }

    public function SaveCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'brand_id' => 'required',

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
            if (empty($request->id)) {
                DB::table('category')->insertGetId(array(
                    "name" => $request->name,
                    "brand_id" => $request->brand_id,
                ));
            } else {
                DB::table('category')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "brand_id" => $request->brand_id,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function SubCategory(Request $request)
    {

        $sub_category = DB::table("sub_category as a")
            ->select("a.*", "b.name as category_name", "c.id as brand_id", "c.name as brand")
            ->join("category as b", "a.category_id", "b.id")
            ->join("brand as c", "b.brand_id", "c.id")

            ->get();
        $brand = DB::table("brand")->get();
        return view("sub-category", compact('sub_category', "brand"));
    }

    public function SaveSubCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'category_id' => 'required',

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
            if (empty($request->id)) {
                DB::table('sub_category')->insertGetId(array(
                    "name" => $request->name,
                    "category_id" => $request->category_id,
                ));
            } else {
                DB::table('sub_category')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "category_id" => $request->category_id,

                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function Product(Request $request)
    {

        $query = DB::table("products as a")
            ->select(
                "a.*",
                "b.name as category_name",
                "c.name as brand_name",
                "sc.name as sub_category_name",
                "ut.name as unit_type"
            )
            ->join("category as b", "a.category_id", "b.id")
            ->join("brand as c", "a.brand_id", "c.id")
            ->join("sub_category as sc", "a.sub_category_id", "sc.id")
            ->join("unit_type as ut", "ut.id", "a.uom")
            ->where("a.company_id", $request->user->active_inventory);

        /* 🔍 Filters */
        if ($request->filled('brand')) {
            $query->where('c.id', $request->brand);
        }

        if ($request->filled('category')) {
            $query->where('b.id', $request->category);
        }

        if ($request->filled('sub_category')) {
            $query->where('sc.id', $request->sub_category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('a.name', 'like', '%' . $request->search . '%')
                    ->orWhere('a.part_no', 'like', '%' . $request->search . '%')
                    ->orWhere('a.hsn_code', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->get('per_page', 20);

        $products = $query->orderBy('a.id', 'desc')
            ->paginate($perPage)
            ->appends($request->all());

        $brand = DB::table("brand")->get();
        $unit_type = DB::table("unit_type")->get();
        $categories = DB::table("category")->get();
        $sub_categories = DB::table("sub_category")->get();
        return view("products", compact('products', "brand", "unit_type", "categories", "sub_categories"));
    }

    public function SaveProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'category_id' => 'required',

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
        $barcode = $this->generateRandomNumber(10);


        $file = "";
        if ($request->hasFile('file')) {
            $file = time() . '.' . $request->file('file')->extension();
            $request->file('file')->move('product images', $file);
        } else {
            if (!empty($request->id)) {
                $products = DB::table("products")->where("id", $request->id)->first();
                $file = $products->image;
            }
        }








        try {
            if (empty($request->id)) {
                DB::table('products')->insertGetId(array(
                    "brand_id" => $request->brand_id,
                    "category_id" => $request->category_id,
                    "sub_category_id" => $request->sub_category_id,
                    "name" => $request->name,
                    "part_no" => $request->part_no,
                    "hsn_code" => $request->hsn_code,
                    "price" => $request->price,
                    "sale_price" => $request->sale_price,
                    "purchase_price" => $request->purchase_price,
                    "min_stock" => $request->minimum_stock,
                    "uom" => $request->uom,
                    "warranty_days" => $request->warranty_days,
                    "active" => $request->active,
                    "bar_code" => $barcode,
                    "gst" => $request->gst,
                    "image" => $file,
                    "vendor_id" => 0,
                    "description" => $request->description,
                    "product_location" => $request->product_location,
                    "discount" => $request->discount,
                    "company_id" => $request->user->active_inventory

                ));
            } else {
                DB::table('products')->where("id", $request->id)->update(array(
                    "brand_id" => $request->brand_id,
                    "category_id" => $request->category_id,
                    "sub_category_id" => $request->sub_category_id,
                    "name" => $request->name,
                    "part_no" => $request->part_no,
                    "hsn_code" => $request->hsn_code,
                    "price" => $request->price,
                    "sale_price" => $request->sale_price,
                    "purchase_price" => $request->purchase_price,
                    "min_stock" => $request->minimum_stock,
                    "uom" => $request->uom,
                    "warranty_days" => $request->warranty_days,
                    "active" => $request->active,
                    "image" => $file,
                    "description" => $request->description,
                    "company_id" => $request->user->active_inventory,
                    "gst" => $request->gst,
                    "product_location" => $request->product_location,
                    "discount" => $request->discount,

                ));

                // DB::table("customer_type_price")->where("product_id", $request->id)->update(array(
                //     "price" => $request->sale_price
                // ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function VendorProduct(Request $request, $id)
    {
        $vendor_product = DB::table("products as a")
            ->select("a.*", "c.name as brand", "c.name as category")
            ->join("brand as c", "a.brand_id", "c.id")
            ->join("category as d", "a.category_id", "d.id")
            ->whereRaw("FIND_IN_SET(?, a.vendor_id)", [$id])
            ->get();

        $vendor = DB::table("vendor")->where("id", $id)->first();
        $products = DB::table('products as a')
            ->select(
                'a.*',
                'b.name as brand',
                'c.name as category',
                'd.name as sub_category'
            )
            ->join('brand as b', 'a.brand_id', '=', 'b.id')
            ->join('category as c', 'a.category_id', '=', 'c.id')
            ->join('sub_category as d', 'a.sub_category_id', '=', 'd.id')
            ->where("a.company_id", $request->user->active_inventory)
            ->where(function ($query) use ($id) {
                $query->whereRaw('NOT FIND_IN_SET(?, a.vendor_id)', [$id])
                    ->orWhereNull('a.vendor_id');
            })
            ->get();





        return view("vendor-product", compact("vendor_product", "vendor", "products"));
    }

    public function AllocateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'product_id' => 'required',

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

        foreach ($request->product_id as $key => $value) {


            DB::table('products')
                ->where('id', $value)
                ->update([
                    'vendor_id' => DB::raw("
            IF(
                vendor_id IS NULL OR vendor_id = '',
                '$request->vendor_id',
                IF(
                    FIND_IN_SET('$request->vendor_id', vendor_id) = 0,
                    CONCAT(vendor_id, ',', '$request->vendor_id'),
                    vendor_id
                )
            )
        ")
                ]);
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }



    public function Settings(Request $request)
    {
        $settings = DB::table("company_settings")->where("id", 1)->first();



        return view("settings", compact("settings"));
    }

    public function SaveSettings(Request $request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = time() . '.' . $request->image->extension();

            $request->image->move('logo', $image);
        } else {
            $company_settings = DB::table("company_settings")->where("id", 1)->first();
            $image = $company_settings->img;
        }


        DB::table('company_settings')->where("id", 1)->update(array(
            "img" => $image,
            "img_width" => $request->img_width,
            "company_name" => $request->company_name,
            "address" => $request->address,
            "contact_person" => $request->contact_person,
            "number" => $request->number,
            "email" => $request->email,
            "gst_no" => $request->gst_no,
            "terms_conditions" => $request->terms_conditions,
            "invoice_prefix" => $request->invoice_prefix,
            "invoice_no" => $request->invoice_no,
            "transaction_password" => $request->transaction_password,
        ));

        return  redirect()->back()->with("success", "Save Successfully");
    }
    public function Gst(Request $request)
    {
        $gst = DB::table("gst")->get();
        return view("gst", compact("gst"));
    }
    public function SaveGst(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'gst' => 'required',


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
        $barcode = $this->generateRandomNumber(10);

        try {
            if (empty($request->id)) {
                DB::table('gst')->insertGetId(array(
                    "gst" => $request->gst,


                ));
            } else {
                DB::table('gst')->where("id", $request->id)->update(array(
                    "gst" => $request->gst,


                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function modeOfTransport(Request $request)
    {

        $data = DB::table("mode_of_transport")->get();
        return view("mode-of-transport", compact("data"));
    }

    public function saveModeOfTransport(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'number' => 'required',
            'vehicle_no' => 'required',
            'vehicle_name' => 'required',
            'user_name' => 'required',
            'password' => 'required',


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
            if (empty($request->id)) {
                DB::table('mode_of_transport')->insertGetId(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "vehicle_no" => $request->vehicle_no,
                    "vehicle_name" => $request->vehicle_name,
                    "user_name" => $request->user_name,
                    "password" => $request->password,
                ));
            } else {
                DB::table('mode_of_transport')->where("id", $request->id)->update(array(
                    "name" => $request->name,
                    "number" => $request->number,
                    "vehicle_no" => $request->vehicle_no,
                    "vehicle_name" => $request->vehicle_name,
                    "user_name" => $request->user_name,
                    "password" => $request->password,
                ));
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function warehouse(Request $request)
    {
        $company = company::get();
        $data =  warehouse::with("company")->get();
        return view("warehouse", compact("data", "company"));
    }

    public function saveWarehouse(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',


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
            if (empty($request->id)) {
                $data = new warehouse();
                $data->company_id = $request->company_id;
                $data->name = $request->name;
                $data->address = $request->address;
                $data->save();
            } else {
                $data = warehouse::where("id", $request->id)->first();
                $data->company_id = $request->company_id;
                $data->name = $request->name;
                $data->address = $request->address;
                $data->save();
            }
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function customerType(Request $request)
    {

        $data = customer_type::get();
        return view("customer-type", compact("data"));
    }

    public function saveCustomerType(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',


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
            DB::beginTransaction();
            if (empty($request->id)) {
                $data = new customer_type();
                $data->name = $request->name;
                $data->save();

                $products = products::get();
                foreach ($products as $key => $value) {
                    $cp = new customer_type_price();
                    $cp->customer_type_id = $data->id;
                    $cp->product_id = $value->id;
                    $cp->price = $value->sale_price;
                    $cp->save();
                }
            } else {
                $data = customer_type::where("id", $request->id)->first();
                $data->name = $request->name;
                $data->save();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function customerTypePriceList(Request $request, $id)
    {

        $data = customer_type_price::where("customer_type_id", $id)->get();
        return view("customer-type-price-list", compact("data"));
    }

    public function updateCustomerPrice(Request $request)
    {
        $data = customer_type_price::where("id", $request->id)->first();
        $data->price = $request->price;
        $data->save();
    }


    public function updateCustomerPrice0(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'customer_type_id' => 'required',
            'price_type' => 'required',
            'type' => 'required',
            'value' => 'required',


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
            DB::beginTransaction();
            $data = customer_type_price::where("customer_type_id", $request->customer_type_id)->get();


            if ($request->price_type == "percentage") {
                if ($request->type == "increment") {

                    foreach ($data as $key => $value) {
                        $percentage = $value->price / 100 * $request->value;
                        $updatePrice = $value->price + $percentage;
                        $ctp = customer_type_price::where("id", $value->id)->first();

                        $ctp->price = $updatePrice;
                        $ctp->save();
                    }
                } else {
                    foreach ($data as $key => $value) {
                        $percentage = $value->price / 100 * $request->value;
                        $updatePrice = $value->price - $percentage;
                        $ctp = customer_type_price::where("id", $value->id)->first();

                        $ctp->price = $updatePrice;
                        $ctp->save();
                    }
                }
            } else {
                if ($request->type == "increment") {

                    foreach ($data as $key => $value) {

                        $updatePrice = $value->price + $request->value;
                        $ctp = customer_type_price::where("id", $value->id)->first();

                        $ctp->price = $updatePrice;
                        $ctp->save();
                    }
                } else {
                    foreach ($data as $key => $value) {
                        $updatePrice = $value->price - $request->value;
                        $ctp = customer_type_price::where("id", $value->id)->first();

                        $ctp->price = $updatePrice;
                        $ctp->save();
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function specialOffer(Request $request)
    {
        $products =   DB::table("products")->get();
        $data = special_offer::with("product")->orderBy("id", "desc")->get();
        return view("special-offer", compact("products", "data"));
    }

    public function saveSpecialOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'discount' => 'required',
            'expire_date' => 'required',
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
            DB::beginTransaction();


            foreach ($request->product_id as $key => $value) {
                DB::table("special_offer")->insert(array(
                    "product_id" => $value,
                    "discount" => $request->discount,
                    "expire_date" => $request->expire_date,
                ));
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function deleteSpecialOffer(Request $request)
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
            DB::table("special_offer")->whereIn("id", explode(",", $request->id))->delete();
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }


    public function TeamHierarchy(Request $request)
    {

        $arr = [];
        $sno = 0;
        $users = DB::table("users as a")->join('role as b', 'a.role_id', 'b.id')->select("a.*", "b.name as designation")->get();
        foreach ($users as $key => $value) {

            if ($value->id == 1) {
                $value->parent_id = 1;
            }
            $user = DB::table("users as a")
                ->join('role as b', 'a.role_id', 'b.id')
                ->select("a.*", "b.name as designation")
                ->where("a.id", $value->parent_id)->get();

            $name = array('v' => $value->name, 'f' => $value->name . '<div style="color:green;font-weight:bold">' . $value->designation . '</div>');
            $arr[$sno][] = $name;
            foreach ($user as $key1 => $value1) {

                $arr[$sno][]  = $value1->name;
            }
            $arr[$sno][]  = '';
            $sno++;
        }
        $data = json_encode($arr);

        // echo '<pre>';
        // print_r($data);
        // die;

        return view("team-hierarchy", compact('data'));
    }

    public function updateProductLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'product_id' => 'required',
            'product_location' => 'required',



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

            DB::table("products")->where("id", $request->product_id)->update(array(
                "product_location" => $request->product_location
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }

        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function updateBulkDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'discount' => 'required',
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

            DB::table("products")->update(array(
                "discount" => $request->discount
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }

    public function updateBulkDiscountCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'discount' => 'required',
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
            DB::table("customers")->update(array(
                "discount" => $request->discount
            ));
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
        return  redirect()->back()->with("success", "Save Successfully");
    }
}
