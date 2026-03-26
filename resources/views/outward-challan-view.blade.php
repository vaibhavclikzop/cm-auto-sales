@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>  {{ $data->outward_id }} Pick Ticket </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Pick Ticket View</h4>
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
                                <strong> PT No. </strong> <br>
                                <strong>Date. </strong> <br>



                            </div>
                            <div style="margin-left: 20px;color: black; font-weight: bold">
                                {{ $data->outward_id }} <br>
                                {{ date('d-m-Y h:i A', strtotime($data->created_at)) }} <br>



                            </div>
                        </div>






                    </div>
                    <div>

                    </div>
                    <div style="text-align: center">
                        <div
                            style="border: solid 1px; padding: 3px 20px; box-shadow: 2px 2px 2px black;color: black; font-weight: bold">
                            PIck Ticket
                        </div>
                        <div style="text-align: right;color: black; font-weight: bold">


                            <p class="mt-2">

                                Delivery Date : {{ $data->delivery_date }} <br>
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
                                <p style=" ">{{ $data->customer_name }}

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
                                <p style=" ">{{ $data->customer_name }}

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

                        $gst_type = 'IGST';
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
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">Part Code
                                    </th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">Product
                                        Description</th>
                                 
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">Product
                                        Location
                                    </th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; ">Current
                                        Stock
                                    </th>


                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px ; color:#393186; width: 60px">
                                        Qty</th>







                                </tr>
                            </thead>
                            <tbody>
                                @php

                                    $sno = 1;

                                @endphp
                                @foreach ($order_det as $item)
                                    <tr style="line-height:1;">
                                        <td
                                            style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px;color:black">
                                            {{ $sno++ }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px;color:black">
                                            {{ $item->brand }}</td>
                                               <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ $item->part_code }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ $item->product }}</td>
                                     
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black ">
                                            {{ $item->product_location }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black ">
                                            {{ $item->stock }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ number_format_indian($item->qty) }} </td>






                                    </tr>
                                @endforeach








                            </tbody>

                        </table>
                        <div style="text-align: right; margin-top: 5px">



                        </div>
                    </div>


                </div>



                <div>
                    <hr style="border:solid 2px; width: 100%; color:black">
                </div>
                <div class="" style="display: flex; justify-content: space-between">
                    <div style="color: black; font-weight: bold">
                        {{-- 
                        <div>
                            Our Bank Details
                        </div>
                        <div>

                            STATE BANK OF INDIA
                        </div>
                        <div>
                            Bank Account No. : 9876543210
                        </div>
                        <div>
                            IFSC : SBIN0000
                        </div> --}}
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
