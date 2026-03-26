@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Mode of transport</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Mode of transport</h4>
            </div>
            <div class="">

                @if ($rolePermissions->where('permission_name', 'mode_of_transport')->where('view', 1)->where('create', 1)->isNotEmpty())
                    <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add </button>
                @endif

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Name</th>
                        <th> Number</th>
                        <th>Vehicle Name</th>
                        <th>Vehicle No</th>
                        <th>Username</th>


                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->vehicle_name }}</td>
                            <td>{{ $item->vehicle_no }}</td>
                            <td>{{ $item->user_name }}</td>



                            <td>
                                @if ($rolePermissions->where('permission_name', 'mode_of_transport')->where('view', 1)->where('edit', 1)->isNotEmpty())
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
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('saveModeOfTransport') }}">
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
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label for="">Vehicle Name.</label>
                            <input type="" name="vehicle_name" id="vehicle_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Vehicle No.</label>
                            <input type="" name="vehicle_no" id="vehicle_no" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label for="">Username</label>
                            <input type="" name="user_name" id="user_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">Password</label>
                            <input type="" name="password" id="password" class="form-control" required>
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
            var data = $(this).data("data")
            $.each(data, function(key, value) {
                $("input[name=" + key + "]").val(value)
            })


            $("#modal_name").text("Update ");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add ");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });
    </script>
@endsection
