@extends('layouts.main')
@section('main-section')
<style>
    .table-success {
    background-color: #e6ffe6;
}

.table-danger {
    background-color: #ffe6e6;
}
</style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Generate PO</h4>
            </div>
            <div class="">

                {{-- <a href="generate-po-product" class="btn btn-dark">Generate PO Via Products</a> --}}

            </div>
        </div>
        <div class="card-body">
            <form method="POST" id="frmMain" action="{{ route('SavePO') }}">
                @csrf
                <input type="hidden" hidden id="orderID" name="id">

                <div class="row">
                    <div class="col-md-3">
                        <label>Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <option value="">Select Vendor</option>
                            @foreach ($vendor as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} ( {{ $item->company }})</option>
                            @endforeach

                        </select>

                    </div>
                    <div class="col-md-3">
                        <label for="">PO Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter PO Name">

                    </div>
                    <div class="col-md-6">
                        <label for="">Description</label>
                        <input type="text" name="description" id="description" class="form-control"
                            placeholder="Enter PO Description">

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

                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        <label for="">Products</label> <br>
                                        <select name="product_id" id="product_id" class="form-control">
                                            <option value="">Select Product</option>
                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Qty</label>
                                        <input type="number" name="qty" id="qty" min="1" value="1"
                                            class="form-control" placeholder="Enter Qty">
                                    </th>
                                    <th>
                                        <label for="">Price</label>
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control" placeholder="Enter price">

                                    </th>

                                    <th>
                                        <button class="btn btn-primary mt-4" type="button" id="addProduct">Add</button>
                                    </th>
                                </tr>
                                <tr>
                                    <th>S.No</th>
                                    <th colspan="2">Product Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>GST</th>

                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list" value="">

                        <div class="text-center col-md-12 mt-3">

                            <button type="button" id="SavePO" name="btnSubmit" class="btn btn-warning">Submit</button>

                        </div>


                    </div>

                </div>

            </form>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            var product_list = [];
            $("select").select2();
            $("#vendor_id").on("change", function() {
                $.ajax({
                    url: "/GetVendorProducts",
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
                        html += '<option value="">----Select Products----</option>';
                        result.forEach(element => {

                            html += '<option value="' + element.id + '" data-price="' +
                                element
                                .purchase_price + '"   data-gst="' +
                                element
                                .gst + '"> ' + element.part_no + ' (' + element.name +
                                ')</option>';
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
            $("#product_id").on("change", function() {
                $("#price").val($(this).find(":selected").data("price"))
            });


            var sno = 1;
            $("#addProduct").on("click", function() {
                var product_id = parseInt($("#product_id").val())
                var product_name = $("#product_id").find(":selected").text()
                var qty = parseInt($("#qty").val())
                var price = parseFloat($("#price").val())
                var gst = parseInt($("#product_id").find(":selected").data("gst"))
                var gst_type = "Outer GST"

                if (!product_id || isNaN(product_id)) {
                    toastr.error("Select a valid Product");
                    return;
                }

                if (!qty || isNaN(qty) || qty <= 0) {
                    toastr.error("Enter a valid quantity");
                    return;
                }

                if (!price || isNaN(price) || price <= 0) {
                    toastr.error("Enter a valid price");
                    return;
                }

                let existingProduct = product_list.find(product => product.product_id === product_id);
                if (existingProduct) {
                    toastr.error("Product already exists");
                    return;
                }

                var html = `<tr class="product${product_id}">
                            <td>${sno++}</td>    
                            <td colspan="2">${product_name}</td>    
                            <td>${qty}</td>    
                            <td>${price}</td>    
                            <td>${gst}</td>    
               
                            <td>${ (price*qty)+price*qty/100*gst}</td>   
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
                    price,
                    gst,

                });

            });

            $(document).on("click", ".remove", function() {
                let id = parseInt($(this).data("id"))

                $(`.product${id}`).remove();
                product_list = product_list.filter(item => item.product_id !== id);

            });
            $("#SavePO").on("click", function() {
                $('#prod_list').val(JSON.stringify(product_list));
                if (!$("#vendor_id").val()) {
                    toastr.error("Select Vendor");
                    return;
                }

                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }


                if ($("#password").val() == false) {
                    toastr.error("Enter Password");
                    return;
                }
                $('#frmMain').submit()

            })


            let mst = {!! json_encode($data) !!};
            let det = {!! json_encode($det) !!};
            if (mst) {
                let sno = 1;
                let total = 0;
                $.each(mst, function(i, o) {
                    $("input[name=" + i + "]").val(o)
                    $("select[name=" + i + "]").val(o)
                    if (i == "invoice_no") {
                        $("#invoice_no").html(`<option value="${o}">${o}</option>`)
                    }
                })
                $("#orderID").val(mst.id)
                $("#vendor_id").trigger("change");




                let currency = mst.currency;


                det.forEach(element => {
                    let product_id = parseInt(element.product_id);
                    let product_name = element.name;
                    let catalog_no = element.catalog_no;
                    let hsn_code = element.hsn_code;
                    let po_no = element.po_no
                    let qty = element.qty;
                    let price = element.price;
                    let gst = element.gst;
                    let discount_type = element.discount_type;
                    let discount_value = element.discount;
                    let double_discount = element.double_discount;

                    let is_double_discount = 0;
                    let product_remarks = element.remarks;


                    id = sno;

                    var html = `<tr class="product${product_id}">
                    
                            <td>${product_id}</td>    
        
                            <td style=" " colspan="2">${product_name}</td>    
                             <td>
                                <input type="number" value="${qty}"  data-id="${product_id}" class="form-control qtyUpdate" style="width:100px">
                            </td>  
                            <td>
                                <input type="number" step="0.01" value="${price}"  data-id="${product_id}" class="form-control priceUpdate" style="width:100px">
                            </td>  
                            <td>${gst}</td>    
                             
                  
                       
             
                            <td>${ (price*qty)+price*qty/100*gst}</td>    
              
               
                            <td> 
                                <button type="button"  class="btn btn-danger remove btn-sm"  data-id="${product_id}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                          
                            </td>    
                        </tr>`;

                    $("#prodList").prepend(html)
                    product_list.push({

                        product_id,
                        qty,
                        price,
                        gst,

                    });

                    sno++;
                });



            }

            $(document).on("keyup", ".qtyUpdate", function() {
                let product_qty = $(this).data("id")
                let qty = $(this).val()

                var product = product_list.find(item => item.product_id === product_qty);

                if (product) {

                    product.qty = qty;

                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
            });

            $(document).on("keyup", ".priceUpdate", function() {
                let product_qty = $(this).data("id")
                let price = $(this).val()

                var product = product_list.find(item => item.product_id === product_qty);

                if (product) {

                    product.price = price;

                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }
            });


$("#BtnUpload").on("click", function() {

    let fileInput = document.getElementById('file');
    let file = fileInput.files[0];

    if (file) {

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

                let data = JSON.parse(result);

                let foundRows = "";
                let notFoundRows = "";

                let sno1 = 1;
                let sno2 = 1;

                if (data.data && data.data.length > 0) {

                    data.data.forEach(element => {

                        if (element.found) {

                            foundRows += `
                                <tr class="prod${element.id}">
                                    <td>${sno1++}</td>
                                    <td colspan="2">${element.name}</td>
                                    <td>${element.qty}</td>
                                    <td>${element.purchase_price}</td>
                                    <td>${element.gst}</td>
                                    <td>${element.purchase_price * element.qty}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" type="button">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                            product_list.push({
                                product_id: element.id,
                                qty: element.qty,
                                price: element.purchase_price, // ✅ fix
                                gst: element.gst,
                            });

                        } else {

                            notFoundRows += `
                                <tr class="table-danger">
                                    <td>${sno2++}</td>
                                    <td colspan="2">${element.part_no} (${element.name})</td>
                                    <td>${element.qty}</td>
                                    <td>NULL</td>
                                    <td>NULL</td>
                                    <td>NULL</td>
                                    <td>-</td>
                                </tr>
                            `;
                        }

                    });

                    // ✅ loop ke baad HTML banao
                    let finalHtml = "";

                    if (foundRows !== "") {
                        finalHtml += `
                            <tr class="table-success">
                                <td colspan="8"><b>Found Products</b></td>
                            </tr>
                            ${foundRows}
                        `;
                    }

                    if (notFoundRows !== "") {
                        finalHtml += `
                            <tr class="table-danger">
                                <td colspan="8"><b>Not Found Products</b></td>
                            </tr>
                            ${notFoundRows}
                        `;
                    }

                    $('#prodList').html(finalHtml);

                } else {

                    $('#prodList').html(`
                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                No Data Found
                            </td>
                        </tr>
                    `);
                }
            }
        });

    } else {
        toastr.error("Select CSV file for upload");
    }
});
        });
    </script>
@endsection
