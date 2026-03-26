@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">
        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header bg-white" style="display: flex; justify-content: space-between;">
                        <div>Services</div>


                    </div>
                    <div class="card-body table-responsive text-uppercase" id="PrintOrder" style="text-transform: uppercase;">
                        <input type="text" id="searchBox" class="form-control mb-3" placeholder="Search here...">

                        <div class="mobile-cards">

                            @foreach ($data as $item)
                                <div class="card mb-4 border-2 shadow-lg rounded-4">
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <h5 class="fw-bold text-primary mb-2">
                                                <i class="bi bi-person-circle me-2"></i>{{ $item->customerDetails->name }}
                                            </h5>
                                            <p class="mb-1"><i
                                                    class="bi bi-telephone me-2"></i>{{ $item->customerDetails->number }}
                                            </p>
                                            <p class="mb-1"><i
                                                    class="bi bi-envelope me-2"></i>{{ $item->customerDetails->email }}</p>
                                            <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>
                                                {{ $item->customerDetails->address }},
                                                {{ $item->customerDetails->city }},
                                                {{ $item->customerDetails->state }} - {{ $item->customerDetails->pincode }}
                                            </p>
                                        </div>

                                        <hr>


                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Installation Date:</strong> {{ $item->installation_date }}</p>  
                                            <p class="mb-1"><strong>Description:</strong> {{ $item->complain_description }}</p>
                                            {{-- <p class="mb-1"><strong>Status:</strong>
                                                <span
                                                    class="badge bg-{{ $item->status == 'pending'
                                                        ? 'warning'
                                                        : ($item->status == 'processing'
                                                            ? 'info'
                                                            : ($item->status == 'completed'
                                                                ? 'success'
                                                                : 'danger')) }}">
                                                    {{ $item->status }}
                                                </span>
                                            </p> --}}
                                            <p class="mb-0"><strong>Created At:</strong>
                                                {{ date('d-m-Y', strtotime($item->created_at)) }}</p>
                                            @if ($item->last_work_status == 'completed')
                                                <p class="mb-0"><strong> <small> Recommendations : </small></strong>
                                                    <small> {{ $item->recommendation }} </small>
                                                    <br>

                                                    <strong> <small> Suggestions : </small></strong>
                                                    <small> {{ $item->suggestion }} </small>

                                                    <br>

                                                    <strong> <small> Last Comment : </small></strong>
                                                    <small> {{ $item->last_comment }} </small>

                                                </p>
                                            @endif
                                        </div>



                                        <hr>

                                        <!-- Products -->
                                        <div class="mb-3">
                                            <h6 class="fw-bold text-secondary">Products</h6>
                                            @foreach ($item->complainDetails as $complain)
                                                <p class="mb-1"><i
                                                        class="bi bi-box-seam me-2"></i>{{ $complain->product_name }}</p>
                                            @endforeach
                                        </div>




                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <!-- Left Checklist Button -->
                                            <div>
                                                <button class="btn btn-outline-theme btn-sm checkList"
                                                    data-data="{{ @json_encode($item->checkList) }}" type="button"> <i
                                                        class="fa fa-check" aria-hidden="true"></i> Checklist</button>
                                            </div>

                                            <!-- Right Action Buttons -->
                                            <div class="d-flex gap-2">
                                                @if ($item->status == 'pending')
                                                    <button class="btn btn-outline-success btn-sm px-3 updateStatus"
                                                        value="{{ $item->id }}" data-type="accept">
                                                        <i class="bi bi-check-circle me-1"></i> Accept
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm px-3 updateStatus"
                                                        value="{{ $item->id }}" data-type="reject">
                                                        <i class="bi bi-x-circle me-1"></i> Reject
                                                    </button>
                                                @endif

                                                @if ($item->status == 'processing' && $item->last_work_status == '')
                                                    <button class="btn btn-success btn-sm px-3 updateService"
                                                        value="{{ $item->id }}" data-type="under_process">
                                                        Start
                                                    </button>
                                                    <button class="btn btn-danger btn-sm px-3 updateService"
                                                        value="{{ $item->id }}" data-type="rejected">
                                                        Reject
                                                    </button>
                                                @endif

                                                @if ($item->last_work_status == 'under_process')
                                                    <button class="btn btn-danger btn-sm px-3 updateService"
                                                        value="{{ $item->id }}" data-type="stop">
                                                        Stop
                                                    </button>
                                                @endif

                                                @if ($item->last_work_status == 'stop')
                                                    <button class="btn btn-warning btn-sm px-3 updateService"
                                                        value="{{ $item->id }}" data-type="under_process">
                                                        Resume
                                                    </button>
                                                    <button class="btn btn-success btn-sm px-3 completeStatus"
                                                        value="{{ $item->id }}" data-data="{{ @json_encode($item) }}">
                                                        Complete
                                                    </button>
                                                @endif
                                            </div>
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

    <form action="{{ route('service-app/updateStatus') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="statusModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Update Status
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="complain_id" id="complain_id" hidden>
                        <input type="text" name="type" id="type" hidden>
                        Are you sure you want to <span id="upText"></span> this request?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-theme btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <form action="{{ route('service-app/updateServiceStatus') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="updateServiceModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Update Service
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="complain_id" id="sComplain_id" hidden>
                        <input type="text" name="type" id="sType" hidden>
                        <label for="">Remarks</label>
                        <textarea name="last_comment" class="form-control" required></textarea>
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


    <form action="{{ route('service-app/updateCompleteStatus') }}" method="POST" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="completeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Complete Service
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="complain_id" id="completeComplain_id" hidden>
                        <div class="row">
                            <div class="col-3">
                                <label for="file" class="upload-box">
                                    <div class="upload-content">
                                        <i class="bi bi-plus-lg"></i>
                                        <p>Upload</p>
                                    </div>
                                </label>
                                <input type="file" name="files[]" id="file" class="d-none" accept="image/*"
                                    multiple>
                            </div>
                            <div class="col-3">
                                <label for="file" class="upload-box">
                                    <div class="upload-content">
                                        <i class="fa fa-camera" aria-hidden="true"></i>
                                        <p>Camera</p>
                                    </div>
                                </label>
                                <input type="file" name="file[]" id="file" class="d-none">
                            </div>

                            <div class="col-12 mt-3">

                                <strong for="">Payment Type</strong> <span id="pay_type"></span>

                            </div>
                            <div class="col-6 mt-3">

                                <label for="">Payable Amount</label>
                                <input type="" class="form-control" id="payable_amount" disabled>

                            </div>
                            <div class="col-6 mt-3">

                                <label for="">Paid Amount</label>
                                <input type="" class="form-control" id="paid_amount" name="paid_amount" required>

                            </div>
                            <div class="col-12 mt-2">

                                <label for="">Suggestions</label>
                                <input type="text" name="suggestions" class="form-control" required
                                    placeholder="Suggestions">
                            </div>

                            <div class="col-12 mt-2">

                                <label for="">Recommendations</label>
                                <input type="text" name="recommendations" class="form-control" required
                                    placeholder="Recommendations">
                            </div>
                            <div class="col-12 mt-2">

                                <label for="">Comment</label>
                                <input type="text" name="last_comment" class="form-control" required
                                    placeholder="Comment">
                            </div>


                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm btn-theme">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <form action="{{ route('service-app/updateCheckList') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="checkListModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Checklist
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.No</th>

                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tblCheckList">

                            </tbody>

                        </table>
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



    <style>
        .upload-box {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border: 2px dashed #6c63ff;
            border-radius: 12px;
            background: #f9f9ff;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            height: 80px;
            text-align: center;
        }

        .upload-box:hover {
            background: #eef1ff;
            border-color: #4f46e5;
            box-shadow: 0px 4px 12px rgba(79, 70, 229, 0.2);
        }

        .upload-content i {
            font-size: 32px;
            color: #4f46e5;
            margin-bottom: 8px;
        }

        .upload-content p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .custom-checkbox {
            appearance: none;
            /* Remove default look */
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #ccc;
            border-radius: 4px;
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .custom-checkbox:checked {
            background-color: green;
            /* Your custom color */
            border-color: green;
            position: relative;
        }

        .custom-checkbox:checked::after {
            content: "✔";
            color: white;
            font-size: 14px;
            position: absolute;
            top: -2px;
            left: 2px;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#searchBox').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.mobile-cards .card').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $(document).on('click', ".updateStatus", function() {

                var type = $(this).data('type');
                var id = $(this).val();
                $("#upText").text(type);
                $("#complain_id").val(id);
                $("#type").val(type);
                $("#statusModal").modal("show");

            });

            $(document).on('click', ".updateService", function() {

                var type = $(this).data('type');
                var id = $(this).val();
                $("#sComplain_id").val(id);
                $("#sType").val(type);
                $("#updateServiceModal").modal("show");

            });


            $(document).on('click', ".completeStatus", function() {

                var id = $(this).val();
                var data = $(this).data('data');
                console.log(data);
                $("#pay_type").html(`<span class="badge bg-info">${data.payment_type}</span>`);
                $("#payable_amount").val(data.payable_amt);
                $("#paid_amount").val(data.paid_amount);
                $("#completeComplain_id").val(id);
                $("#completeModal").modal("show");

            });

            $(document).on("click", ".checkList", function() {

                var data = $(this).data("data");
                var html = "";
                var sno = 1;

                data.forEach(element => {
                    var input = "";
                    if (element.status == 1) {
                        checked =
                            ` <input type="checkbox" class="custom-checkbox" checked disabled>`;
                    } else {
                        checked =
                            ` <input type="checkbox" class="checks custom-checkbox" name="checks[]" value="${element.id}">`;
                    }
                    html += `<tr>
                        <td>${sno++}</td>
                        <td>${element.name}</td>
                        <td>
                        ${checked}
                        </td>
                        </tr>`;
                });

                $("#tblCheckList").html(html)
                $("#checkListModal").modal("show")


            });
        });
    </script>
@endsection
