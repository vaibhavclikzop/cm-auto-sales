@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Raise pick ticket</h4>
            </div>


        </div>
        <div class="card-body">
            <form action="{{ route('SaveOutward') }}" id="formMain" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="row">
                    <input type="text" name="id" hidden>
                    <div class="col-md-3">
                        <label for="form-label"> Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->company }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label for="form-label"> Select Order</label>
                        <select name="order_id" id="order_id" class="form-control" required>
                            <option value="">Select Order</option>
                        </select>

                    </div>
                    <div class="col-md-2">
                        <label for="form-label"> Invoice Date</label>
                        <input type="date" name="invoice_date" id="invoice_date" value="{{ date('Y-m-d') }}"
                            class="form-control" required>


                    </div>
                    <div class="col-md-2">
                        <label for="">Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($warehouse as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Location</label>
                        <select name="location_id" id="location_id" class="form-control" required>
                            <option value="">Select</option>

                        </select>
                    </div>
                    <div class="col-md-3 d-none">
                        <label for="form-label"> jon no</label>
                        <input type="" name="jon_no" id="jon_no" class="form-control" required>


                    </div>

                    <div class="col-md-2 mt-1 d-none">
                        <label for="form-label">Additional Discount</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control"
                            value="0">


                    </div>

                    <div class="col-md-2 mt-1">
                        <label for="form-label"> Discount Type</label>
                        <select name="discount_type" id="discount_type">
                            <option value="price" selected>Price</option>
                            <option value="discount">Discount</option>
                        </select>



                    </div>


                    <div class="col-md-10 mt-1">
                        <label for="form-label"> Description</label>
                        <input type="" name="description" id="description" class="form-control">


                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Part Code</th>
                                    <th>Actual Qty</th>
                                    <th>Out Qty</th>
                                    <th>Current Stock</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productList">

                            </tbody>

                        </table>
                        <input type="hidden" id="prod_list" name="prod_list">
                    </div>

                </div>
                <div class="text-center col-md-12 mt-3">

                    <button type="button" id="Save" class="btn btn-warning">Submit</button>

                </div>
            </form>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            $("select").select2()

            var customer_id = "{{ request('customer_id') }}"
            console.log(customer_id);
            $("#customer_id").val(customer_id)
            $("#customer_id").trigger("change")




        })


        $("#customer_id").on("change", function() {

            $.ajax({
                url: "/GetCustomerOrder",
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
                    let order_id = "{{ request('order_id') }}"
                    $("#order_id").val(order_id)
                    $("#order_id").trigger("change")
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });

        var products = [];
        var row;
        $("#location_id").on("change", function() {

            let out_id = "{{ request('out_id') }}";
            if (out_id) {
                return;
            }
            let id = $("#order_id").val();
            $.ajax({
                url: "/GetOrderDet",
                type: "POST",
                data: {
                    location_id: $(this).val(),
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {
                    var html = "";
                    var sno = 1;
                    row = "";
                    products = [];
                    let bgColor = "#edffe8";
                    result.forEach(element => {


                        let order_det_id = element.id;
                        var id = sno;
                        var product_id = element.product_id;

                        var price = element.price
                        var qty = element.qty - element.out_qty

                        if (element.stock < qty) {
                            qty = element.stock

                            bgColor = "#fffae8";
                        }

                        if (element.stock == 0) {
                            bgColor = "#ffe8e8";
                        }
                        row += `
                            <tr class="prod${id}" style="background:${bgColor}">
                                <td style="background:${bgColor}">${sno}</td>
                            <td style="background:${bgColor}">${element.brand}</td>
                           <td style="background:${bgColor}; white-space: normal; word-break: break-word;">
                            ${element.product}
                            </td>

                            <td style="background:${bgColor}">${element.article_no}</td>
                            <td style="background:${bgColor}">${element.qty}</td>
                            <td style="background:${bgColor}">${element.out_qty}</td>
                            <td style="background:${bgColor}">${element.stock}</td>
                            <td style="background:${bgColor}"><input type="number" class="  qty" data-product_id="${product_id}" 
                                                            data-received_qty="${element.out_qty}" 
                                                            data-actual_qty="${element.qty}" 
                                                               data-stock="${element.stock}" 
                                                            data-id="${id}"  value="${qty}"></td>  
                                <td style="background:${bgColor}"><button onclick="removeItem(${id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                </tr>`;
                        if (qty > 0) {


                            products.push({
                                id,
                                product_id,
                                qty,
                                price,
                                order_det_id

                            });

                        }
                        sno++;
                    });
                    console.log(products);
                    $("#productList").html(row)

                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });

        });


        $("#warehouse_id").on("change", function() {

            $.ajax({
                url: "/getLocation",
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
                    html += '<option value="">----Select Location----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '" >' + element
                            .name +
                            '</option>';
                    });
                    $("#location_id").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });

        function removeItem(id) {
            $(`.prod${id}`).remove();
            products = products.filter(item => item.id !== id);
            console.log(products);
        }
        $(document).on("keyup", '.qty', function() {
            var product_id = parseInt($(this).data("id"))

            var qty = parseInt($(this).val());
            var received_qty = parseInt($(this).data("received_qty"))
            var actual_qty = parseInt($(this).data("actual_qty"))
            var stock = parseInt($(this).data("stock"))
            var remaining_qty = actual_qty - received_qty;
            if (stock < remaining_qty) {
                toastr.error("Received qty can not be more then remaining qty");
                $(this).val(stock)
                return;
            }

            console.log(remaining_qty)
            if (qty > remaining_qty) {
                toastr.error("Received qty can not be more then remaining qty");
                $(this).val(remaining_qty)
                return;
            }

            var product = products.find(item => item.id === product_id);

            if (product) {

                product.qty = qty;
                console.log("Updated Product List:", products);
            } else {
                toastr.error("Something went wrong");
                return;
            }
        })
        $("#Save").on("click", function() {
            event.preventDefault();

            $('#prod_list').val(JSON.stringify(products));
            if (!$("#customer_id").val()) {
                toastr.error("Select Customer");
                return;
            }

            if (!$("#order_id").val()) {
                toastr.error("Select Order");
                return;
            }

            if (products.length === 0) {
                toastr.error("Select at least one product");
                return;
            }
            $("#formMain").submit()

        });

        $(document).ready(function() {

            let order_mst = @json($outward_mst);
            let order_det = @json($outward_det);
            if (order_mst) {


                $.each(order_mst, function(i, o) {
                    $("input[name=" + i + "]").val(o)
                    $("select[name=" + i + "]").val(o)
                    $("textarea[name=" + i + "]").val(o)
                });
                $("#customer_id").trigger("change")
                $("#discount_type").trigger("change")
                setTimeout(() => {
                    $("#order_id").val(order_mst.order_id)
                    $("#order_id").trigger("change")
                    $("#warehouse_id").trigger("change")
                    setTimeout(() => {
                        $("#location_id").val(order_mst.location_id)
                        $("#location_id").trigger("change")
                    }, 1000);

                }, 1000);



                let sno = 1;
                order_det.forEach(element => {
                    var order_det_id = element.order_det_id;
                    var id = sno;
                    var product_id = element.product_id;

                    var price = element.price
                    var qty = element.qty - element.out_qty
                    row += `
                            <tr class="prod${id}">
                                <td>${id}</td>
                            <td>${element.brand}</td>
                                        <td style="
                            white-space: normal;
                            overflow-wrap: anywhere;
                        ">
                        ${element.product}
                        </td>

                            <td>${element.article_no}</td>
                            <td>${element.qty}</td>
                            <td>${element.out_qty}</td>
                            <td>${element.stock}</td>
                            <td><input type="number" class="form-control qty" data-product_id="${product_id}" 
                                                            data-received_qty="0" 
                                                            data-actual_qty="${element.qty}" 
                                                            data-id="${id}"  value="${element.outward_qty}"></td>  
                                <td><button onclick="removeItem(${id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                </tr>`;

                    products.push({
                        id,
                        product_id,
                        qty: element.outward_qty,
                        price,
                        order_det_id

                    });

                    sno++
                });
                console.log(products);
                $("#productList").html(row)



            }
        })
    </script>
@endsection
