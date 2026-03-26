@extends('layouts.main')
@section('main-section')
    @push('title')
        <title> {{ $data->invoice_id }} Tax Invoice </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class=" ">
                <h4>Tax Invoice</h4>
                <div>
                    Discount Type : <span class="badge bg-danger"> {{ $data->discount_type }}</span>
                </div>
            </div>
            <div class="d-flex">


                @if ($data->is_invoice == 0 && $data->status != 'cancel')
                    <button class="btn btn-dark convertInvoice mx-3" type="button" value="{{ $data->id }}"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Convert this ticket to invoice">
                        <i class="fa fa-shuffle" aria-hidden="true"></i>
                    </button>
                @endif
                <form action="">
                    <input type="checkbox" onchange="this.form.submit()" name="type" value="without"
                        @if (request('type') == 'without') @checked(true) @endif id="withOutDiscount"> <label
                        for="withOutDiscount">Without Discount</label>
                    <button type="button" onclick="printcontent()" class="btn btn-primary mx-3"><i class="fa fa-print"
                            aria-hidden="true"></i> Print</button>
                </form>




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
                        @if ($data->is_e_invoice)
                            <img src="https://router.mastersindia.co/api/v1/einvoice/qrcode/amFuX21hcl8yMDI1LTI2-699d9c1aff8a237edc26a3da/"
                                style="width: 120px">
                        @endif


                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 11px">
                    <div>

                        <div style="display: flex">
                            <div style="color: black; font-weight: bold">
                                <strong> Invoice No. </strong> <br>

                                @if ($data->is_e_invoice)
                                    ACK No & Date : <br>
                                    IRN NO : <br>
                                @else
                                    Invoice Date :
                                @endif



                            </div>
                            <div style="margin-left: 20px;color: black; font-weight: bold">
                                {{ $data->invoice_id }} <br>

                                @if ($data->is_e_invoice)
                                    {{ $data->AckNo }} / {{ date('d-m-Y', strtotime($data->AckDt)) }}<br>
                                    {{ $data->Irn }} <br>
                                @elseif ($data->invoice_convert_date)
                                    {{ date('d-m-Y', strtotime($data->invoice_convert_date)) }}
                                @else
                                    {{ date('d-m-Y', strtotime($data->invoice_date)) }}
                                @endif



                            </div>
                        </div>

                    </div>
                    <div>

                    </div>
                    <div style="text-align: center">
                        <div
                            style="border: solid 1px; padding: 3px 20px; box-shadow: 2px 2px 2px black;color: black; font-weight: bold">
                            Tax Invoice
                        </div>
                        <div style="text-align: right;color: black; font-weight: bold">


                            <p class="mt-2">

                                {{-- Delivery Date : {{ $data->delivery_date }} <br> --}}

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

                        if (strtolower($data->c_state) != strtolower($data->state)) {
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
                                @foreach ($order_det as $item)
                                    @php

                                        $discount_price = $item->price - ($item->price / 100) * $item->discount;

                                        if ($data->discount_type == 'discount') {
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

                                        foreach ($gst as $key => $value) {
                                            if ($item->gst == $value->gst) {
                                                if (!isset($gst_summary[$value->gst])) {
                                                    $gst_summary[$value->gst] = 0;
                                                }

                                                $gst_summary[$value->gst] += $taxable_amount;
                                            }
                                        }

                                        $sub_total = $total_taxable_amount;
                                    @endphp


                                    <tr style="line-height:1;">
                                        <td
                                            style="border: solid 1px; padding: 3px; text-transform: uppercase; font-size:11px;color:black">
                                            {{ $sno++ }}</td>

                                        <td
                                            style="border: solid 1px; padding: 3px; text-transform: uppercase; font-size:11px;color:black">
                                            {{ $item->brand }}</td>
                                        <td
                                            style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ $item->part_code }}</td>

                                        <td
                                            style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ $item->product }}</td>

                                        <td
                                            style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black ">
                                            {{ $item->hsn_code }}</td>

                                        <td
                                            style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black">
                                            {{ number_format_indian($item->qty) }} </td>

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
                                            @if ($data->is_invoice == 0 && $data->status != 'cancel')
                                                <td style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right"
                                                    contenteditable="true" data-id="{{ $item->id }}"
                                                    class="updateValue" data-field="discount">
                                                    {{ $item->special_discount }}
                                                </td>
                                            @else
                                                <td
                                                    style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                                    {{ $item->special_discount }}
                                                </td>
                                            @endif
                                        @endif

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
                                            @if (strtolower($data->c_state) != strtolower($data->state))
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
                                    <td @if (request('type') == 'without') colspan="9" @else colspan="12" @endif
                                        style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                        Grand Total
                                    </td>
                                    <td
                                        style="border: solid 1px; padding: 3px;  text-transform: uppercase;font-size:11px;color:black;; text-align: right">
                                        {{ number_format_indian($sub_total, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td @if (request('type') == 'without') colspan="9" @else colspan="12" @endif
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
                <hr>

                <div style="font-size: 11px">
                    <span><strong> Terms & Conditions</strong></span>

                    <ol>
                        <li>Goods once sold shall not be taken back or exchanged.</li>
                        <li>
                            Payment shall be made as per agreed payment terms; failing which,
                            interest @ 2% per month shall be applicable.
                        </li>
                        <li>
                            All disputes, if any, shall be subject to Mohali jurisdiction only.
                        </li>
                    </ol>

                </div>
                <div style="margin-top:20px; text-align: center">
                    <h6 style="font-size: 11px">This is computer generated Tax Invoice no signature required</h6>
                </div>

            </div>
        </div>

    </div>


    <form action="{{ route('convertToInvoice') }}" method="POST">
        @csrf
        <div class="modal fade" id="invoiceModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Convert To Invoice
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="invoiceID" name="id">
                        You are going to convert this Ticket to invoice <br>
                        <div class="d-none">
                            <label for="">Invoice Amount</label>
                            <input type="text" id="invoiceAmt" disabled class="form-control">
                            <label for="" class="mt-3">Additional Discount</label>
                            <input type="number" step="0.01" name="discount" class="form-control" required
                                value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).on("keyup", ".updateValue", function() {
            let id = $(this).data("id")
            let value = $(this).text().trim();
            let field = $(this).data("field")

            $.ajax({
                url: "/updateOutwardDetValue",
                type: "POST",
                data: {
                    id: id,
                    value: value,
                    field: field,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {

                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });
        $(document).on("click", ".convertInvoice", function() {

            $("#invoiceID").val($(this).val())
            $("#invoiceModal").modal("show");
        });
    </script>
@endsection
