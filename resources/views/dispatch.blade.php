@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Dispatch </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Dispatch </h4>
            </div>
            <div>
                <form action="" method="GET" class="d-flex">
                    <input type="hidden" value="{{ request('status') }}" name="status" hidden>
                    <div>
                        <label for="From Date">From Date</label>
                        <input type="date" name="fromDate" class="form-control" onchange="this.form.submit()"
                            value="{{ request('fromDate') }}">
                    </div>
                    <div>
                        <label for="To Date">To Date</label>
                        <input type="date" name="toDate" class="form-control mx-2" onchange="this.form.submit()"
                            value="{{ request('toDate') }}">
                    </div>
                </form>
            </div>
            <div>
                <button onclick="downloadCSV()" type="button" class="btn btn-secondary">Export to CSV</button>
            </div>

        </div>

        <div class="card-body">
            <h5>Dispatch Plan</h5>

            <table class="table" id="myTable">


                <thead class="">
                    <tr>
                        <th>S.No</th>
                        <th>Vehicle Name</th>
                        <th>Vehicle No</th>
                        <th>Vehicle 2</th>
                        <th>Invoice ID</th>
                        <th>Party Code</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>City</th>

                        <th>Invoice Date</th>
                        <th>Dispatch Date</th>
                        <th>No. of Box</th>
                        <th>Item Total</th>
                        <th>Total Qty</th>
                        <th>Remarks</th>
                        <th>File</th>
                        <th>User</th>
                        <th>MAP</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>


                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($dispatch_plan as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>
                                @if ($item->vehicle_name)
                                    {{ $item->vehicle_name }}
                                @else
                                    {{ $item->transport_name }}
                                @endif


                            </td>
                            <td>
                                @if ($item->vehicle_no)
                                    {{ $item->vehicle_no }}
                                @else
                                    {{ $item->tracking_no }}
                                @endif





                            </td>
                            <td>{{ $item->vehicle_name2 }} <br>{{ $item->vehicle_no2 }} </td>
                            <td>{{ $item->invoice_id }}</td>
                            <td> {{ $item->party_code }} </td>
                            <td>{{ $item->customer }} </td>
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->city }}</td>

                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->transport_date }}</td>
                            <td>{{ $item->no_of_box }}</td>
                            <td>{{ $item->item_total }}</td>
                            <td>{{ $item->total_qty }}</td>
                            <td>{{ $item->transport_remarks }}</td>
                            <td>
                                @if ($item->dispatch_file)
                                    <a href="/dispatch files/{{ $item->dispatch_file }}" target="_blank">File</a>
                                @else
                                    No File
                                @endif
                            </td>
                            <td>{{ $item->user }}</td>
                            <td>
                                <a href="https://www.google.com/maps?q={{ $item->coordinates }}" target="_blank">
                                    <i class="fa fa-street-view" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                @if (request('status') != 'delivered')
                                    <span class="badge bg-danger">Unverified</span>
                                @else
                                    <span class="badge bg-success">Verified</span>
                                @endif

                            </td>
                            <td>
                                <a href="/invoice-view/{{ $item->id }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i>
                                </a>
                                                 @if ($rolePermissions->whereIn('permission_name', ['dispatch_plan',"ready_to_deliver"])->where('view', 1)->where('edit', 1)->isNotEmpty())
                                @if (request('status') != 'delivered')
                                    <button class="btn btn-success btnDeliver btn-sm" value="{{ $item->id }}" 
                                        data-remarks="{{ $item->transport_remarks }}"
                                        data-number="{{ $item->number }}"> <i class="fa fa-paper-plane"
                                            aria-hidden="true"></i>
                                        Deliver</button>
                                    @if ($item->transport_id == 0)
                                        <button class="btn btn-dark btn-sm uploadFile" type="button"
                                            value="{{ $item->id }}"
                                            data-remarks="{{ $item->transport_remarks }}">Upload File</button>
                                    @endif
                                @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>



        </div>

    </div>


    <form action="{{ route('updateWithPassword') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="deliverModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delivered
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" hidden>
                        <div>
                            <label for="">Select Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">Select</option>
                                <option value="otp">Via OTP</option>
                                <option value="password">Via Transaction Password</option>
                            </select>
                        </div>
                        <div class="otpMainDiv mt-3">
                            <div class="">
                                <label for="">Number</label>
                                <input type="number" class="form-control" name="number" id="number" disabled
                                    placeholder="Number">
                            </div>
                            <div class="mt-3 otpDIV">
                                <label for="">Enter OTP. </label>
                                <input type="number" class="form-control" name="otp" id="otp"  
                                    placeholder="Enter OTP">

                            </div>
                        </div>

                        <div class="tPasswordDiv mt-3">
                            <div class="">
                                <label for="">Transaction Password</label>
                                <input type=" " class="form-control" name="transaction_password"
                                    placeholder="Transaction Password">
                            </div>
                            <div class="mt-3  ">
                                <label for="">Remarks </label>
                                <textarea type=" " class="form-control" name="remarks" id="t_remarks" placeholder="">textarea</textarea>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" id="btnSendOTP" class="btn btn-primary">Send OTP</button>
                        <button type="button" id="btnDelivered" class="btn btn-primary">Delivered</button>
                        <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <form action="{{ route('uploadDispatchFile') }}" method="POST" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="uploadFileModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Upload File
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="uploadID" name="id" hidden>
                        <div class="row">
                            <div class="col-12">
                                <label for="">File</label>
                                <input type="file" name="file" class="form-control">

                            </div>
                            <div class="col-12 mt-3">
                                <label for="">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" required></textarea>

                            </div>

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
        function downloadCSV() {
            let table = document.getElementById("myTable");
            let rows = table.querySelectorAll("tr");
            let csv = [];

            rows.forEach(row => {
                let cols = row.querySelectorAll("td, th");
                let rowData = Array.from(cols).map(col => `"${col.innerText}"`).join(",");
                csv.push(rowData);
            });

            let csvContent = csv.join("\n");
            let blob = new Blob([csvContent], {
                type: "text/csv;charset=utf-8;"
            });
            let link = document.createElement("a");

            link.setAttribute("href", URL.createObjectURL(blob));
            link.setAttribute("download", "dispatch.csv");
            link.style.display = "none";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <script>
        $(document).on("click", ".btnDeliver", function() {
            $("#number").val($(this).data("number"))
            $("#id").val($(this).val())
    
            $("#t_remarks").val($(this).data("remarks"))
            $("#deliverModal").modal("show")
        });

        $(".otpDIV").hide();
        $("#btnDelivered").hide();
        $("#btnSendOTP").on("click", function() {
            let number = $("#number").val();
            let id = $("#id").val();
            $.ajax({
                url: "/sendOtpSMS",
                type: "POST",
                data: {
                    number: number,
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                beforeSend: function() {
                    $("#btnSendOTP")
                        .prop("disabled", true)

                        .text("Sending...");
                },

                success: function(result) {
                    if (result.error == true) {
                        toastr.error(result.msg)
                    } else {
                        toastr.success(result.msg)

                        $(".otpDIV").show(500)
                    }
                },

                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Server error. Please try again.");
                },

                complete: function() {
                    $("#btnSendOTP").hide();
                    $("#btnDelivered").show();
                }
            });

        });

        $("#btnDelivered").on("click", function() {
            let otp = $("#otp").val();
            let id = $("#id").val();
            $.ajax({
                url: "/deliveredChallans",
                type: "POST",
                data: {
                    otp: otp,
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                beforeSend: function() {
                    $("#btnDelivered")
                        .prop("disabled", true)
                        .text("Checking....");
                },

                success: function(result) {
                    if (result.error == true) {
                        toastr.error(result.msg)
                    } else {
                        toastr.success(result.msg)
                        setTimeout(() => {
                            location.reload();
                        }, 2000);


                    }
                },

                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Server error. Please try again.");
                },

                complete: function() {
                    $("#btnDelivered")
                        .prop("disabled", false)
                        .text("Check Again....");

                }
            });

        });

        $(document).on("click", ".uploadFile", function() {
            $("#uploadID").val($(this).val())
            $("#remarks").val($(this).data("remarks"))
            $("#uploadFileModal").modal("show")
        });
        $(".otpMainDiv").hide()
        $(".tPasswordDiv").hide();
        $("#btnSendOTP").hide();
        $("#btnSave").hide();
        $("#type").on("change", function() {
            let type = $(this).val();
            if (type == "otp") {
                $("#btnSave").hide();
                $("#btnSendOTP").show()
                $(".otpMainDiv").show()
                $(".tPasswordDiv").hide()
            } else if (type == "password") {

                $("#btnSendOTP").hide()
                $(".otpMainDiv").hide()
                $(".tPasswordDiv").show()
                $("#btnSave").show();
            } else {
                $("#btnSave").hide();
                $("#btnSendOTP").hide()
                $(".otpMainDiv").hide()
                $(".tPasswordDiv").hide()
            }
        })
    </script>
@endsection
