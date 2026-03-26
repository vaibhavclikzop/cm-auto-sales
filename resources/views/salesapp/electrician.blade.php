@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">
        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header bg-white" style="display: flex; justify-content: space-between;">
                        <div>Architect</div>
                        <div>

                            <button class="btn btn-theme btn-sm" type="button" id="add">Add</button>
                        </div>

                    </div>
                    <div class="card-body table-responsive text-uppercase" id="PrintOrder"
                        style="text-transform: uppercase;">
                        <input type="text" id="searchBox" class="form-control mb-3" placeholder="Search here...">

                        <div class="mobile-cards">
                            @php $sno = 1; @endphp
                            @foreach ($data as $item)
                                <div class="card mb-3 shadow-sm border-0">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $item->company }}</h5>
                                        <p><strong>Name:</strong> {{ $item->name }}</p>
                                        <p><strong>Number:</strong> {{ $item->number }}</p>
                                        <p><strong>Email:</strong> {{ $item->email }}</p>
                                        <p><strong>GST:</strong> {{ $item->gst }}</p>
                                        <p><strong>Address:</strong> {{ $item->address }}, {{ $item->city }},
                                            {{ $item->state }} - {{ $item->pincode }}</p>
                                        <p>
                                            <strong>Status:</strong>
                                            @if ($item->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </p>
                                        <button class="btn btn-primary btn-sm edit" data-data="{{ @json_encode($item) }}">
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('sales-app/saveElectrician') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add customers</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">
                        <div class="col-md-12">
                            <label for="validationCustom01" class="form-label"> Company Name</label>
                            <input type="text" name="company" id="company" class="form-control">

                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-6 mt-4">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">GST</label>
                            <input type="text" name="gst" id="gst" class="form-control">
                        </div>

                        <div class="col-md-12 mt-4">
                            <label for="">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>


                        <div class="col-md-6 mt-4">
                            <label for="">State</label>
                            <select name="state" id="stateSimple" class="form-control">
                                <option value="">---Select State---</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">City</label>
                            <select name="city" id="citySimple" class="form-control">
                                <option value="">---Select City---</option>
                            </select>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Pincode</label>
                            <input type="number" name="pincode" id="pincode" class="form-control">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="">Active</label>
                            <select name="active" id="active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
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
        $(document).ready(function() {
            $("#searchBox").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                $(".mobile-cards .card").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });

        $(document).on("click", ".edit", function() {
            var data = $(this).data("data")
            $.each(data, function(i, o) {
                $("input[name=" + i + "]").val(o)
                $("select[name=" + i + "]").val(o)
                $("textarea[name=" + i + "]").val(o)

            })
            $("#city").html(`<option value="${data.city}">${data.city}</option>`);
            $("#modal_name").text("Update Architect");
            $("#exampleModal").modal("show");
        });

        $("#add").on("click", function() {
            $("#modal_name").text("Add Architect");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });
    </script>
@endsection
