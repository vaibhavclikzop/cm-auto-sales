@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Return</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Return</h4>
            </div>
            <div class="">


                <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add</button>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Customer</th>
                        <th> Order ID</th>
                        <th> PT ID</th>
                        <th> Return Date</th>
                        <th> Description</th>
                        <th> User</th>


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
                            <td>{{ $item->customer }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ $item->outward_id }}</td>
                            <td>{{ $item->return_date }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->user }}</td>
                            <td>

                                <a class="btn btn-sm btn-primary" href="/sale-return-challan-view/{{ $item->id }}"> <i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveSaleReturn') }}"
                id="frmMain">
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

                        <div class="col-md-4">
                            <label for="">Select Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $item)
                                    <option value="{{ $item->id }}"> {{ $item->company }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Select Invoice</label>
                            <select name="outward_id" id="outward_id" class="form-control">
                                <option value="">Select Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Date</label>
                            <input type="date" name="return_date" id="return_date" class="form-control">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="">Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-4">
                            <label>Product</label> <br>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Qty</label>
                            <input type="number" name="qty" id="qty" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="">Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="current_stock">Current Stock</option>
                                <option value="scrap">Scrap</option>
                            </select>
                        </div>
                        <div class="col-md-2">

                            <button class="btn btn-primary mt-4" id="addProduct" type="button">Add</button>
                        </div>
                        <div class="col-12">
                            <input type="hidden" id="prod_list" name="prod_list">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Part Code</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="prodList">

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btnSave">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#customer_id").select2({
                dropdownParent: $("#exampleModal")
            });

        })
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#brand_id").val($(this).data("brand_id"));
            $("#modal_name").text("Update Category");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Sale Return");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });

        $("#customer_id").on("change", function() {
            var vendor_id = $(this).val();
            $.ajax({
                url: "/GetOutwardChallan",
                type: "POST",
                data: {
                    id: vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "<option>Select</option>";
                    result.forEach(element => {
                        html +=
                            `<option value='${element.id}'>  ${element.outward_id}</option>`;
                    });
                    $("#outward_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        })

        $("#outward_id").on("change", function() {
            var vendor_id = $(this).val();
            $.ajax({
                url: "/GetOutwardChallanProducts",
                type: "POST",
                data: {
                    id: vendor_id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "<option>Select</option>";
                    result.forEach(element => {
                        html +=
                            `<option value='${element.product_id}' data-part_no="${element.part_no}" data-qty="${element.qty}"> ${element.part_no} (${element.product}) : Qty - ${element.qty}</option>`;
                    });
                    $("#product_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });
        var product_list = [];
        var sno = 1;
        $("#addProduct").on("click", function() {
            var product_id = parseInt($("#product_id").val())
            var product_name = $("#product_id").find(":selected").text()
            var part_no = $("#product_id").find(":selected").data("part_no")
            var qty = parseInt($("#qty").val())
            var type = ($("#type").val())


            if (!product_id || isNaN(product_id)) {
                toastr.error("Select a valid Product");
                return;
            }

            if (!qty || isNaN(qty) || qty <= 0) {
                toastr.error("Enter a valid quantity");
                return;
            }


            if ($("#product_id").find(":selected").data("qty") < qty) {
                toastr.error("Qty can not be more then inward qty ");
                return;
            }



            let existingProduct = product_list.find(product => product.product_id === product_id);
            if (existingProduct) {
                toastr.error("Product already exists");
                return;
            }

            var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td>${part_no}</td>    
                            <td>${product_name}</td>    
                            <td>${qty}</td>    
                            <td>${type}</td>    
                         
                            <td> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

            $("#prodList").append(html)
            product_list.push({
                product_id,
                qty,
                type

            });

        });

        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"))

            $(`.product${id}`).remove();
            product_list = product_list.filter(item => item.product_id !== id);

        });
        $("#btnSave").on("click", function() {
            $('#prod_list').val(JSON.stringify(product_list));
            if (!$("#customer_id").val()) {
                toastr.error("Select Customer");
                return;
            }

            if (!$("#outward_id").val()) {
                toastr.error("Select outward invoice");
                return;
            }

            if (product_list.length === 0) {
                toastr.error("Select at least one product");
                return;
            }


            $('#frmMain').submit()

        })
    </script>
@endsection
