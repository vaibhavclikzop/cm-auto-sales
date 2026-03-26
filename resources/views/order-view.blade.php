@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order View</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4> {{ Str::upper(request('status')) ?? '' }} Order View</h4>
            </div>
            <div class="">


                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>


            </div>
        </div>
        <div class="card-body table-responsive text-uppercase" id="PrintOrder" style="text-transform: uppercase;">
            <div style=" border:solid 1px black; padding: 5px">

                <div style="display: flex; justify-content: space-between; color:black; font-weight: bold">
                    <div>
                        <img src="/logo/{{ $data->img }}" alt="" style="width: 120px; margin-right: 5px;">

                    </div>
                    <div style="text-align: center">

                        <h4 style="font-size: 28px; font-weight: bolder">{{ $data->name }}</h4>
                        <p style="font-size: 11px">

                            {!! $data->c_address !!}
                            <br>
                            <span style="text-transform: none"> Phone :
                                E-Mail : {{ $data->c_email }} Web : </span>
                            <br>
                            GST : {{ $data->gst_no }} <br>

                        </p>
                    </div>
                    <div>
                        <img src="/isimark.png" alt="" style="width: 90px ">
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 11px">
                    <div>

                        <div style="display: flex">
                            <div style="color: black; font-weight: bold">
                                <strong> Order No. </strong> <br>
                                <strong>Date. </strong> <br>



                            </div>
                            <div style="margin-left: 20px;color: black; font-weight: bold">
                                {{ $data->order_id }} <br>
                                {{ date('d-m-Y', strtotime($data->created_at)) }} <br>



                            </div>
                        </div>






                    </div>
                    <div>

                    </div>
                    <div style="text-align: center">
                        <div
                            style="border: solid 1px; padding: 3px 20px; box-shadow: 2px 2px 2px black;color: black; font-weight: bold">
                            Order
                        </div>
                        <div style="text-align: right;color: black; font-weight: bold">


                            <p class="mt-2">


                                Status : {{ $data->status }} <br>
                            </p>
                        </div>

                    </div>
                </div>
                <div style="margin-top: 5px; display: flex; justify-content: space-between; font-size: 11.5px">
                    <div style="border: solid 1px; width: 50%; padding: 5px">
                        <div>
                            <strong>BILL TO : </strong>
                        </div>
                        <div style="display: flex; justify-content: space-between">
                            <div>
                                <p style=" ">{{ $data->company_name }}

                                    <br>
                                    {{ $data->bill_address }}, <br>
                                    {{ $data->bill_city }},
                                    {{ $data->bill_state }},
                                    {{ $data->bill_pincode }}
                                    <br>
                                    Contact : {{ $data->number }} <br>
                                    GST/UID : {{ $data->gst }}
                                </p>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>
                    <div style="border: solid 1px; width: 50%; padding: 5px">
                        <div>
                            <strong>DELIVERY TO : </strong>
                        </div>
                        <div style="display: flex; justify-content: space-between">
                            <div>
                                <p style=" ">{{ $data->company_name }}

                                    <br>
                                    {{ $data->ship_address }}, <br>
                                    {{ $data->ship_city }},
                                    {{ $data->ship_state }},
                                    {{ $data->ship_pincode }}
                                    <br>
                                    Contact : {{ $data->number }} <br>
                                    GST/UID : {{ $data->gst }}
                                </p>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>

                </div>



                <div>
                    @php
                        $gst_type = '';
                        $gst_bir = '';
                        //                         if ($data->bill_state == 'Haryana') {
                        //                             $gst_type = 'SGST + CGST';
                        //                             $gst_bir = '9 + 9%';
                        //                         } else {
                        //   }

                        if ($data->c_state != $data->state) {
                            $gst_type = 'IGST';
                        } else {
                            $gst_type = 'CGST + SGST';
                        }
                        $gst_bir = '18%';

                        $currencySign = '';
                        $data->currency = 'INR';
                        // if ($data->currency != 'INR') {
                        //     $currencySign = 'USD <i class="fa fa-dollar" aria-hidden="true"></i>';
                        // } else {

                        // }
                        $currencySign = 'INR ₹';

                    @endphp
                    <div class="mt-3">
                        <table class="w-100" id="myTable">
                            <thead>
                                <tr style="border: solid 1px; padding: 5px;font-size:11px ">
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px; color:#393186;  ">S.No</th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px; color:#393186;  ">Brand</th>

                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">Product
                                        Description</th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">Part Code
                                    </th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">HSN Code
                                    </th>


                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; width: 60px">
                                        Qty</th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px; color:#393186; text-align: right  ">
                                        Rate/Pcs
                                        <br>{!! $currencySign !!}

                                    </th>

                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186;; text-align: right ">
                                        Dis.
                                    </th>

                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px; color:#393186; width:100px ; text-align: right">

                                        @if ($data->currency == 'INR')
                                            Taxable Amt. <br> {!! $currencySign !!}
                                        @else
                                            Amount
                                            <br>{!! $currencySign !!}
                                        @endif

                                    </th>
                                    @if ($data->currency == 'INR')
                                        <th scope="col"
                                            style="border: solid 1px; padding: 5px; font-size:11px; color:#393186; text-align: right">
                                            {{ $gst_type }} <br> {!! $currencySign !!}
                                        </th>
                                    @endif


                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px; color:#393186; ; text-align: right ">
                                        Total

                                        <br>{!! $currencySign !!}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php

                                    $sno = 1;
                                    $total = 0;
                                    $total_gst = 0;
                                    $without_gst_total = 0;
                                    $price = 0;
                                    $discount_type = '';
                                    $taxable_amount = 0;
                                    $gst_amount = 0;
                                    $total_amount = 0;
                                    $sub_total = 0;
                                    $total_taxable_amount = 0;
                                    $total_gst_amount = 0;
                                    $data->freight_charges = 0;
                                    if ($data->currency == 'INR') {
                                        $freight_charges = ($data->freight_charges * 18) / 100 + $data->freight_charges;
                                    } else {
                                        $freight_charges = $data->freight_charges;
                                    }

                                @endphp
                                @foreach ($order_det as $item)
                                    @php

                                        $price = $item->price;
                                        $discount_type = $item->discount . '% ';
                                        $taxable_amount = $price - ($price * $item->discount) / 100;

                                        $taxable_amount = $item->qty * $taxable_amount;

                                        $gst_amount = ($taxable_amount * 18) / 100;

                                        $total_amount = $taxable_amount + $gst_amount;
                                        $sub_total += $total_amount;
                                        $total_taxable_amount += $taxable_amount;
                                        $total_gst_amount += $gst_amount;
                                    @endphp


                                    <tr style="line-height:1;">
                                        <td
                                            style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px;color:black">
                                            {{ $sno++ }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px;color:black">
                                            {{ $item->brand }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ $item->product }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ $item->part_code }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black ">
                                            {{ $item->hsn_code }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ number_format_indian($item->qty) }} </td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                            {{ number_format_indian($price, 2) }}
                                        </td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                            {{ $discount_type }}
                                        </td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                            {{ number_format_indian($taxable_amount, 2) }}
                                        </td>

                                        @if ($data->currency == 'INR')
                                            <td
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">

                                                @if ($gst_type != 'IGST')
                                                    {{ number_format_indian($gst_amount / 2, 2) }} +
                                                    {{ number_format_indian($gst_amount / 2, 2) }}
                                                @else
                                                    {{ number_format_indian($gst_amount, 2) }}
                                                @endif
                                                <br> {{ $gst_bir }}
                                            </td>
                                        @endif

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                            {{ number_format_indian($total_amount, 2) }}
                                        </td>


                                    </tr>
                                @endforeach
                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="8"
                                @else
                                    colspan="5" @endif
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        Sub Total {!! $currencySign !!}</td>
                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        {{ number_format_indian($total_taxable_amount, 2) }}</td>
                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        {{ number_format_indian($total_gst_amount, 2) }}</td>

                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        {{ number_format_indian($sub_total, 2) }}</td>
                                </tr>

                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="10"
                                @else
                                    colspan="7" @endif
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        Freight @if ($data->currency == 'INR')
                                            (inc. GST) {!! $currencySign !!}
                                        @else
                                            {{ $data->freight_type }} {!! $currencySign !!}
                                        @endif
                                    </td>



                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        {{ number_format_indian($freight_charges, 2) }}</td>
                                </tr>




                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="10"
                                @else
                                    colspan="7" @endif
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        Total {!! $currencySign !!}</td>


                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        {{ number_format_indian($freight_charges + $sub_total, 2) }}
                                    </td>
                                </tr>


                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="10"
                                @else
                                    colspan="7" @endif
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right; text-align: right">
                                        R/O Total Amount {!! $currencySign !!}</td>


                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black; text-align: right">
                                        {{ number_format_indian(round($freight_charges + $sub_total)) }}.00
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                        <div style="text-align: right; margin-top: 5px">

                            <strong> {{ numberToWords(round($freight_charges + $sub_total)) }} Rupees
                                Only</strong>

                        </div>
                    </div>


                </div>



                <div>
                    <hr style="border:solid 2px; width: 100%; color:black">
                </div>
                <div class="" style="display: flex; justify-content: space-between">
                    <div style="color: black; font-weight: bold">

                        <div>
                            Our Bank Details
                        </div>
                        <div>

                            CENTRAL BANK OF INDIA
                        </div>
                        <div>
                            BRANCH : SEC 15, CHANDIGARH
                        </div>

                        <div>
                            Bank Account No. : 5277112599
                        </div>
                        <div>
                            IFSC : CBIN0280413
                        </div>
                    </div>
                    <div style="color: #393185; text-align: center ; font-size: 10px">


                    </div>
                    <div style="text-align: right">
                        {{ $data->name }}
                        <br>
                        <br>
                        <br>

                        Auth. Signatory
                    </div>
                </div>
                <div style="margin-top:20px; text-align: center">
                    <h6 style="font-size: 11px">This is computer generated Proforma Invoice no signature required</h6>
                </div>
            </div>
        </div>

    </div>
@endsection
