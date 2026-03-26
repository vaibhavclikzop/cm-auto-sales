@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchases</h4>
            </div>
            <div class="">

            </div>
        </div>
        <div class="card-body" id="">
            <form method="POST" action="{{ route('SaveInwardStock') }}" id="formMain">
                @csrf

                <div class="row">
                    <div class="col-md-3">
                        <label for="">Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select</option>
                            @foreach ($vendor as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-3">
                        <label for="">PO</label>
                        <select name="po_id" id="po_id" class="form-control">
                            <option value="">Select</option>

                        </select>

                    </div>
                    <div class="col-md-3">
                        <label>Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($warehouse as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label>Location</label>
                        <select name="location_id" id="location_id" class="form-control" required>
                            <option value="">Select</option>

                        </select>

                    </div>

                    <div class="col-md-3  mt-2">
                        <label>Invoice No</label>
                        <input type="text" name="invoice_no" id="invoice_no" class="form-control"
                            placeholder="Enter Invoice No.">

                    </div>

                    <div class="col-md-3  mt-2">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control">
                    </div>
                    <div class="col-md-3 mt-2">
                        <label>Received Material Date</label>
                        <input type="date" name="received_material_date" id="received_material_date"
                            class="form-control">
                    </div>
                    <div class="col-md-3 mt-2">
                        <label>Description</label>
                        <input type="text" name="description" id="description" class="form-control"
                            placeholder="Enter Description">
                    </div>

                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
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
                                <a href="import-po-requirement-list.csv" class="btn btn-success btn-sm"
                                    download="import-po-requirement-list.csv">Download sample file</a>

                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Brand</label>
                                <select name="brand_id" id="brand_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($brand as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-3">
                                <label for="">Product</label>
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">Qty</label>
                                <input type="number" id="qty" class="form-control">

                            </div>
                            <div class="col-md-3">
                                <label for="">Add</label> <br>
                                <button class="btn btn-primary" type="button" id="btnAdd">Add</button>

                            </div>


                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Part Location</th>
                                    <th>Part Code</th>

                                    <th>Actual Qty</th>
                                    <th>Received Qty</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productList">

                            </tbody>
                            <input type="hidden" id="prod_list" name="prod_list">
                        </table>

                    </div>

                </div>

                <div class="text-center col-md-12 mt-3">

                    <button type="button" id="Save" name="btnSubmit" class="btn btn-warning">Submit</button>

                </div>
            </form>


        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("select").select2()

            function getRandomColor() {
                let letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }


            $("#brand_id").on("change", function() {
                $.ajax({
                    url: "/GetProducts1",
                    type: "POST",
                    data: {
                        brand_id: $(this).val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        var html = "";
                        html += '<option value="">----Select Product----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id +
                                '" data-article_code="' +
                                element.part_no + '"  data-name="' +
                                element.name + '" data-price="' +
                                element.purchase_price + '"  data-product_location="' +
                                element.product_location + '">' + element.part_no +
                                ' (' +
                                element.name +
                                ')</option>';
                        });
                        $("#product_id").html(html)
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            })

            var product_list = [];

            $("#vendor_id").on("change", function() {
                $.ajax({
                    url: "/GetPO",
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
                        html += '<option value="">----Select PO----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id + '">' + element
                                .po_id +
                                '</option>';
                        });
                        $("#po_id").html(html)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });


            $("#po_id").on("change", function() {
                $.ajax({
                    url: "/GetPODet",
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
                        var sno = 1;

                        product_list = [];
                        result.forEach(element => {
                            var product_id = element.product_id
                            var qty = element.qty
                            var price = element.price
                            var r_qty = qty - element.received_qty
                            var tableHead = "";
                            var id = 0;
                            if (r_qty > 0) {




                                html += `
                                        <tr class="product${product_id}">
                                        <td>${sno++}</td>    
                                        <td>${element.brand}</td>    
                               
                                        <td>${element.product_name}</td>   
                                        <td>${element.product_location}</td>     
                                        <td>${element.article_no}</td>    
                                        <td>${element.qty}</td>    
                                        <td>${element.received_qty}</td>    
                                        <td><input type="number"   class="form-control qty" data-product_id="${product_id}" 
                                            data-received_qty="${element.received_qty}" 
                                            data-actual_qty="${element.qty}" 
                                            data-id="${product_id}"  value="${r_qty}"></td>  
                                        <td><input type="number" step="0.01" class="form-control price"  data-id="${product_id}"  value="${element.price}"></td>    
                                        <td><button class="btn btn-sm btn-danger remove" type="button" data-id="${product_id}" ><i class="fa fa-trash" aria-hidden="true"></i></button></td>    
                                        </tr>
                                    `;


                                id = product_id;
                                qty = r_qty
                                product_list.push({
                                    id,
                                    product_id,
                                    qty,
                                    price,
                                });



                            }
                        });

                        console.log(product_list);
                        $("#productList").html(html)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });
            var sno = 1;

            $("#btnAdd").on("click", function() {

                var row = "";

                let brand = $("#brand_id").find(":selected").text();
                let product_id = parseInt($("#product_id").val());
                let product_name = $("#product_id").find(":selected").text();
                let part_no = $("#product_id").find(":selected").data("article_code");
                let price = $("#product_id").find(":selected").data("price");
                let qty = $("#qty").val();
                let product_location = $("#product_id").find(":selected").data("product_location");
                let exists = product_list.some(item => item.id == sno);

                if (exists) {
                    toastr.error('This product already added.');
                    return;
                }


                row += `
                            <tr class="product${sno}">
                            <td>${sno}</td>
                            <td >${brand}</td>
                            <td >${product_name}</td>
                            <td >${product_location}</td>
                            <td>${part_no}</td>
                            <td>NA</td>
                            <td>NA</td>
                           
                   
                                <td>${qty}</td>
            
                                <td>${price}</td>
                              
                                <td><button class="btn btn-sm btn-danger remove" type="button" data-id="${sno}" ><i class="fa fa-trash" aria-hidden="true"></i></button></td>   
                            </tr>
                             `;



                product_list.push({
                    id: sno,
                    product_id,
                    qty,
                    price,


                });
                sno++;

                $('#productList').append(row);
                console.log(product_list);



            });

            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.id !== id);
                console.log(product_list);

            });

            $("#Save").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));
                if (!$("#vendor_id").val()) {
                    toastr.error("Select Vendor");
                    return;
                }

                if (!$("#location_id").val()) {
                    toastr.error("Select Location");
                    return;
                }



                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }


                $("#formMain").submit();
            })
            $(document).on("keyup", '.qty', function() {
                var product_id = parseInt($(this).data("product_id"))

                var qty = parseInt($(this).val());
                var received_qty = parseInt($(this).data("received_qty"))
                var actual_qty = parseInt($(this).data("actual_qty"))
                var remaining_qty = actual_qty - received_qty;
                console.log(remaining_qty)
                if (qty > remaining_qty) {
                    toastr.error("Received qty can not be more then remaining qty");
                    $(this).val(remaining_qty)
                    return;
                }

                var product = product_list.find(item => item.product_id === product_id);

                if (product) {

                    product.qty = qty;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }



            })
            $(document).on("keyup", '.price', function() {
                var id = parseInt($(this).data("id"))

                var price = parseFloat($(this).val());
                var product = product_list.find(item => item.id === id);

                if (product) {

                    product.price = price;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }

            })






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

            $("#BtnUpload").on("click", function() {

                let fileInput = document.getElementById('file');
                let file = fileInput.files[0];
                if (file) {
                    // Create a new FormData object
                    let formData = new FormData();
                    formData.append('file', file);

                    $.ajax({
                        url: "/UploadPORequirementList",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(result) {
                            var row = "";
                            let sno = 1;
                            data = JSON.parse(result)
                            data.data.forEach(element => {
                                row += `
                            <tr class="prod${element.id}">
                            <td>${sno++}</td>
                            <td >${element.brand_name}</td>
                            <td >${element.name}</td>
                            <td >${element.product_location}</td>
                            <td>${element.part_no}</td>
                            <td>NA</td>
                            <td>NA</td>
                           
                   
                                <td>${element.qty}</td>
            
                                <td>${element.purchase_price}</td>
                              
                                <td><button onclick="removeItem(${element.qty})" class="btn btn-sm btn-danger" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                            </tr>
                             `;

                                let product_id = element.id;
                                let qty = element.qty;
                                let price = element.purchase_price;
                                let gst = element.gst;
                                let id = element.id;
                                product_list.push({
                                    id,
                                    product_id,
                                    qty,
                                    price,


                                });
                            });

                            $('#productList').append(row);
                            console.log(product_list);

                        },
                        error: function(data) {
                            console.log(data);

                        }
                    });
                } else {
                    toastr.error("Select CSV file for upload");
                }

            });
        });
    </script>
@endsection
