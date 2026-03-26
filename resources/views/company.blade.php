@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Company</h4>
            </div>
            <div class="">

                @if ($rolePermissions->where('permission_name', 'company')->where('view', 1)->where('create', 1)->isNotEmpty())
                    <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add</button>
                @endif

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Img</th>
                        <th> Name</th>
                        <th> Number</th>
                        <th> Email</th>
                        <th> Address</th>
                        <th> GST No</th>
                        <th> Invoice No.</th>
                        <th> PT No.</th>

                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($store as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

                            <td><img src="/logo/{{ $item->img }}" width="45px" alt=""></td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->gst_no }}</td>
                            <td>{{ $item->invoice_prefix }}{{ $item->invoice_no }}</td>
                            <td>{{ $item->pt_prefix }}{{ $item->pt_no }}</td>


                            <td>
                                @if ($rolePermissions->where('permission_name', 'company')->where('view', 1)->where('edit', 1)->isNotEmpty())
                                    <button class="btn btn-primary btn-sm edit" type="button"
                                        data-data="{{ @json_encode($item) }}"><i class="fa fa-pencil"
                                            aria-hidden="true"></i></button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveCompany') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add </span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">
                        <div class="col-md-6">
                            <label for="">Image</label>
                            <input type="file" name="file" class="form-control">

                        </div>


                        <div class="col-md-6">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-6  mt-2">
                            <label for="">Email</label>
                            <input type="text" name="email" id="email" class="form-control">

                        </div>

                        <div class="col-md-6  mt-2">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control">

                        </div>

                        <div class="col-md-12 mt-2">
                            <label for="">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>

                        </div>

                        <div class="col-md-6  mt-2">
                            <label for="">State</label>
                            <select name="state" id="state" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }} </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-6  mt-2">
                            <label for="">GST NO.</label>
                            <input type="" name="gst_no" id="gst_no" class="form-control">

                        </div>

                        <div class="col-md-6  mt-2">
                            <label for="">Invoice Prefix</label>
                            <input type="" name="invoice_prefix" id="invoice_prefix" class="form-control"
                                value="INV">

                        </div>

                        <div class="col-md-6  mt-2">
                            <label for="">Invoice No.</label>
                            <input type="number" name="invoice_no" id="invoice_no" class="form-control" value="1">

                        </div>


                        <div class="col-md-6  mt-2">
                            <label for="">Pick Ticket Prefix</label>
                            <input type="" name="pt_prefix" id="pt_prefix" class="form-control"
                                value="" required>

                        </div>

                        <div class="col-md-6  mt-2">
                            <label for="">Pick Ticket No.</label>
                            <input type="number" name="pt_no" id="pt_no" class="form-control"
                                value="" required>

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
        $(document).on("click", ".edit", function() {
            var data = $(this).data("data");
            $.each(data, function(key, value) {
                $("input[name='" + key + "']").val(value)
                $("select[name='" + key + "']").val(value)
                $("textarea[name='" + key + "']").val(value)
            });
            $("#modal_name").text("Update");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add");



            $("#id").val("");

            $("#exampleModal").modal("show");
        });
    </script>
@endsection
