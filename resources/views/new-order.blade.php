@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>New Order </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>New Order</h4>
            </div>

        </div>
        <div class="card-body">
            <form id="frmMain" class="row" method="post" action="{{ route('SaveNewOrder') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="text" name="id" hidden>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Party Type</label>
                        <select name="customer_type" id="customer_type" class="form-control" required>
                            <option value="">--Select --</option>
                            @foreach ($customer_type as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group col-md-3">
                        <label>Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">--Select Customer--</option>

                        </select>
                    </div>



                    <div class="col-md-3 d-none">
                        <label>Expected Delivery Date</label>
                        <input type="date" value="{{ date('Y-m-d') }}" name="delivery_date" id="expected_delivery_date"
                            class="form-control">

                    </div>

                    <div class="col-md-3">
                        <label>City</label>
                        <input type="text" name="city" id="city" class="form-control" required>

                    </div>
                    <div class="col-md-3">
                        <label>Coordinates</label>
                        <input type="text" name="coordinates" id="coordinates" class="form-control">

                    </div>

                    <div class="row mt-3">
                        <!-- Billing Address -->
                        <div class="col-md-12">
                            <div style="display: flex; justify-content: space-between">
                                <div>
                                    <h5 class="mb-1"><strong>Billing Address</strong></h5>
                                </div>
                                <div class="d-flex">
                                    {{-- <div>
                                        <input type="checkbox" id="viewShipping">
                                        <label for="viewShipping" class="ms-1">
                                            <h5> View Shipping </h5>
                                        </label>
                                    </div> --}}
                                    <div class="mx-4">

                                        <input type="checkbox" id="sameAsBilling">
                                        <label for="sameAsBilling" class="ms-1">
                                            <h5> Same as Billing </h5>
                                        </label>
                                    </div>
                                </div>

                            </div>


                            <div class="row">

                                <div class="form-group col-3 mb-2">
                                    <label>Address</label>
                                    <input type="text" id="bill_address" name="bill_address" class="form-control">
                                </div>
                                <div class="form-group col-3 mb-2">
                                    <label>State</label>
                                    <select type="text" id="bill_state" name="bill_state" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($state as $item)
                                            <option value="{{ $item->state }}"> {{ $item->state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-3 mb-2">
                                    <label>City <span id="bill_city_status" class="text-success"></span> </label>
                                    <select type="text" id="bill_city" name="bill_city" class="form-control">
                                        <option value="">City</option>
                                    </select>
                                </div>

                                <div class="form-group col-3 mb-2">
                                    <label>Pincode</label>
                                    <input type="text" id="bill_pincode" name="bill_pincode" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="col-md-12 viewShippingDiv">
                            <h5 class="mb-1 d-flex justify-content-between">
                                <strong>Shipping Address</strong>


                            </h5>


                            <div class="row">

                                <div class="form-group col-3 mb-2">
                                    <label>Address</label>
                                    <input type="text" id="ship_address" name="ship_address" class="form-control">
                                </div>

                                <div class="form-group col-3 mb-2">
                                    <label>State</label>
                                    <select type="text" id="ship_state" name="ship_state" class="form-control">
                                        <option value="">State</option>
                                        @foreach ($state as $item)
                                            <option value="{{ $item->state }}"> {{ $item->state }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group col-3 mb-2">
                                    <label>City <span id="ship_city_status" class="text-success"></span></label>
                                    <select id="ship_city" name="ship_city" class="form-control">
                                        <option value="">Select City</option>
                                    </select>
                                </div>

                                <div class="form-group col-3 mb-2">
                                    <label>Pincode</label>
                                    <input type="text" id="ship_pincode" name="ship_pincode" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="col-md-12">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter description" class="form-control" required></textarea>

                    </div>
                </div>
                <div>

                </div>
                <hr>
                <div class="row ">
                    <div class="col-md-4">
                        <label>Upload Requirement File<span class="text-danger"> *Only CSV File*</span></label>
                        <input type="file" id="file">

                    </div>

                    <div class="col-md-4">

                        <button class="btn btn-dark" type="button" id="BtnUpload">Upload</button>

                    </div>
                    <div class="col-md-4">
                        <a href="import-requirement-list.csv" class="btn btn-success btn-sm"
                            download="import-requirement-list.csv">Download sample file</a>

                    </div>

                </div>
                <br>


                <div class="d-flex justify-content-between">
                    <div>
                        <label for=""> Brand</label> <br>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <option value="">--Select Brand --</option>

                            @foreach ($brand as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} </option>
                            @endforeach
                        </select>

                    </div>

                    <div style="width:15%">
                        <label for=""> Part Code <span id="orderCount"></span> </label> <br>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">--Select --</option>
                        </select>

                    </div>
                    <div>
                        <label for="">Product Name <span id="cStock"></span></label> <br>
                        <input type="" name="" id="product_name" class="form-control" disabled>
                    </div>
                    <div>
                        <label for="">Price <span id="product_discount"></span></label> <br>
                        <input type="" name="price" id="price" placeholder="Enter Price"
                            class="form-control" required disabled>
                    </div>
                    <div style="width: 15%">
                        <label for=""> Max. Dis% <span id="spcDiscount"></span></label>
                        <input type="number" step="0.01" id="discount" name="discount" class="form-control">
                    </div>
                    <div style="width: 10%">
                        <label for=""> Qty</label> <span id="pQty" class="text-danger"></span> <br>
                        <input type="number" name="qty" id="qty" placeholder="Enter Quantity"
                            class="form-control" required>
                    </div>

                    <div>

                        <button type="button" id="btnAddItem" onclick="addItem()"
                            class="btn  btn-success mt-3">ADD</button>
                    </div>
                </div>

                <table class="table">
                    <thead style="color: black;">

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

                <input type="hidden" name="prod_list" id="prod_list" value="">

                <div class="text-center col-md-12 mt-3">

                    <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>

        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("select").select2();
        })

        $("#customer_type").on("change", function() {
            $.ajax({
                url: "/getCustomer",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Customer----</option>';
                    result.forEach(element => {
                        let status = "";
                        if (element.active == 0) {

                            status = "disabled";
                        }

                        html += '<option value="' + element.id + '" data-city="' + element
                            .city1 + '"  data-coordinates="' + element.coordinates + '" ' +
                            status + ' data-address="' + element.address + '" data-state="' +
                            element.state + '" data-city="' + element.city +
                            '" data-pincode="' + element.pincode + '"   data-city1="' + element
                            .city1 + '"  data-ship_address="' + element.ship_address +
                            '"   data-ship_district="' + element.ship_district +
                            '"   data-ship_city="' + element.ship_city +
                            '"   data-ship_pincode="' + element.ship_pincode +
                            '" data-ship_state="' + element.ship_state + '"> ' +
                            element.party_code + ',  ' + element.company +
                            '</option>';
                    });
                    $("#customer_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });


        $("#customer_id").on("change", function() {
            $("#city").val($(this).find(":selected").data("city"))
            $("#coordinates").val($(this).find(":selected").data("coordinates"))
            $("#bill_state").val($(this).find(":selected").data("state"));
            $("#bill_address").val($(this).find(":selected").data("address"));
            if ($("#bill_state").val() != null) {
                $("#bill_state").trigger($("#bill_state").val()).change();
            }


            $("#ship_address").val($(this).find(":selected").data("ship_address"));
            $("#ship_state").val($(this).find(":selected").data("ship_state"));

            if ($("#ship_state").val() != null) {
                $("#ship_state").trigger($("#ship_state").val()).change();

            }

            $("#bill_city_status").text("Fetching....")


            $("#bill_pincode").val($(this).find(":selected").data("pincode"));
            $("#ship_pincode").val($(this).find(":selected").data("ship_pincode"));

            $(document).ajaxStop(function() {

                $("#bill_city").val($("#customer_id").find(":selected").data("city"));
                $("#ship_city").val($("#customer_id").find(":selected").data("ship_district"));
                $("#bill_city").trigger($("#city").val()).change();
                $("#bill_city_status").text("")
            })
        })


        $("#sameAsBilling").on("change", function() {
            if ($(this).is(":checked")) {

                $("#ship_address").val($("#bill_address").val());

                $("#ship_state").val($("#bill_state").val());
                $("#ship_state").trigger($("#bill_state").val()).change();
                $("#ship_city_status").text("Fetching...")
                setTimeout(() => {
                    let ship_city = $("#bill_city").val();
                    $("#ship_city").html('<option value="' + ship_city + '">' + ship_city + '</option>');
                    $("#ship_city").val(ship_city);
                    $("#ship_city_status").text("")
                }, 300)

                $("#ship_pincode").val($("#bill_pincode").val());

            } else {
                $("#ship_address").val("");
                $("#ship_city").val("");
                $("#ship_state").val("");
                $("#ship_pincode").val("");
            }
        });

        $("#bill_state").on("change", function() {
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
                    $("#bill_city").html(html)

                },
                error: function(result) {
                    console.log(result);
                }
            });

        })

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
                    html += '<option value="">----Select City----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.city + '">' + element.city +
                            '</option>';
                    });
                    $("#ship_city").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });

        $("#brand_id").on("change", function() {
            var customer_type = $("#customer_type").val();
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

                        html += '<option value="' + element.id + '">' + element.name +
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
                            element.brand + '" data-order_count="' + element.order_count +
                            '" data-product_discount="' + element.discount + '">' +
                            element.part_no + ' (' + element.name +
                            ')</option>';
                    });
                    $("#product_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
        var maxDiscount = 0;
        var order_count = 1;
        var productDiscount = 0;
        $("#product_id").on("change", function() {

           let customer_id= $("#customer_id").val();
           if (!customer_id) {
            toastr.error("Select Customer");
            return;
           }
            $("#price").val($(this).find(":selected").data("price"))
            $("#product_name").val($(this).find(":selected").data("name"))
            $("#product_discount").text("Item Disc % " + $(this).find(":selected").data("product_discount"))
            order_count = $(this).find(":selected").data("order_count");
            productDiscount = $(this).find(":selected").data("product_discount");
            $("#orderCount").text("Qty  Count : " + order_count)
            $.ajax({
                url: "/getSpecialOffer",
                type: "POST",
                data: {
                    id: $(this).val(),
                    customer_id: customer_id,

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    maxDiscount = result.specialOffer;

                    $("#spcDiscount").text(result.specialOffer)
                    $("#pQty").text(" P. Qty." + result.pending_qty)
                    $("#cStock").text(" CS. " + result.cs)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        });
        $("#btnAddItem").attr("disabled", "disabled")
        $("#qty").on("keyup", function() {

            let qty = parseInt($(this).val())
            let check = qty % order_count;
            if (check == 0) {
                $("#btnAddItem").removeAttr("disabled")
            } else {
                $("#btnAddItem").attr("disabled", "disabled")
            }

        })


        $("#discount").on("keyup", function() {
            let discount = parseFloat($(this).val());
            maxDiscount = parseFloat(maxDiscount);
            productDiscount = parseFloat(productDiscount);
            if (productDiscount > maxDiscount) {
                maxDiscount=productDiscount;
            }

            if (discount > maxDiscount) {
                toastr.error("Discount can not be more then max discount")
                $(this).val(maxDiscount)
            }
        })


        var products = [];
        var row;
        let sno = 1;

        function addItem() {
            var id = $('#product_id').val()
            var prod_name = $('#product_id option:selected').data("name")
            var qty = $('#qty').val()
            var product_code = $("#product_id").find(':selected').data('article_code')
            var price = $("#price").val()
            var discount = $("#discount").val()
            var brand = $("#product_id").find(':selected').data('brand')


            if (qty <= 0) {
                alert('Qty should be more than zero.');
                return;
            }
            if (id <= 0) {
                alert('Select product.');
                return;
            }

            var ex_p = products.filter((item, index) => item.sno == sno)

            if (products[sno] != undefined) {
                alert('This product already added.');
                return;
            }
            row = `
            <tr class="prod${sno}">
 
                <td>${brand}</td>
                <td>${prod_name}</td>
                <td>${product_code}</td>
                <td>${price}</td>
                <td>${discount}</td>
                <td>${qty}</td>
                <td>${price*qty-(price*qty/100*discount)}</td>
                <td><button onclick="removeItem(${sno})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
            </tr>
        `;

            $('#productList').append(row);
            //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
            products.push({
                sno,
                id,
                qty,
                price,
                discount,
                order_det_id: 0

            });
            sno++;
            console.log(products);

            $('#product_id').val('')
            $('#qty').val('')
            $('#price').val('')
            $('#discount').val(0)
        }

        function removeItem(id) {
            $(`.prod${id}`).remove();
            products = products.filter(item => item.sno !== id);
            console.log(products);
        }

        function saveBill() {
            $('#prod_list').val(JSON.stringify(products));



            $('#frmMain').submit()
        }


        $("#BtnUpload").on("click", function() {

            let fileInput = document.getElementById('file');
            let file = fileInput.files[0];
            if (file) {
                // Create a new FormData object
                let formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: "/UploadRequirementList",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        data = JSON.parse(result)
                        data.data.forEach(element => {
                            row += `
                            <tr class="prod${element.id}">
                            <td>${element.brand_name}</td>
                            <td>${element.name}</td>
                           
                                <td>${element.part_no}</td>
                                <td>${element.final_price}</td>
                                <td>${element.discount}</td>
                                <td>${element.qty}</td>
                                <td>${element.final_price*element.qty-(element.final_price*element.qty/100*element.discount)}</td>
                                <td><button onclick="removeItem(${element.id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            </tr>
                        `;

                            let id = element.id;
                            let qty = element.qty;
                            let price = element.final_price;
                            let discount = element.discount;
                            products.push({
                                sno,
                                id,
                                qty,
                                price,
                                discount,
                                order_det_id: 0

                            });
                            sno++;
                        });

                        $('#productList').append(row);


                    },
                    error: function(data) {
                        console.log(data);

                    }
                });
            } else {
                toastr.error("Select CSV file for upload");
            }

        });

        $(document).ready(function() {

            let order_mst = @json($order_mst);
            let order_det = @json($order_det);
            if (order_mst) {


                $.each(order_mst, function(i, o) {
                    $("input[name=" + i + "]").val(o)
                    $("select[name=" + i + "]").val(o)
                    $("textarea[name=" + i + "]").val(o)
                });
                $("#customer_type").trigger("change")

                $("#ship_state").trigger("change");

                setTimeout(() => {
                    $(document).ajaxStop(function() {
                        $("#bill_city").val(order_mst.bill_city);
                        $("#ship_city").val(order_mst.ship_city);
                        $("#ship_city").trigger("change");

                    });

                }, 2000);

                setTimeout(() => {
                    $("#customer_id").val(order_mst.customer_id)
                    $("#customer_id").trigger("change")
                    setTimeout(() => {
                        $("#order_id").val(order_mst.order_id)
                        $("#order_id").trigger("change")
                        $("#warehouse_id").trigger("change")
                        setTimeout(() => {
                            $("#location_id").val(order_mst.location_id)
                            $("#location_id").trigger("change")
                            $("#ship_city").val(order_mst.ship_city);
                        }, 1000);

                    }, 1000);


                }, 1000);




                order_det.forEach(element => {
                    row += `
                            <tr class="prod${sno}">
                            <td>${element.brand_name}</td>
                            <td>${element.name}</td>
                           
                                <td>${element.part_no}</td>
                                <td>${element.price}</td>
                                <td>${element.discount}</td>
                                <td>${element.qty}</td>
                         <td>${(element.price * element.qty -
                                    (element.price * element.qty * element.discount / 100)
                                ).toFixed(2)}</td>
                                <td><button onclick="removeItem(${sno})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            </tr>
                        `;

                    let id = element.id;
                    let qty = element.qty;
                    let price = element.price;
                    let discount = element.discount;
                    let order_det_id = element.order_det_id;
                    products.push({
                        sno,
                        id,
                        qty,
                        price,
                        discount,
                        order_det_id

                    });
                    sno++;

                });
                console.log(products);
                $("#productList").html(row)



            }
        })
    </script>
@endsection
