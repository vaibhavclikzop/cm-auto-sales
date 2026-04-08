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

    <strong>Dear {{ $data->customer_name }},</strong><br><br>

    Please find attached the invoice {{ $data->invoice_id }} dated
    {{ date('d-m-Y', strtotime($data->created_at)) }} for your reference.<br><br>

    Kindly verify the details and let us know if any clarification is required.<br>
    We request you to process the payment as per the agreed terms.<br><br>

    <!-- Download Button -->
    <a href="{{ url('download-invoice/' . $data->id) }}?type=without"
        style="display:inline-block;
              padding:10px 20px;
              background-color:#28a745;
              color:#ffffff;
              text-decoration:none;
              border-radius:5px;
              font-size:14px;">
        Download Invoice
    </a>

    <br><br>

</body>

</html>
