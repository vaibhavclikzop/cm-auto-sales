@extends('salesapp.layouts.main')
@section('main-section')
    <style>
        .dispatch-card-neo {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            padding: 14px;
            margin-bottom: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .dispatch-card-neo:active {
            transform: scale(0.98);
        }

        .neo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(270deg, #6366f1, #3b82f6, #06b6d4);
            background-size: 600% 600%;
            animation: gradientMove 6s ease infinite;
            color: #fff;
            padding: 14px;
            border-radius: 16px;
        }

        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .neo-status {
            background: rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .neo-quick {
            display: flex;
            justify-content: space-between;
            margin: 14px 4px;
            font-size: 14px;
        }

        .neo-quick i {
            color: #2563eb;
        }

        .neo-expand {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.6s ease;
        }

        .dispatch-card-neo.open .neo-expand {
            max-height: 500px;
        }

        .neo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            padding-top: 10px;
        }

        .neo-grid span {
            font-size: 12px;
            color: #6b7280;
        }

        .neo-grid strong {
            font-size: 14px;
        }

        .neo-actions {
            position: relative;
            display: flex;
            justify-content: space-around;
            margin-top: 18px;
        }

        .neo-btn {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 12px 22px rgba(0, 0, 0, 0.25);
            transition: transform 0.3s ease;
        }

        .neo-btn:active {
            transform: scale(0.9);
        }

        .neo-btn.primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .neo-btn.success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }
    </style>
    <div class="">
        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header bg-white" style=" ">
                        <div> <strong> Ready To Deliver </strong></div>
                        <hr>
                        <div>

                            <form action="" method="GET" class="row">
                                <input type="hidden" value="{{ request('status') }}" name="status" hidden>
                                <div class="col-6">
                                    <label for="From Date">From Date</label>
                                    <input type="date" name="fromDate" class="form-control" onchange="this.form.submit()"
                                        value="{{ request('fromDate') }}">
                                </div>
                                <div class="col-6">
                                    <label for="To Date">To Date</label>
                                    <input type="date" name="toDate" class="form-control mx-2"
                                        onchange="this.form.submit()" value="{{ request('toDate') }}">
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="d-block d-md-none">

                            @foreach ($dispatch_plan as $item)
                                <div class="dispatch-card-neo" onclick="toggleCard(this)">

                                    <!-- Animated Header -->
                                    <div class="neo-header">
                                        <div>
                                            <div class="neo-id">#{{ $item->outward_id }}</div>
                                            <div class="neo-customer">{{ $item->customer }}</div>
                                            <div class="neo-customer">{{ $item->number }}</div>
                                            <div class="neo-customer">{{ $item->party_code }}</div>
                                        </div>
                                        @if (request('status') != 'delivered')
                                            <span class="neo-status">Unverified</span>
                                        @endif
                                    </div>

                                    <!-- Always Visible -->
                                    <div class="neo-quick">

                                        <div><i class="fa fa-map-marker"></i> {{ $item->address }}</div>
                                    </div>

                                    <!-- Expandable Content -->
                                    <div class="  ">

                                        <div class="neo-grid ">
                                            <div>
                                                <span>Invoice</span> <br>
                                                <strong>{{ $item->invoice_id }}</strong>
                                            </div>
                                            <div>
                                                <span>Dispatch Date</span>
                                                <br>
                                                <strong>{{ $item->transport_date }}</strong>
                                            </div>
                                            <div>
                                                <span>Boxes</span>
                                                <strong>{{ $item->no_of_box }}</strong>
                                            </div>
                                            <div>
                                                <span>Qty</span>
                                                <strong>{{ $item->total_qty }}</strong>
                                            </div>
                                            <div>
                                                <span>Item</span>
                                                <strong> {{ $item->item_total }}</strong>
                                            </div>
                                            <div>
                                                <span>User</span>
                                                <strong>{{ $item->user }}</strong>
                                            </div>
                                        </div>

                                        <!-- Floating Actions -->
                                        <div class="neo-actions">
                                            @if (request('status') == 'delivered')
                                                <div class="">
                                                    <button class="btn btn-success " type="button"> Delivered</button>
                                                </div>
                                            @else
                                                <div class="">
                                                    <button class="btn btn-primary btnDeliver" value="{{ $item->id }}"
                                                        data-number="{{$item->number}}"> <i class="fa fa-paper-plane"
                                                            aria-hidden="true"></i> Deliver</button>
                                                </div>
                                            @endif


                                            <a href="https://www.google.com/maps?q={{ $item->coordinates }}"
                                                target="_blank" class="neo-btn success">
                                                <i class="fa fa-street-view" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            @endforeach

                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>




    <form action="" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="deliverModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delivered
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" hidden>
                        <div class="">
                            <label for="">Number</label>
                            <input type="number" class="form-control" name="number" id="number" disabled
                                placeholder="Number">
                        </div>
                        <div class="mt-3 otpDIV">
                            <label for="">Enter OTP.  </label>
                            <input type="number" class="form-control" name="otp" id="otp" required
                                placeholder="Enter OTP">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" id="btnSendOTP" class="btn btn-primary">Send OTP</button>
                        <button type="button" id="btnDelivered" class="btn btn-primary">Delivered</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <script>
        $(document).on("click", ".btnDeliver", function() {
            $("#number").val($(this).data("number"))
            $("#id").val($(this).val())
            $("#deliverModal").modal("show")
        });

        $(".otpDIV").hide();
        $("#btnDelivered").hide();
        $("#btnSendOTP").on("click", function() {
            let number = $("#number").val();
            let id = $("#id").val();
            $.ajax({
                url: "/sales-app/sendOtpSMS",
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
                url: "/sales-app/deliveredChallan",
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

        })
    </script>
@endsection
