@extends('layouts.main')
@section('main-section')
@push('title')
<title>Invoices </title>
@endpush
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Invoices</h4>
            <div id="alertMsg">

            </div>

        </div>
        <div>

            <a class="btn {{ request()->status == 'pending' ? 'btn-success' : 'btn-primary' }}"
                href="/invoices?status=pending&id={{ request('id') }}">Pending</a>
            <a class="btn {{ request()->status == 'dispatch' ? 'btn-success' : 'btn-primary' }}"
                href="/invoices?status=dispatch&id={{ request('id') }}">Dispatch</a>
            <a class="btn {{ request()->status == 'delivered' ? 'btn-success' : 'btn-primary' }}"
                href="/invoices?status=delivered&id={{ request('id') }}">Delivered</a>

        </div>


    </div>
    <div class="card-body">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Invoice No </th>
                    <th>PT ID </th>
                    <th>Customer </th>
                    <th>Order ID </th>
                    <th>Invoice Date </th>
                    <th>Status </th>
                    <th>User </th>
                    <th>Action </th>
                </tr>
            </thead>
            <tbody>
                @php
                $sno = 1;
                @endphp
                @foreach ($outward as $item)
                <tr>
                    <td>{{ $sno++ }}</td>
                    <td>{{ $item->invoice_id }}</td>
                    <td>{{ $item->outward_id }}</td>
                    <td style="white-space: normal; word-break: break-word;">
                    {{$item->company}}
                    </td>

                    <td>{{ $item->order_id }}</td>


                    <td>
                        @if ($item->is_e_invoice == 0)
                        {{ date('d-m-Y', strtotime($item->invoice_convert_date)) }}
                        @else
                        {{ date('d-m-Y', strtotime($item->AckDt)) }}
                        @endif
                    </td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->user }}</td>
                    <td>
                        @if ($item->is_invoice == 0)
                        <button class="btn btn-primary btn-sm convertInvoice" type="button"
                            value="{{ $item->id }}">Convert to Invoice</button>
                        @endif

                        <div class="btn-group">
                            <button class="btn btn-success btn-sm dropdown-toggle p-1" data-bs-toggle="dropdown">
                                <i class="fa fa-download"></i>
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/download-invoice/{{ $item->id }}?type=with">With Discount</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/download-invoice/{{ $item->id }}?type=without">Without Discount</a>
                                </li>
                            </ul>
                        </div>
                        <a class="btn btn-dark btn-sm" href="/invoice-view/{{ $item->id }}"><i
                                class="fa fa-eye" aria-hidden="true"></i></a>
                        @if ($item->is_invoice == 1)
                        <button class="btn btn-danger btn-sm sendEmail" type="button"
                            value="{{ $item->id }}" data-email="{{ $item->email }}"><i
                                class="fa fa-envelope" aria-hidden="true"></i></button>
                        @endif

                        @if ($item->is_e_invoice == 1)
                        <a class="btn btn-success btn-sm" href="{{ $item->EinvoicePdf }}" target="_blank"> <i
                                class="fa-solid fa-file-invoice"></i>
                            <i class="fa fa-download" aria-hidden="true"></i> </a>
                        @else
                        <button class="btn btn-secondary btn-sm sendEInvoice" type="button"
                            value="{{ $item->id }}"> <i class="fa-solid fa-file-invoice"></i>
                            E-Invoice</button>
                        @endif

                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>



<div class="modal fade" id="emailModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Send Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <input type="hidden" hidden id="id" name="id">
                        <label for="">Email</label>
                        <input type="text" name="email" id="email" class="form-control" required>

                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" id="sendEmail" class="btn btn-primary">Send Email</button>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).on("click", ".sendEmail", function() {
        $("#email").val($(this).data("email"))
        $("#id").val($(this).val())
        $("#emailModal").modal("show")
    });

    $("#sendEmail").on("click", function() {

        let id = $("#id").val();
        let email = $("#email").val();


        $("#alert").html("");

        // Validate before sending
        if (!id || !email) {
            $("#alert").html(`
            <div class="alert alert-danger"><strong>Error:</strong> Email or Dispatch ID missing.</div>
            `);
            return;
        }

        $.ajax({
            url: "/sendEmail",
            type: "POST",
            data: {
                id: id,
                email: email,

            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            // Show while sending
            beforeSend: function() {
                $("#alert").html(`
                <div class="alert alert-info">Sending email... please wait.</div>
                    `);
                $("#sendEmail").attr("disabled", "disabled");
                $("#sendEmail").text("Sending email...");

            },

            success: function(result) {
                 let msg = result.message || result || "Email sent successfully ";
                $("#alert").html(`
                <div class="alert alert-success"><strong>Success:</strong> ${msg}</div>
                     `);
            },

            complete: function() {

                $("#sendEmail").removeAttr("disabled");
                $("#sendEmail").text("Send again");
                console.log("Request completed");
            },

            error: function(xhr) {
                console.log(xhr);

                let message = "Something went wrong.";


                if (xhr.responseText) {
                    message = xhr.responseText;
                }

                $("#alert").html(`
                <div class="alert alert-danger"><strong>Error:</strong> ${message}</div>
            `);
            },
        });
    });
    $(document).on("click", ".sendEInvoice", function() {
        let invoice_id = $(this).val()
        let btn = $(this);
        $.ajax({
            url: "/generateEInvoice",
            type: "POST",
            data: {
                invoice_id: invoice_id,


            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            // Show while sending
            beforeSend: function() {
                btn.prop("disabled", true);
                btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            },

            success: function(result) {
                // ${result.error.results.message.EinvoicePdf}

                if (result.status == false) {
                    $("#alertMsg").html(`<div
                            class="alert alert-success"
                            role="alert"
                        >
                            <strong>E-invoice Generated successfully : <br>Ack No. ${result.error.results.message.AckNo}</strong> <br>   Request ID : ${result.error.results.requestId}
                        </div>
                        `);
                    let pdfUrl = result.error.results.message.EinvoicePdf;

                    btn.replaceWith(`
    <a href="${pdfUrl}" target="_blank"
       class="btn btn-success btn-sm">
       <i class="fa-solid fa-download"></i> Download Invoice
    </a>
`);
                }
            },

            complete: function(xhr) {
                btn.prop("disabled", false);
                btn.html('Completed');
                console.log(xhr.responseText);
                if (xhr.responseText) {
                    message = xhr.responseText;
                    let data = JSON.parse(message)

                    if (data.status == true) {

                        $("#alertMsg").html(`<div
                            class="alert alert-danger"
                            role="alert"
                        >
                            <strong>${data.message}</strong>  
                        </div>
                        `);
                        btn.prop("disabled", true);
                        btn.html('error');
                    }
                }

            },

            error: function(xhr) {

                if (xhr.responseText) {
                    message = xhr.responseText;
                    let data = JSON.parse(message)
                    message = (JSON.parse(message));

                    if (data.status == true) {

                        $("#alertMsg").html(`<div
                            class="alert alert-danger"
                            role="alert"
                        >
                            <strong>${data.message}</strong>  
                        </div>
                        `);
                        btn.prop("disabled", true);
                        btn.html('error');
                    } else {

                        $("#alertMsg").html(`<div
                            class="alert alert-danger"
                            role="alert"
                        >
                            <strong>${message.message}</strong>  
                        </div>
                        `);
                    }
                }



            },
        });

    });
</script>
@endsection