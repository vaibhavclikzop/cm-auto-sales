@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Merge Order</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Merge Order</h4>
            </div>

        </div>
        <div class="card-body">
            <form action="{{ route('saveMergeOrder') }}" id="frmMain" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row">
                    <div class="col-3">
                        <label for="">Customers</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}" data-customer_type_id="{{ $item->customer_type_id }}">
                                    {{ $item->company }} </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-3">
                        <label for="">Pending Orders</label>
                        <select name="order_ids[]" id="order_id" class="form-control" required multiple>
                            <option value="">Select</option>

                        </select>
                    </div>
                    <div class="col-2">
                        <label for="">Order Date</label>
                        <input type="date" value="{{ date('Y-m-d') }}" name="order_date" id="order_date"
                            class="form-control">
                    </div>
                    <div class="col-4">
                        <label for="">Description</label>
                        <input type="" name="description" id="description" class="form-control">
                    </div>

                </div>
                <div class="row mt-2">
                    <div class="d-flex ">
                        <div class="">
                            <label for=""> Brand</label> <br>
                            <select name="brand_id" id="brand_id" class="form-control">
                                <option value="">--Select Brand --</option>

                                @foreach ($brand as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="mx-1" style="width: 270px">
                            <label for=""> Part Code</label> <br>
                            <select name="product_id" id="product_id" class="form-control" required>
                                <option value="">--Select --</option>
                            </select>

                        </div>
                        <div class="">
                            <label for="">Product Name <span id="cStock"></span></label> <br>
                            <input type="" name="" id="product_name" class="form-control" disabled>
                        </div>
                        <div class=" mx-1">
                            <label for="">Price</label> <br>
                            <input type="number" name="price" id="price" placeholder="Enter Price"
                                class="form-control" required disabled>
                        </div>
                        <div class="">
                            <label for=""> Spec. Dis.% <span id="spcDiscount"></span></label>
                            <input type="number" step="0.01" id="discount" name="discount" class="form-control">
                        </div>
                        <div class=" mx-1">
                            <label for=""> Qty</label> <span id="pQty" class="text-danger"></span> <br>
                            <input type="number" name="qty" id="qty" placeholder="Enter Quantity"
                                class="form-control" required>
                        </div>

                        <div class="mt-1">

                            <button type="button" onclick="addItem()" class="btn  btn-success mt-3">ADD</button>
                        </div>
                    </div>

                    <div class="col-12 mt-3">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Part Code</th>
                                    <th>Price </th>
                                    <th>Discount %</th>
                                    <th>Qty. </th>
                                    <th>Total</th>


                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productList">


                            </tbody>

                        </table>

                    </div>

                    <input type="hidden" name="prod_list" id="prod_list" value="">

                    <div class="text-center col-md-12 mt-3">

                        <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>

                    </div>
                </div>
            </form>


        </div>
    </div>
    <script>
        $(document).ready(function() {


            $("#customer_id, #order_id, #product_id").select2();
        });

        $("#customer_id").on("change", function() {
            products = [];
            row = "";
            $('#productList').html("");
            $.ajax({
                url: "/getPendingOrder",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Order----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '" >' + element
                            .order_id +
                            '</option>';
                    });
                    $("#order_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });


        var maxDiscount = 0;




        var products = [];
        var row;

        $("#brand_id").on("change", function() {
            var customer_type = $("#customer_id").find(":selected").data("customer_type_id");
            var brand_id = $("#brand_id").val();
            if (!customer_type) {
                toastr.error("Choose Customer First");
                $(this).val("")
                return;
            }
            getProduct(customer_type, brand_id)

        })


        $("#category_id").on("change", function() {
            $.ajax({
                url: "/GetSubCategory",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Sub Category----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element
                            .name +
                            '</option>';
                    });
                    $("#sub_category_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        })

        $("#customer_type").on("change", function() {

            var customer_type = $("#customer_type").val();
            var brand_id = $("#brand_id").val();
            if (!customer_type) {
                toastr.error("Choose Customer First");
                $(this).val("")
                return;
            }
            getProduct(customer_type, brand_id)

        });





        function getProduct(customer_type, brand_id) {
            $.ajax({
                url: "/GetProducts",
                type: "POST",
                data: {

                    customer_type: customer_type,
                    brand_id: brand_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#product_id").html(
                        '<option value="">---- Searching products... ----</option>'
                    );
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Product----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '" data-article_code="' +
                            element.part_no + '"  data-name="' +
                            element.name + '" data-price="' +
                            element.final_price + '" data-brand="' +
                            element.brand + '">' + element.part_no + ' (' + element.name +
                            ')</option>';
                    });
                    $("#product_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }

        $("#product_id").on("change", function() {
            $("#price").val($(this).find(":selected").data("price"))
            $("#product_name").val($(this).find(":selected").data("name"))

            $.ajax({
                url: "/getSpecialOffer",
                type: "POST",
                data: {
                    id: $(this).val(),

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    maxDiscount = result.specialOffer;

                    $("#spcDiscount").text("Max Dis. " + result.specialOffer)
                    $("#pQty").text(" P. Qty." + result.pending_qty)
                    $("#cStock").text(" CS. " + result.cs)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });




        $("#discount").on("keyup", function() {
            let discount = parseFloat($(this).val());
            maxDiscount = parseFloat(maxDiscount);

            if (discount > maxDiscount) {
                toastr.error("Discount can not be more then max discount")
                $(this).val(maxDiscount)
            }
        })




        $("#order_id").on("change", function() {
            products = [];
            row = "";
            $('#productList').html("");
            $.ajax({
                url: "/getPendingOrderDetails",
                type: "POST",
                data: {
                    order_id: $(this).val(),

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    result.forEach(element => {
                        addProduct(element.product_id, element.name, element.qty,
                            element
                            .part_no, element.price, element.discount, element
                            .brand);
                    });
                },
                error: function(result) {
                    console.log(result);
                }
            });
        })

        function addItem() {
            var id = parseInt($('#product_id').val())
            var prod_name = $('#product_id option:selected').data("name")
            var qty = $('#qty').val()
            var product_code = $("#product_id").find(':selected').data('article_code')
            var price = $("#price").val()
            var discount = $("#discount").val()
            var brand = $("#product_id").find(':selected').data('brand')
            addProduct(id, prod_name, qty, product_code, price, discount, brand);


        }

        function addProduct(id, prod_name, qty, product_code, price, discount, brand) {
            if (qty <= 0) {
                alert('Qty should be more than zero.');
                return;
            }
            if (id <= 0) {
                alert('Select product.');
                return;
            }

            var ex_p = products.filter((item, index) => item.id == id)

            // if (products[id] != undefined) {
            //     alert('This product already added.');
            //     return;
            // }
            row = `
            <tr class="prod${id}">
 
                <td>${brand}</td>
                <td>${prod_name}</td>
                <td>${product_code}</td>
                <td>${price}</td>
                <td> <input type="number" step="0.01" class="form-control updateDiscount" data-id="${id}" value="${discount}" > </td>
            <td> <input type="number" class="form-control updateQty" data-id="${id}" value="${qty}" > </td>
                <td>${price*qty-(price*qty/100*discount)}</td>
                <td><button onclick="removeItem(${id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
            </tr>`;


            $('#productList').append(row);

            //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
            products.push({
                id,
                qty,
                price,
                discount,
                order_det_id:null

            });
            console.log(products);

            $('#product_id').val('')
            $('#qty').val('')
            $('#price').val('')
            $('#discount').val(0)
        }

        $(document).on("keyup", ".updateDiscount", function() {
            let product_id = $(this).data("id");
            let discount = $(this).val();


            // find product in array
            let product = products.find(p => p.id == product_id);

            if (product) {
                product.discount = discount;
            }

            console.log(products);

        });


        $(document).on("keyup", ".updateQty", function() {
            let product_id = $(this).data("id");
            let qty = $(this).val();


            // find product in array
            let product = products.find(p => p.id == product_id);

            if (product) {
                product.qty = qty;
            }

            console.log(products);

        })

        function removeItem(id) {
            $(`.prod${id}`).remove();
            console.log(id);
            products = products.filter(item => item.id !== id);
            console.log(products);
        }




        function saveBill() {

            let order_id = $("#order_id").val();
            if (order_id == null) {
                toastr.error('Select at least one order');
                return;
            }
            if (products.length === 0) {
                toastr.error('Please select at least one product');
                return false;
            }
            $('#prod_list').val(JSON.stringify(products));
            $('#frmMain').submit()
        }
    </script>
@endsection
