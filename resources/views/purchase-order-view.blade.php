@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between;">
            <div></div>
            <div>

                <button type="button" class="btn btn-primary" onclick="printcontent()">
                    Print
                </button>

            </div>

        </div>
        <div class="card-body table-responsive text-uppercase" id="PrintOrder" style="text-transform: uppercase; ">
            <div style="border:solid 1px black; padding:5px">



                <div style="display: flex; justify-content: space-between; color:black; font-weight: bold">
                    <div>
                        <img src="/logo/{{ $data->img }}" alt="" style="width: 190px; margin-right: 5px;">

                    </div>
                    <div style="text-align: center">

                        <h4 style="font-size: 28px; font-weight: bolder">{{ $data->name }}</h4>
                        <p style="font-size: 11px">

                            {!! $data->address !!}
                            <br>
                            <span style="text-transform: none"> Phone : {{ $data->number }}
                                E-Mail : {{ $data->email }} </span>
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
                            <div>
                                <strong> PO No. </strong> <br>
                                <strong>Date. </strong> <br>

                            </div>
                            <div style="margin-left: 20px">
                                PO-{{ $data->id }} <br>
                                {{ date('d-m-Y', strtotime($data->created_at)) }} <br>

                            </div>
                        </div>






                    </div>
                    <div>

                    </div>
                    <div style="text-align: center">
                        <div style="border: solid 1px; padding: 3px 20px; box-shadow: 2px 2px 2px black">
                            Purchase Order
                        </div>
                        <div style="text-align: right">


                            {{-- <p class="mt-2">
                            Payment Terms : {{ $data->payment_terms }} <br>
                            Delivery Time : {{ $data->delivery_time }} <br>
                            Validity : {{ $data->validity }} <br>
                        </p> --}}
                        </div>

                    </div>
                </div>
                <div style="margin-top: 5px; display: flex; justify-content: space-between; font-size: 11.5px">
                    <div style="border: solid 1px; width: 50%; padding: 5px">
                        <div>
                            <strong>Party Details : </strong>
                        </div>
                        <div style="display: flex; justify-content: space-between">
                            <div>
                                <p style=" ">{{ $data->company }}

                                    <br>
                                    {{ $data->vendor_address }}, <br>
                                    {{ $data->vendor_city }},
                                    {{ $data->vendor_state }},
                                    {{ $data->vendor_pincode }}
                                    <br>
                                    Contact : {{ $data->vendor_number }} <br>
                                    GST/UID : {{ $data->vendor_gst }}
                                </p>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>
                    <div style="border: solid 1px; width: 50%; padding: 5px">
                        <div>
                            <strong>Order Details : </strong>
                        </div>
                        <div style="display: flex; justify-content: space-between">
                            <div style="display: flex">
                                <div>
                                    <strong> PO No. </strong> <br>
                                    <strong>Date. </strong> <br>

                                </div>
                                <div style="margin-left: 20px">
                                    PO-{{ $data->id }} <br>
                                    {{ date('d-m-Y', strtotime($data->created_at)) }} <br>

                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>

                </div>



                <div>

                    <div class="mt-3">
                        <table class="w-100" id="myTable">
                            <thead>
                                <tr style="border: solid 1px; padding: 5px;font-size:11px  ">
                                    <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">S.No</th>
                                    <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">Brand</th>


                                    <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">Product
                                        Description</th>
                                    <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">Part Code
                                    <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">HSN Code
                                    </th>


                                    <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">Qty</th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px;text-align:center ">Rate</th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px;text-align:center ">
                                       @if ($data->vendor_state == $data->state)
                                            CGST + SGST
                                        @else
                                            IGST
                                        @endif
                                    </th>
                                    <th scope="col"
                                        style="border: solid 1px; padding: 5px; font-size:11px;text-align:center ">Total
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
                                    $total_qty = 0;

                                @endphp
                                @foreach ($po_det as $item)
                                    @php
                                        $gst_amount = ($item->qty * $item->price * $item->gst) / 100;
                                        $total = $item->price * $item->qty + $gst_amount;
                                        $sub_total += $total;
                                        $total_qty += $item->qty;
                                        $total_gst_amount += $gst_amount;
                                        $total_gst_amount += $gst_amount;
                                        $total_taxable_amount += $item->price;
                                    @endphp


                                    <tr style="line-height:1;">
                                        <td
                                            style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px">
                                            {{ $sno++ }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                            {{ $item->brand }}</td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                            {{ $item->name }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px ">
                                            {{ $item->part_no }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px ">
                                            {{ $item->hsn_code }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px ">
                                            {{ $item->qty }} {{ $item->uom }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px ;text-align:right">
                                            {{ $item->price }}</td>
                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;text-align:right ">
                                            @if ($data->vendor_state == $data->state)
                                                {{ $item->gst / 2 }}% + {{ $item->gst / 2 }}% <br>
                                                {{ $gst_amount / 2 }} + {{ $gst_amount / 2 }}
                                            @else
                                                {{ $item->gst }}%<br>
                                                {{ $gst_amount }}
                                            @endif


                                        </td>

                                        <td
                                            style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;text-align:right">
                                            {{ number_format_indian($total, 2) }}</td>


                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5"
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px; text-align: right">
                                        Grand Total </td>

                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;text-align:right">
                                        {{ $total_qty }}</td>

                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;text-align:right">
                                        {{ $total_taxable_amount }}</td>

                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;text-align:right">
                                        {{ $total_gst_amount }}</td>




                                    <td colspan=""
                                        style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px;text-align:right">
                                        {{ number_format_indian($sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right" colspan="9">
                                        {{ numberToWords(round($sub_total)) }} Rupees Only
                                    </th>
                                </tr>

                            </tbody>

                        </table>
                    </div>


                </div>



                <div>
                    <hr style="border:solid 2px; width: 100%; color:black">

                </div>
                <div class="" style="display: flex; justify-content: space-between">
                    <div>

                    </div>
                    <div>
                        {{ $data->name }}
                        <br>
                        <br>
                        <br>

                        Auth. Signatory
                    </div>
                </div>
                <div style="margin-top:20px; text-align: center">
                    <h6 style="font-size: 11px">This is computer generated Purchase order no signature required</h6>
                </div>
            </div>
        </div>
    </div>
@endsection
