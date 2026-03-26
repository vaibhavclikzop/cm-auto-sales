@extends('lead-app.layouts.main')
@section('main-section')
    <style>
        .myBtn {
            background: #eef2ff;
            color: #4f46e5;
            padding: 4px 10px;
            font-size: 11px;
            border-radius: 20px;
            font-weight: 600;
            outline: none;
            border: none;
        }
    </style>
    <div class="card">
        <div class="card-header">
            Leads
        </div>
        <div class="card-body">
            <div class="card-body p-2 d-block d-md-none">
                @foreach ($data as $row)
                    <div class="mb-3 p-3"
                        style="
                background:#ffffff;
                border-radius:14px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
                border-left:5px solid #4f46e5;
            ">

                        <!-- TOP ROW -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div style="font-weight:600;font-size:14px;color:#111;">
                                Lead #{{ $row->id }}
                            </div>
                            <span
                                style="
                        background:#eef2ff;
                        color:#4f46e5;
                        padding:4px 10px;
                        font-size:11px;
                        border-radius:20px;
                        font-weight:600;
                    ">
                                {{ $row->status_name }}
                            </span>
                        </div>

                        <!-- NAME -->
                        <div style="font-size:15px;font-weight:700;color:#000;">
                            {{ $row->name ?? 'N/A' }}
                        </div>

                        <!-- INFO GRID -->
                        <div class="mt-2" style="font-size:13px;color:#555;">
                            <div class="d-flex mb-1">
                                <div style="width:90px;font-weight:600;">Assigned:</div>
                                <div>{{ $row->user_name }}</div>
                            </div>

                            <div class="d-flex mb-1">
                                <div style="width:90px;font-weight:600;">Mobile:</div>
                                <div>{{ $row->mobile ?? '-' }}</div>
                            </div>

                            <div class="d-flex mb-1">
                                <div style="width:90px;font-weight:600;">Email:</div>
                                <div style="word-break:break-all;">{{ $row->email ?? '-' }}</div>
                            </div>
                            <div class="d-flex mb-1">
                                <div style="width:90px;font-weight:600;">Remind Date:</div>
                                <div style="word-break:break-all;">{{ $row->remind_date ?? '-' }}</div>
                            </div>
                            <div class="d-flex mb-1">
                                <div style="width:90px;font-weight:600;">Remind Time:</div>
                                <div style="word-break:break-all;">{{ $row->remind_time ?? '-' }}</div>
                            </div>
                            <div class="d-flex mb-1">
                                <div style="width:90px;font-weight:600;">Remarks:</div>
                                <div style="word-break:break-all;">{{ $row->remarks ?? '-' }}</div>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small style="color:#999;">
                                {{ date('d-M-y, h:i A', strtotime($row->created_at)) }}
                            </small>

                            <small style="color:#999;">
                                {{ date('d-M-y, h:i A', strtotime($row->updated_at)) }}
                            </small>


                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">

                            <button type="button" class="viewRemarks myBtn" value="{{ $row->id }}" style="">
                                View Remarks →
                                </a>
                                <button class="myBtn btnEdit" type="button" value="{{ $row->id }}"><i
                                        class="fa fa-pencil" aria-hidden="true"></i> Edit </button>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>




    <div class="modal fade" id="remarksModal">
        <div class="modal-dialog   modal-dialog-scrollable modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Remarks
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive">
                    <div id="remarksContainer"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('/lead-app/SaveLead') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add </span></h5>
                       
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">


                        <div class="col-md-4">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control">

                        </div>

                        <div class="col-md-4">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>

                        </div>

                        <div class="col-md-4">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control">

                        </div>



                        <div class="col-md-4 mt-3">
                            <label for="">Classification</label>
                            <select name="classification" id="classification" class="form-control">
                                <option value="">Select</option>
                                <option value="Hot">Hot</option>
                                <option value="Cold">Cold</option>
                                <option value="Warm">Warm</option>

                            </select>

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($Leadstatus as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Remind Date</label>
                            <input type="date" name="remind_date" id="remind_date" class="form-control">

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">Remind Time</label>
                            <input type="time" name="remind_time" id="remind_time" class="form-control">

                        </div>

                        <div class="col-md-12 mt-3">
                            <label for="">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" required></textarea>

                        </div>




                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    

    <script>
        $(document).on("click", ".viewRemarks", function() {



            var id = $(this).val();
            $.ajax({
                url: "/lead-app/GetRemarks",
                type: "POST",
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {

                    var remarksList = "";
                    var sno = 1;
                    result.forEach(element => {
                        remarksList += `
        <div class="mb-3 p-3"
            style="
                background:#ffffff;
                border-radius:14px;
                box-shadow:0 4px 10px rgba(0,0,0,0.08);
                border-left:4px solid #4f46e5;
            ">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span
                    style="
                        background:#eef2ff;
                        color:#4f46e5;
                        padding:4px 10px;
                        font-size:11px;
                        border-radius:20px;
                        font-weight:600;
                    ">
                    ${element.status}
                </span>

                <small style="color:#888;font-size:11px;">
                    #${sno++}
                </small>
            </div>

            <!-- REMARK -->
            <div style="font-size:14px;font-weight:600;color:#111;margin-bottom:6px;">
                ${element.remarks}
            </div>

            <!-- META INFO -->
            <div style="font-size:12px;color:#555;">
                <div class="d-flex mb-1">
                    <div style="width:90px;font-weight:600;">Reminder:</div>
                    <div>${element.remind_date} ${element.remind_time}</div>
                </div>

                <div class="d-flex mb-1">
                    <div style="width:90px;font-weight:600;">By:</div>
                    <div>${element.user}</div>
                </div>

                <div class="d-flex">
                    <div style="width:90px;font-weight:600;">Added:</div>
                    <div>${element.created_at}</div>
                </div>
            </div>
        </div>
    `;
                    });
                    $("#remarksContainer").html(remarksList)

                    $("#remarksModal").modal("show");
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });


        $(document).on("click", ".btnEdit", function() {


            $("#id").val($(this).val());


            var id = $(this).val();
            $.ajax({
                url: "/lead-app/GetLeadDetails",
                type: "POST",
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {


                    $.each(result, function(i, o) {
                        console.log(i, o)

                        $('input[name=' + i + ']').val(o);
                        $('select[name=' + i + ']').val(o);
                    })
                    $("#modal_name").text("Update Lead");
                    $("#exampleModal").modal("show");
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });



        })
    </script>
@endsection
