@extends('lead-app.layouts.main')
@section('main-section')
    <style>
        /* ================= MOBILE ORDER FORM UI ================= */
        @media (max-width: 768px) {

            .card {
                border-radius: 18px;
                border: none;
                box-shadow: 0 12px 28px rgba(0, 0, 0, .08);
            }

            .card-header {
                background: linear-gradient(135deg, #6366f1, #3b82f6);
                color: #fff;
                font-weight: 700;
                font-size: 16px;
                border-radius: 18px 18px 0 0;
                padding: 14px;
            }

            .card-body {
                padding: 14px;
            }

            label {
                font-size: 12px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 4px;
            }



            hr {
                margin: 16px 0;
                opacity: .15;
            }

            /* Upload Section */
            .upload-box {
                background: #f8fafc;
                border: 2px dashed #c7d2fe;
                border-radius: 14px;
                padding: 12px;
                text-align: center;
            }

            .upload-box button,
            .upload-box a {
                width: 100%;
                margin-top: 6px;
                border-radius: 12px;
            }

            /* Product Add Card */
            .product-card {
                background: #f9fafb;
                border-radius: 16px;
                padding: 12px;
                margin-top: 12px;
            }

            .btn-success,
            .btn-warning,
            .btn-dark {
                border-radius: 14px;
                height: 44px;
                font-weight: 700;
            }

            /* Product List Table -> Card style */
            table {
                display: none;
            }

            .mobile-products .item {
                background: #fff;
                border-radius: 14px;
                padding: 12px;
                margin-bottom: 10px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
            }

            .mobile-products .item-title {
                font-weight: 700;
                font-size: 14px;
            }

            .mobile-products .meta {
                font-size: 12px;
                color: #6b7280;
            }

            .mobile-products .price {
                font-weight: 800;
                color: #2563eb;
                font-size: 15px;
            }

            /* Sticky submit */
            .sticky-submit {
                position: sticky;
                bottom: 0;
                background: #fff;
                padding: 10px;
                margin-top: 14px;
                box-shadow: 0 -6px 16px rgba(0, 0, 0, .1);
                border-radius: 16px 16px 0 0;
            }
        }
    </style>

    <div class="card">
        <div class="card-header">
            Add Order
        </div>
        <div class="card-body">

            <form id="frmMain" class="row" method="post" action="{{ route('lead-app/SaveNewOrder') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="row ">
                    <div class="form-group col-6">
                        <label>Party Type</label>
                        <select name="customer_type" id="customer_type" class="form-control" required>
                            <option value="">--Select --</option>
                            @foreach ($customer_type as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group col-6">
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

                    <div class="col-6 mt-2">
                        <label>City</label>
                        <input type="text" name="city" id="city" class="form-control" required>

                    </div>
                    <div class="col-6 mt-2">
                        <label>Coordinates</label>
                        <input type="text" name="coordinates" id="coordinates" class="form-control">

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

                <div class="upload-box mt-3">
                    <label>Upload Requirement CSV</label>
                    <input type="file" id="file" class="form-control">
                    <button class="btn btn-dark" type="button" id="BtnUpload">Upload File</button>
                    <a href="import-requirement-list.csv" class="btn btn-outline-primary btn-sm" download>
                        Download Sample
                    </a>
                </div>



                <br>

                <div class="product-card">


                    <div class="row mt-3 ">
                        <div class="col-6">
                            <label for=""> Brand</label> <br>
                            <select name="brand_id" id="brand_id" class="form-control">
                                <option value="">--Select Brand --</option>

                                @foreach ($brand as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-6">
                            <label for=""> Part Code</label> <br>
                            <select name="product_id" id="product_id" class="form-control" required>
                                <option value="">--Select --</option>
                            </select>

                        </div>
                        <div class="col-12 mt-2">
                            <label for="">Product Name <span id="cStock"></span></label> <br>
                            <input type="" name="" id="product_name" class="form-control" disabled>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="">Price</label> <br>
                            <input type="number" name="price" id="price" placeholder="Enter Price"
                                class="form-control" required disabled>
                        </div>
                        <div class="col-6 mt-2">
                            <label for=""> Spec. Dis.% <span id="spcDiscount"></span></label>
                            <input type="number" step="0.01" id="discount" name="discount" class="form-control">
                        </div>
                        <div class="col-6 mt-2">
                            <label for=""> Qty</label> <span id="pQty" class="text-danger"></span> <br>
                            <input type="number" name="qty" id="qty" placeholder="Enter Quantity"
                                class="form-control" required>
                        </div>

                        <div class="col-6 mt-2">

                            <button type="button" onclick="addItem()" class="btn  btn-success mt-3">ADD</button>
                        </div>
                    </div>

                </div>
                <div class="table-responsive">



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
                    <div class="mobile-products d-md-none" id="mobileProductList"></div>


                </div>
                <input type="hidden" name="prod_list" id="prod_list" value="">

                <div class="text-center col-md-12 mt-3">

                    <button type="button" onclick="saveBill()" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>
        </div>

    </div>

    <script>
        var maxDiscount = 0;
        $(document).ready(function() {
            $("select").select2();
            $("#customer_id").select2();


            $("#customer_type").on("change", function() {

                $.ajax({
                    url: "/lead-app/getCustomer",
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

                            html += '<option value="' + element.id + '" data-city="' +
                                element
                                .city1 + '"  data-coordinates="' + element.coordinates +
                                '" ' +
                                status + '> ' +
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
            })

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
                        customer_id: $("#customer_id").val()

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


        });

        $("#discount").on("keyup", function() {
            let discount = parseFloat($(this).val());
            maxDiscount = parseFloat(maxDiscount);

            if (discount > maxDiscount) {
                toastr.error("Discount can not be more then max discount")
                $(this).val(maxDiscount)
            }
        })


        var products = [];
        var row;

        function addItem() {
            var id = parseInt($('#product_id').val())
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

            var ex_p = products.filter((item, index) => item.id == id)

            if (products[id] != undefined) {
                alert('This product already added.');
                return;
            }
            row = `
            <tr class="prod${id}">
 
                <td>${brand}</td>
                <td>${prod_name}</td>
                <td>${product_code}</td>
                <td>${price}</td>
                <td>${discount}</td>
                <td>${qty}</td>
                <td>${price*qty-(price*qty/100*discount)}</td>
                <td><button onclick="removeItem(${id})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
            </tr>
        `;

            let mobileCard = `
<div class="item prod${id}">
    <div class="item-title">${prod_name}</div>
    <div class="meta">${brand} • ${product_code}</div>
    <div class="meta"> Price :  ${price}</div>
    <div class="meta"> Discount : ${discount}</div>
    <div class="meta"> Qty :  ${qty}</div>
 
    <div class="d-flex justify-content-between mt-2">
        <div class="price">₹ ${price*qty-(price*qty/100*discount)}</div>
        <button onclick="removeItem(${id})" class="btn btn-sm btn-outline-danger">
            Remove
        </button>
    </div>
</div>
`;
            $('#productList').append(row);
            $('#mobileProductList').append(mobileCard);
            //var prod=[new Array('id',id),new Array('product_name',prod_name),new Array('qty',qty),new Array('warranty',warranty)]
            products.push({
                id,
                qty,
                price,
                discount,
                order_det_id: 0

            });
            console.log(products);

            $('#product_id').val('')
            $('#qty').val('')
            $('#price').val('')
            $('#discount').val(0)
        }

        function removeItem(id) {
            $(`.prod${id}`).remove();
            console.log(id);
            products = products.filter(item => item.id !== id);
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
                                <td><button onclick="removeItem(${element.qty})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            </tr>
                        `;

                            let id = element.id;
                            let qty = element.qty;
                            let price = element.final_price;
                            let discount = element.discount;
                            products.push({
                                id,
                                qty,
                                price,
                                discount

                            });
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
    </script>
@endsection
