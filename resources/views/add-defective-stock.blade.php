@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Add Defective Stock</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Add Defective Stock</h4>
            </div>

        </div>
        <div class="card-body">
            <form id="frmMain" method="POST" action="{{ route('SaveDefectiveStock') }}">
                @csrf
                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>

                                    <th>
                                        <label for="">Locations</label>
                                        <select name="location_id" id="location_id" class="form-control">
                                            <option value="">Select Location</option>
                                            @foreach ($location as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach

                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Products</label>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>

                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Current stock</label>
                                        <input type="number" id="current_stock" value="0" class="form-control"
                                            disabled>
                                    </th>
                                    <th>
                                        <label for="">Defective stock</label>
                                        <input type="number" name="qty" id="qty" min="1" value="1"
                                            class="form-control" placeholder="Enter Qty">
                                    </th>

                                    <th>
                                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product Name</th>
                                    <th>Article No.</th>
                                    <th>Defective Stock</th>


                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list" value="">
                    </div>

                    <div class="text-center col-md-12 mt-3">

                        <button type="button" id="SaveDefectiveStock" name="btnSubmit"
                            class="btn btn-warning">Submit</button>

                    </div>
                </div>
            </form>

        </div>

    </div>


    <script>
        $(document).ready(function() {
            var product_list = [];
            var sno = 1;
            $("#location_id").on("change", function() {
                var id = $(this).val()
                $.ajax({
                    url: "/GetCurrentStock",
                    type: "POST",
                    data: {
                        location_id: id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        var products = "<option>---Select Products---</option>";
                        result.forEach(element => {
                            products +=
                                `<option value="${element.product_id}" data-qty="${element.stock}" data-article_no="${element.article_no}">${element.product_name}</option>`;

                        });

                        $("#product_id").html(products)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });
            var current_stock = 0;

            $("#product_id").on("change", function() {

                current_stock = $(this).find(":selected").data("qty");

                $("#current_stock").val(current_stock)

            });



            $("#addProduct").on("click", function() {
                var product_id = parseInt($("#product_id").val())
                var location_id = parseInt($("#location_id").val())
                var product_name = $("#product_id").find(":selected").text()
                var article_no = $("#product_id").find(":selected").data("article_no")
                var qty = parseInt($("#qty").val())


                if (!product_id || isNaN(product_id)) {
                    toastr.error("Select a valid Product");
                    return;
                }

                if (!qty || isNaN(qty) || qty <= 0) {
                    toastr.error("Enter a valid quantity");
                    return;
                }
                if (qty > current_stock) {
                    toastr.error("Qty can not be more then current stock");
                    return;
                }


                let existingProduct = product_list.find(product => product.product_id === product_id);
                if (existingProduct) {
                    toastr.error("Product already exists");
                    return;
                }

                var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td>${product_name}</td>    
                            <td>${article_no}</td>    
                            <td>${qty}</td>    
                           
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
                    location_id
                });

            });

            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.product_id !== id);

            });

            $("#SaveDefectiveStock").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));


                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }

                $("#SaveDefectiveStock").attr("disabled", "disabled")
                 $('#frmMain').submit()

            });

        });
    </script>
@endsection
