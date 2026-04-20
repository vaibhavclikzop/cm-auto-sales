<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 9px;
        }

        .main {
            border: 1px solid black;

        }


        .row {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .products>tbody th,
        .products>tbody td {
            padding: 0px 3px;

        }

        .products>tbody th:first-child,
        .products>tbody td:first-child {
            border-left: none;
        }

        .products>tbody th:last-child,
        .products>tbody td:last-child {
            border-right: none;
        }
    </style>

</head>

<body>

    <div class="main">
        <table width="100%" style="border:none;">

            <tr style="border:none">

                <td width="20%" style="border:none">

                    <img src="{{ public_path('logo/' . $data->img) }}" style="width:120px">

                </td>

                <td width="60%" style="border:none;text-align:center">

                    <h2 style="margin:0;color:#0b3a6f;font-weight:bold">
                        {{ $data->name }}
                    </h2>

                    <p style="font-size:11px;margin-top:5px">

                        {!! $data->c_address !!}

                        <br>

                        Phone :
                        E-Mail : {{ $data->c_email }}
                        Web :

                        <br>

                        GST : {{ $data->gst_no }}

                    </p>

                </td>

                <td width="20%" style="border:none;text-align:right">

                    @if ($data->is_e_invoice && $qr_code)
                        <img src="{{ $qr_code }}" style="width:120px">
                    @endif
                    <br>

                    <div
                        style="
                            border:1px solid black;
                            display:inline-block;
                            padding:4px 15px;
                            margin-top:5px;
                            font-weight:bold;
                            box-shadow:2px 2px 0px #000;
                            ">

                        TAX INVOICE

                    </div>

                </td>

            </tr>

        </table>
        <table width="100%" style="border:none;margin-top:15px">

            <tr>

                <td style="border:none;width:70%; padding-left: 7px">

                    <strong>INVOICE NO.</strong> : {{ $data->invoice_id }}

                    <br>

                    <strong>ACK NO & DATE :</strong>

                    @if ($data->is_e_invoice)
                        {{ $data->AckNo }} / {{ date('d-m-Y', strtotime($data->AckDt)) }}
                    @endif

                    <br>

                    <strong>IRN NO :</strong>

                    @if ($data->is_e_invoice)
                        {{ $data->Irn }}
                    @endif

                </td>

                <td style="border:none;width:30%"></td>

            </tr>

        </table>

        <table width="100%">

            <tr>

                <td width="50%" style="border-left: none;  padding-left: 7px">

                    <strong>BILL TO :</strong>

                    <p>

                        {{ $data->customer_name }}

                        <br>

                        {{ $data->bill_address }}

                        <br>

                        {{ $data->bill_city }},
                        {{ $data->bill_state }},
                        {{ $data->bill_pincode }}

                        <br>

                        Contact : {{ $data->number }}

                        <br>

                        GST : {{ $data->gst }}

                    </p>

                </td>

                <td width="50%" style=" padding-left: 7px; border-right: none;">

                    <strong>DELIVERY TO :</strong>

                    <p>

                        {{ $data->customer_name }}

                        <br>

                        {{ $data->ship_address }}

                        <br>

                        {{ $data->ship_city }},
                        {{ $data->ship_state }},
                        {{ $data->ship_pincode }}

                        <br>

                        Contact : {{ $data->number }}

                        <br>

                        GST : {{ $data->gst }}

                    </p>

                </td>

            </tr>

        </table>



        <table class="products">


            <tbody>
                <tr>

                    <th>S.No</th>
                    <th>Brand</th>
                    <th>Part Code</th>
                    <th>Product</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    @if ($type != 'without')
                        <th>Disc</th>
                        <th>Sp Disc</th>
                    @endif
                    <th>Price</th>
                    <th>Taxable Amt.</th>
                    <th>CGST + SGST</th>
                    <th>Total</th>

                </tr>





                @php
                    $sno = 1;

                    $total_taxable = 0;
                    $total_gst = 0;
                    $grand_total = 0;
                @endphp

                @foreach ($order_det as $item)
                    @php

                        $discount_price = $item->price - ($item->price / 100) * $item->discount;

                        if ($data->discount_type == 'discount') {
                            $special_discount_price =
                                $discount_price - ($discount_price / 100) * $item->special_discount;
                        } else {
                            $special_discount_price =
                                $item->price - ($item->price / 100) * ($item->discount + $item->special_discount);
                        }

                        $price = $special_discount_price;

                        $taxable_amount = $price * $item->qty;

                        $gst_amount = ($taxable_amount / 100) * $item->gst;

                        $total = $taxable_amount + $gst_amount;

                        $total_taxable += $taxable_amount;
                        $total_gst += $gst_amount;
                        $grand_total += $total;

                    @endphp

                    <tr>

                        <td>{{ $sno++ }}</td>

                        <td>{{ $item->brand }}</td>

                        <td>{{ $item->part_code }}</td>

                        <td>{{ $item->product }}</td>

                        <td>{{ $item->hsn_code }}</td>

                        <td>{{ $item->qty }}</td>

                        <td class="right">{{ number_format($item->price, 2) }}</td>

                        @if ($type != 'without')
                            <td class="right">{{ $item->discount }}%</td>
                            <td class="right">{{ number_format($item->special_discount, 2) }}</td>
                        @endif

                        <td class="right">{{ number_format($price, 2) }}</td>

                        <td class="right">{{ number_format($taxable_amount, 2) }}</td>

                        <td class="center">

                            @if (strtolower($data->c_state) != strtolower($data->state))
                                {{ $item->gst }} %
                            @else
                                {{ $item->gst / 2 }}% + {{ $item->gst / 2 }}%
                            @endif

                        </td>

                        <td class="right">{{ number_format($total, 2) }}</td>

                    </tr>
                @endforeach


                <tr>

                    <td colspan="{{ $type != 'without' ? 10 : 8 }}" class="right">
                        <strong>SUB TOTAL</strong>
                    </td>
                    <td class="right">{{ number_format($total_taxable, 2) }}</td>

                    <td class="right">{{ number_format($total_gst, 2) }}</td>

                    <td class="right">{{ number_format($grand_total, 2) }}</td>

                </tr>

                <tr>

                    <td colspan="{{ $type != 'without' ? 12 : 10 }}" class="right">
                        <strong>GRAND TOTAL</strong>
                    </td>
                    <td class="right"><strong>{{ number_format($grand_total, 2) }}</strong></td>

                </tr>
                <tr>
                    <td colspan="{{ $type != 'without' ? 12 : 10 }}" style=" text-align: right">
                        Round OFF
                    </td>
                    <td style=" text-align: right">
                        {{ number_format_indian(round($grand_total), 2) }}
                    </td>
                </tr>


            </tbody>

        </table>
        @php
            $sub_total = $grand_total;
        @endphp

        <br>

        <div class="right" style="padding-right: 5px">

            <strong> {{ numberToWords(round($grand_total)) }} Rupees Only</strong>

        </div>

        <br>

        <hr>

        <table width="100%">

            <tr>

                <td style="border-left: none; padding-left: 5px">

                    <strong>Our Bank Details</strong>

                    <br>

                    {{ $data->bank_name }}

                    <br>

                    BRANCH : {{ $data->branch_name }}

                    <br>

                    Bank Account No : {{ $data->account_number }}

                    <br>

                    IFSC : {{ $data->ifsc_code }}

                </td>

                <td class="right" style="border-right: none; padding-right: 5px">

                    {{ $data->name }}

                    <br><br><br>

                    Auth. Signatory

                </td>

            </tr>

        </table>

        <br>

        <strong style="margin-left: 5px">Terms & Conditions</strong>

        <ol>

            <li>Goods once sold shall not be taken back or exchanged.</li>

            <li>Payment shall be made as per agreed payment terms.</li>

            <li>All disputes subject to Mohali jurisdiction only.</li>

        </ol>

        <div class="center">

            This is computer generated Tax Invoice no signature required

        </div>

    </div>

</body>

</html>
