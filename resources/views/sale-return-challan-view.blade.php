@extends('layouts.main')
@section('main-section')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Purchase Order View</h4>
        </div>
        <div class="">

            <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                    aria-hidden="true"></i> Print</button>
        </div>
    </div>
    <div class="card-body" id="PrintOrder">
        <div style="border: solid 1px; padding:5px">

            <div style="display: flex; justify-content: space-between; color:black; font-weight: bold">
                <div>
                    <img src="/logo/{{ $po_mst->img }}" alt="" style="width: 190px ;">

                </div>
                <div style="text-align: center">

                    <h4 style="font-size: 28px; font-weight: bolder">{{ $po_mst->name }}</h4>
                    <p style="font-size:14px">

                        {!! $po_mst->address !!}
                        <br>
                        <span style="text-transform: none"> Phone : {{ $po_mst->number }}
                            E-Mail : {{ $po_mst->email }} </span>
                        <br>
                        GST : {{ $po_mst->gst_no }} <br>

                    </p>
                </div>
                <div>
                    <div style=" text-align: center">

                        <div
                            style="border: solid 1px; padding: 3px 10px; box-shadow: 2px 2px 2px black; font-size:11px; text-align: center">
                            Sale Return
                        </div>
                    </div>
                </div>
            </div>
            <hr style="padding: 0px; margin: 0">
            <div style="display: flex; justify-content: space-between; font-size: 10px">
                <div>
                    <h6 style="font-size:14px">Customer Details</h6>
                    <div style="display: flex">
                        <div>
                            <strong> Customer Name </strong> <br>
                            <strong> Invoice No : </strong> <br>



                        </div>
                        <div style="margin-left: 20px">
                            {{ $po_mst->customer }} <br>
                            {{ $po_mst->invoice_id }} <br>




                        </div>
                    </div>

                </div>
                <div>

                    <div style="display: flex">
                        <div>
                            <strong> Challan/Bill No : </strong> <br>
                            <strong>Challan/Bill Date </strong> <br>


                            <strong>Remarks. </strong>
                        </div>
                        <div style="margin-left: 20px">

                            GR-{{ $po_mst->id }} <br>

                            {{ date('d-m-Y', strtotime($po_mst->created_at)) }} <br>


                            {{ $po_mst->description }}

                        </div>
                    </div>

                </div>

            </div>




            <div>
                @php
                $gst_type = '';

                if (strtolower($po_mst->c_state) != strtolower($po_mst->state)) {
                $gst_type = 'IGST';
                } else {
                $gst_type = 'CGST + SGST';
                }

                @endphp
                <div class="mt-3">
                    <table class="w-100" id="myTable">
                        <thead>
                            <tr style="border: solid 1px; padding: 3px;font-size:11px ">
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px; color:#393186;  ">S.No</th>
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px; color:#393186;  ">Brand</th>
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186; ">Part Code
                                </th>
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186; ">Product
                                    Description</th>

                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186; ">HSN Code
                                </th>


                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186; width: 60px">
                                    Qty</th>

                                @if (request('type') != 'without')
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px; color:#393186; text-align: right  ">
                                    Rate/Pcs


                                </th>

                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186;; text-align: right ">
                                    Discount
                                </th>

                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186;; text-align: right ">
                                    Spc. Disc.
                                </th>
                                @endif

                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px ; color:#393186;; text-align: right ">
                                    Price
                                </th>
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px; color:#393186; width:100px ; text-align: right">
                                    Taxable Amt.
                                </th>
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px; color:#393186; width:100px ; text-align: right">
                                    {{ $gst_type }}
                                </th>
                                <th scope="col"
                                    style="border: solid 1px; padding: 3px; font-size:11px; color:#393186; width:100px ; text-align: right">
                                    Total
                                </th>




                            </tr>
                        </thead>
                        <tbody>
                            @php

                            $sno = 1;
                            $taxable_amount = 0;
                            $total_taxable_amount = 0;
                            $sub_total = 0;
                            $total = 0;
                            $grand_total = 0;
                            $discount_price = 0;
                            $special_discount_price = 0;
                            $total_amount = 0;
                            $gst_summary = [];
                            $gst_amount = 0;
                            $total_tx_amt = 0;
                            $total_gst = 0;
                            $total_qty = 0;

                            @endphp
                            @foreach ($po_det as $item)
                            @php

                            $discount_price = $item->price - ($item->price / 100) * $item->discount;

                            if ($po_mst->discount_type == 'discount') {
                            $special_discount_price =
                            $discount_price - ($discount_price / 100) * $item->special_discount;

                            } else {
                            $special_discount_price =
                            $item->price- ($item->price / 100) * ($item->discount + $item->special_discount);

                            }
                            $taxable_amount = $special_discount_price * $item->qty;

                            $gst_amount = ($taxable_amount / 100) * $item->gst;
                            $total_amount = $taxable_amount + $gst_amount;
                            $total_tx_amt += $taxable_amount;
                            $total_taxable_amount += $total_amount;
                            $total_gst += $gst_amount;
                            $total_qty += $item->qty;

                            if (!isset($gst_summary[$item->gst])) {
                            $gst_summary[$item->gst] = 0;
                            }

                            $gst_summary[$item->gst] += $taxable_amount;
                            $sub_total = $total_taxable_amount;
                            @endphp


                            <tr style="line-height:1;">
                                <td
                                    style="border: solid 1px; padding: 3px; text-transform: uppercase; font-size:11px;color:black">
                                    {{ $sno++ }}
                                </td>

                                <td
                                    style="border: solid 1px; padding: 3px; text-transform: uppercase; font-size:11px;color:black">
                                    {{ $item->brand }}
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black">
                                    {{ $item->part_code }}
                                </td>

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black">
                                    {{ $item->product_name }}
                                </td>

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black ">
                                    {{ $item->hsn_code }}
                                </td>

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black">
                                    {{ number_format_indian($item->qty) }}
                                </td>

                                @if (request('type') != 'without')
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($item->price, 2) }}
                                </td>

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ $item->discount }} % <br>
                                    {{ $discount_price }}
                                </td>
                                {{--@if ($po_mst->is_invoice == 0 && $po_mst->status != 'cancel') --}}
                                <td style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right"
                                    contenteditable="true" data-id="{{ $item->id }}"
                                    class="updateValue" data-field="discount">
                                    <!-- {{ $item->special_discount }} -->
                                </td>
                                @else
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ $item->special_discount }}
                                </td>
                                @endif
                                {{-- @endif--}}

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($special_discount_price, 2) }}
                                </td>

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($taxable_amount, 2) }}
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    @if (strtolower($po_mst->c_state) != strtolower($po_mst->state))
                                    {{ $item->gst }} %
                                    @else
                                    {{ $item->gst / 2 }} % + {{ $item->gst / 2 }} %
                                    @endif

                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($total_amount, 2) }}
                                </td>







                            </tr>
                            @endforeach

                            <tr>
                                <td colspan="5"
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    Sub Total
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;;  ">
                                    {{ number_format_indian($total_qty) }}
                                </td>
                                @if (request('type') != 'without')
                                <td colspan="4">

                                </td>
                                @else
                                <td colspan="1">

                                </td>
                                @endif

                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($total_tx_amt, 2) }}
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($total_gst, 2) }}
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($sub_total, 2) }}
                                </td>

                            </tr>



                            <tr>
                                <td @if (request('type')=='without' ) colspan="9" @else colspan="12" @endif
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    Grand Total
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian($sub_total, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td @if (request('type')=='without' ) colspan="9" @else colspan="12" @endif
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    Round OFF
                                </td>
                                <td
                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                    {{ number_format_indian(round($sub_total), 2) }}
                                </td>
                            </tr>


                        </tbody>

                    </table>
                    <div style="text-align: right; margin-top: 5px">

                        <strong> {{ numberToWords(round($sub_total)) }} Rupees
                            Only</strong>

                    </div>
                </div>



            </div>

            <div class="d-flex mt-1 justify-content-between">
                <div style="border: solid 1px black; width: 40%; padding:10px; font-size: 10px">
                    <p>
                        Material Report <br>
                        Material OK <br>
                        Qty. <br> <br>
                        Material Rejection <br>
                        Qty.
                    </p>
                </div>
                <div style="border: solid 1px black; width: 60%; padding:10px; font-size: 10px">
                    <div style="text-align: center">


                        <p>Receiver Signature : </p>
                        <hr>

                        <h6 class="" style="font-size: 10px">For {{ $po_mst->name }}</h6>

                        <p class="mt-1">Authorized Signatory</p>
                    </div>
                </div>

            </div>

        </div>
    </div>


</div>
@endsection