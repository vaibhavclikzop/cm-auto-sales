@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Customer List</h4>
            </div>
            <div class="">

                @if ($rolePermissions->where('permission_name', 'customers')->where('view', 1)->where('create', 1)->isNotEmpty())
                    <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add Customer</button>

                    <!-- Modal trigger button -->
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#importCustomer">
                        Import Customer
                    </button>
                @endif

            </div>
        </div>
        <div class="card-body">
            {!! session('msg') !!}
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Party Code</th>
                        <th>Party Type</th>

                        <th>Party Name</th>
                        <th>Name</th>
                        <th>Number</th>

                        <th>Email</th>
                        <th>GST</th>
                        <th>Address</th>

                        <th>City</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Pincode</th>
                        <th>Active</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($customers as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->party_code }}</td>
                            <td>{{ $item->customerType->name }}</td>

                            <td
                                style="  
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
     ">
                                {{ $item->company }}</td>
                            <td
                                style="  
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
     ">
                                {{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->gst }}</td>
                            <td>
                                <span
                                    style="        width: 250px;
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    display: inline-flex;
">
                                    {{ $item->address }}</span>
                            </td>


                            <td>{{ $item->city1 }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->state }}</td>

                            <td>{{ $item->pincode }}</td>
                            @if ($item->active == 1)
                                <td><span class="badge badge-success">Active</span></td>
                            @else
                                <td><span class="badge badge-danger">InActive</span></td>
                            @endif

                            <td>
                                @if ($rolePermissions->where('permission_name', 'customers')->where('view', 1)->where('edit', 1)->isNotEmpty())
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
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveCustomer') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add customers</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">
                        <div class="col-md-4">
                            <label for="validationCustom01" class="form-label"> Party Type</label>
                            <select name="customer_type_id" id="customer_type_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($customer_type as $item)
                                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="validationCustom01" class="form-label"> Party Name</label>
                            <input class="form-control" id="company" name="company" required>


                        </div>




                        <div class="col-md-4">
                            <label for="" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>

                        </div>
                        <div class="col-md-4 mt-2">
                            <label for="">Number</label>
                            <input type="number" name="number" id="number" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-2">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-4 mt-2">
                            <label for="">GST</label>
                            <input type="text" name="gst" id="gst" class="form-control">
                        </div>



                        <div class="col-md-12 mt-2">
                            <strong>Billing Address</strong> <br>
                            <label for="">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>


                        <div class="col-md-3 mt-2">
                            <label for="">State</label>
                            <select name="state" id="state" class="form-control">
                                <option value="">---Select State---</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label for="">District</label>
                            <select name="city" id="city" class="form-control">
                                <option value="">---Select District---</option>
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label for="">City</label>
                            <input type="text" name="city1" id="city1" class="form-control">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label for="">Pincode</label>
                            <input type="number" name="pincode" id="pincode" class="form-control">
                        </div>


                        <div class="col-md-12 mt-2">
                            <div style="display: flex; justify-content: space-between">
                                <div>
                                    <strong>Shipping Address</strong>
                                </div>
                                <div>
                                    <input type="checkbox" id="sameBilling"> <label for="sameBilling"> Same as
                                        Billing</label>
                                </div>
                            </div>

                            <label for="">Address</label>
                            <textarea name="ship_address" id="ship_address" class="form-control"></textarea>
                        </div>


                        <div class="col-md-3 mt-2">
                            <label for="">State</label>
                            <select name="ship_state" id="ship_state" class="form-control">
                                <option value="">---Select State---</option>
                                @foreach ($state as $item)
                                    <option value="{{ $item->state }}">{{ $item->state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label for="">District</label>
                            <select name="ship_district" id="ship_district" class="form-control">
                                <option value="">---Select District---</option>
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label for="">City</label>
                            <input type="text" name="ship_city" id="ship_city" class="form-control">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label for="">Pincode</label>
                            <input type="number" name="ship_pincode" id="ship_pincode" class="form-control">
                        </div>
                        <div class="col-md-4 mt-2">
                            <label for="">Map Coordinates</label>
                            <input type="" name="coordinates" id="coordinates" class="form-control">
                        </div>

                        <div class="col-md-4 mt-2">
                            <label for="">Party Code</label>
                            <input type="" name="party_code" id="party_code" class="form-control" required>
                        </div>

                        <div class="col-md-4 mt-2">
                            <label for="">Active</label>
                            <select name="active" id="active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="">DSR</label>
                            <input type="text" name="dsr" id="dsr" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2">
                            <label for="">Manager</label>
                            <select name="manager_id" id="manager_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                @endforeach
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

    <form action="{{ route('supplier/ImportCustomers') }}" method="post" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="importCustomer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Import Customers
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <div>
                                <a href="/import-customers.csv" download="/import-customers.csv"
                                    class="btn btn-dark">Download Sample</a>
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
        function camelcase(str) {
            return str
                .toLowerCase()
                .replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
        }

        $(document).on("click", ".edit", function() {


            var data = $(this).data("data");
            $.each(data, function(key, value) {
                $("input[name='" + key + "']").val(value)
                $("select[name='" + key + "']").val(value)
                $("textarea[name='" + key + "']").val(value)
            })
    
            $("#state").val(camelcase(data.state))
            $("#ship_state").val(camelcase(data.ship_state))
            $("#city").html(`<option value="${data.city}">${data.city}</option>`)
            $("#ship_district").html(`<option value="${data.ship_district}">${data.ship_district}</option>`)
            $("#modal_name").text("Update customers");


            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add customers");



            $("#id").val("");

            $("#exampleModal").modal("show");
        });
        $(".reference").hide();
        $("#source").on("change", function() {
            if ($(this).val() == "Reference") {
                $(".reference").show(500);
            } else {
                $(".reference").hide(500);
            }
        });


        $("#state").on("change", function() {
            $.ajax({
                url: "/GetCity",
                type: "POST",
                data: {
                    state: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select City----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.city + '">' + element.city +
                            '</option>';
                    });
                    $("#city").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });



        $("#ship_state").on("change", function() {
            $.ajax({
                url: "/GetCity",
                type: "POST",
                data: {
                    state: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select District----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.city + '">' + element.city +
                            '</option>';
                    });
                    $("#ship_district").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });


        $("#sameBilling").on("click", function() {
            if ($(this).prop("checked")) {
                let district = $("#city1").val();
                $("#ship_address").val($("#address").val())
                $("#ship_state").val($("#state").val())
                $("#ship_district").html(`<option value="${district}">${district}</option>`)
                $("#ship_city").val($("#city1").val())
                $("#ship_pincode").val($("#pincode").val())
            } else {
                let district = "---Select District---";
                $("#ship_address").val("")
                $("#ship_state").val("")
                $("#ship_district").html(`<option value="">${district}</option>`)
                $("#ship_city").val("")
                $("#ship_pincode").val("")

            }
        })
    </script>
@endsection
