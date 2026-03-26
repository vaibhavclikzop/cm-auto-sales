<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
</head>
@php
    $data->currency = 'INR';
    $currencySign = '';
@endphp



<body style="margin:0;padding:0;background:#ffffff;font-family:Arial, Helvetica, sans-serif;text-transform:uppercase;">


  <strong>  Dear {{ $data->customer_name }}, </strong><br>

    Please find attached the invoice {{ $data->invoice_id }} dated {{ date('d-m-Y', strtotime($data->created_at)) }} for
    your reference.<br>

    Kindly verify the details and let us know if any clarification is required.<br>
    We request you to process the payment as per the agreed terms.
    <br>
    <br>



    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="800" cellpadding="5" cellspacing="0" style="border:1px solid #000;color:#000;">

                    <!-- HEADER -->
                    <tr>
                        <td width="25%">
                            <img src="https://cmautomobiles.net/logo/1767951973.png" style="width:120px;">
                        </td>
                        <td width="50%" align="center">
                            <h4 style="margin:0;font-size:28px;font-weight:bold;">
                                {{ $data->name }}
                            </h4>
                            <p style="font-size:11px;margin:5px 0;">
                                {!! $data->c_address !!}<br>
                                <span style="text-transform:none">
                                    Phone : E-Mail : {{ $data->c_email }} Web :
                                </span><br>
                                GST : {{ $data->gst_no }}
                            </p>
                        </td>
                        <td width="25%" align="right">

                        </td>
                    </tr>

                    <!-- INVOICE INFO -->
                    <tr>
                        <td colspan="3">
                            <table width="100%" cellpadding="5" cellspacing="0">
                                <tr>
                                    <td width="40%" style="font-size:11px;font-weight:bold;">
                                        Invoice No. : {{ $data->invoice_id }}<br>
                                        Date : {{ date('d-m-Y h:i A', strtotime($data->created_at)) }}
                                    </td>
                                    <td width="20%" align="center">
                                        <div style="border:1px solid #000;padding:5px;font-weight:bold;">
                                            Invoice
                                        </div>
                                    </td>
                                    <td width="40%" align="right" style="font-size:11px;font-weight:bold;">
                                        Delivery Date : {{ $data->delivery_date }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- BILL TO / DELIVERY TO -->
                    <tr>
                        <td colspan="3">
                            <table width="100%" cellpadding="5" cellspacing="0">
                                <tr>
                                    <td width="50%" style="border:1px solid #000;font-size:11px;">
                                        <strong>BILL TO :</strong><br>
                                        {{ $data->customer_name }}<br>
                                        {{ $data->address }},<br>
                                        {{ $data->city }}, {{ $data->state }}, {{ $data->pincode }}<br>
                                        Contact : {{ $data->number }}<br>
                                        GST/UID : {{ $data->gst }}
                                    </td>
                                    <td width="50%" style="border:1px solid #000;font-size:11px;">
                                        <strong>DELIVERY TO :</strong><br>
                                        {{ $data->customer_name }}<br>
                                        {{ $data->address }},<br>
                                        {{ $data->city }}, {{ $data->state }}, {{ $data->pincode }}<br>
                                        Contact : {{ $data->number }}<br>
                                        GST/UID : {{ $data->gst }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ITEMS TABLE -->
                    <tr>
                        <td colspan="3">
                            <table width="100%" cellpadding="5" cellspacing="0"
                                style="font-size:11px;border-collapse:collapse;">
                                <tr style="color:#393186;font-weight:bold;">
                                    <th style="border:1px solid #000;">S.No</th>
                                    <th style="border:1px solid #000;">Brand</th>
                                    <th style="border:1px solid #000;">Product Description</th>
                                    <th style="border:1px solid #000;">Part Code</th>
                                    <th style="border:1px solid #000;">HSN Code</th>
                                    <th style="border:1px solid #000;">Qty</th>
                                    <th style="border:1px solid #000;text-align:right;">Rate/Pcs<br>INR ₹</th>
                                    <th style="border:1px solid #000;text-align:right;">Dis.</th>
                                    <th style="border:1px solid #000;text-align:right;">Taxable Amt.<br>INR ₹</th>
                                    <th style="border:1px solid #000;text-align:right;">IGST<br>INR ₹</th>
                                    <th style="border:1px solid #000;text-align:right;">Total<br>INR ₹</th>
                                </tr>

                                @php $sno=1; @endphp

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
                                    <tr>
                                        <td style="border:1px solid #000;">{{ $sno++ }}</td>
                                        <td style="border:1px solid #000;">{{ $item->brand }}</td>
                                        <td style="border:1px solid #000;">{{ $item->product }}</td>
                                        <td style="border:1px solid #000;">{{ $item->part_code }}</td>
                                        <td style="border:1px solid #000;">{{ $item->hsn_code }}</td>
                                        <td style="border:1px solid #000;">{{ number_format_indian($item->qty) }}</td>
                                        <td style="border:1px solid #000;text-align:right;">
                                            {{ number_format_indian($item->price, 2) }}
                                        </td>
                                        <td style="border:1px solid #000;text-align:right;">
                                            {{ $item->discount }}%
                                        </td>
                                        <td style="border:1px solid #000;text-align:right;">
                                            {{ number_format_indian($item->qty * ($item->price - ($item->price * $item->discount) / 100), 2) }}
                                        </td>
                                        <td style="border:1px solid #000;text-align:right;">
                                            {{ number_format_indian(($item->qty * ($item->price - ($item->price * $item->discount) / 100) * 18) / 100, 2) }}
                                        </td>
                                        <td style="border:1px solid #000;text-align:right;">
                                            {{ number_format_indian($item->qty * ($item->price - ($item->price * $item->discount) / 100) * 1.18, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- SUB TOTAL -->
                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="8" @else colspan="5" @endif
                                        style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;font-weight:bold;">
                                        Sub Total {!! $currencySign !!}
                                    </td>
                                    <td style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;">
                                        {{ number_format_indian($total_taxable_amount, 2) }}
                                    </td>
                                    <td style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;">
                                        {{ number_format_indian($total_gst_amount, 2) }}
                                    </td>
                                    <td style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;">
                                        {{ number_format_indian($sub_total, 2) }}
                                    </td>
                                </tr>

                                <!-- FREIGHT -->
                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="10" @else colspan="7" @endif
                                        style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;font-weight:bold;">
                                        Freight
                                        @if ($data->currency == 'INR')
                                            (inc. GST) {!! $currencySign !!}
                                        @else
                                            {{ $data->freight_type }} {!! $currencySign !!}
                                        @endif
                                    </td>
                                    <td style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;">
                                        {{ number_format_indian($freight_charges, 2) }}
                                    </td>
                                </tr>

                                <!-- TOTAL -->
                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="10" @else colspan="7" @endif
                                        style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;font-weight:bold;">
                                        Total {!! $currencySign !!}
                                    </td>
                                    <td style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;">
                                        {{ number_format_indian($freight_charges + $sub_total, 2) }}
                                    </td>
                                </tr>

                                <!-- ROUND OFF TOTAL -->
                                <tr>
                                    <td @if ($data->currency == 'INR') colspan="10" @else colspan="7" @endif
                                        style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;font-weight:bold;">
                                        R/O Total Amount {!! $currencySign !!}
                                    </td>
                                    <td style="border:1px solid #000;padding:5px;font-size:11px;text-align:right;">
                                        {{ number_format_indian(round($freight_charges + $sub_total)) }}.00
                                    </td>
                                </tr>

                                <!-- AMOUNT IN WORDS -->
                                <tr>
                                    <td colspan="100%" style="padding:5px;text-align:right;font-size:11px;">
                                        <strong>
                                            {{ numberToWords(round($freight_charges + $sub_total)) }} Rupees Only
                                        </strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td colspan="3">
                            <hr style="border:2px solid #000;">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <table width="100%" cellpadding="5" cellspacing="0">
                                <tr>
                                    <td style="font-size:11px;font-weight:bold;">
                                        Our Bank Details<br>
                                        STATE BANK OF INDIA<br>
                                        Bank Account No. : 9876543210<br>
                                        IFSC : SBIN0000
                                    </td>
                                    <td align="right" style="font-size:11px;">
                                        {{ $data->name }}<br><br><br>
                                        Auth. Signatory
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" align="center" style="font-size:11px;">
                            This is computer generated Proforma Invoice no signature required
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>
    Thank you for your continued support. <br>

    Regards,<br>
    {{ $data->name }}
</body>

</html>
