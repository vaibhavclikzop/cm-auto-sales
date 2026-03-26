@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Add Scrap Stock</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Add Scrap Stock</h4>
            </div>

        </div>
        <div class="card-body">
            <form id="frmMain" method="POST" action="{{ route('SaveScrapProducts') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    
                                    <th>
                                        <label for="">Location</label>
                                        <select name="location_id" id="location_id" class="form-control">
                                            <option value="">Select Location</option>
                                            @foreach ($location as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <label for="">Order</label>
                                        <select name="order_id" id="order_id" class="form-control">
                                            <option value="">Select Order</option>

                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Product </th>
                                    <th>Article No</th>
                                    <th>Qty</th>
                                    <th>Scrap Qty</th>
                                    <th>Defective Qty</th>
                                    <th>Inward Qty</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody id="prodList">

                            </tbody>
                        </table>
                        <input type="hidden" name="prod_list" id="prod_list" value="">
                    </div>

                    <div class="text-center col-md-12 mt-3">

                        <button type="button" id="Save" name="btnSubmit" class="btn btn-warning">Submit</button>

                    </div>
                </div>
            </form>

        </div>

    </div>



    <script>
        $(document).ready(function() {

            var qty = 0;

            var product_list = [];
            $("#location_id").on("change", function() {
                var location_id = $(this).val()
                var team_id = $("#team_id").val()
                if (team_id == false) {
                    toastr.error("Select team first");
                    $(this).val("");
                    return;
                }
                $.ajax({
                    url: "/GetGenSet",
                    type: "POST",
                    data: {
                        location_id: location_id,
                        team_id: team_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        var products = "<option>---Select Lift---</option>";
                        result.forEach(element => {
                            products +=
                                `<option value="${element.id}" >${element.customer}, Delivery : ${element.delivery_date}</option>`;

                        });

                        $("#order_id").html(products)
                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });


            $("#order_id").on("change", function() {
                var gen_set_id = $(this).val()


                $.ajax({
                    url: "/GetGenSetProducts",
                    type: "POST",
                    data: {
                        id: gen_set_id,

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },
                    success: function(result) {
                        product_list = [];
                        var sno = 1;
                        var products = "";

                        result.forEach(element => {
                            var id = element.id;
                            var scrap_qty = 0;
                            var defective_qty = 0;
                            var inward_qty = 0;
                            qty = element.qty;
                            products += `
                            
                                        <tr>
                                            <td>${sno++}</td>    
                                            <td>${element.product}</td>    
                                            <td>${element.article_no}</td>    
                                            <td>${element.qty}</td>    
                                            <td>
                                            <input type="number" value="0" class="form-control scrap_qty" data-id="${element.id}">    
                                            </td>    
                                             <td>
                                            <input type="number" value="0" class="form-control defective_qty" data-id="${element.id}">    
                                            </td>   
                                             <td>
                                            <input type="number" value="0" class="form-control inward_qty" data-id="${element.id}">    
                                            </td>   
                                               <td>
                                            <input type="file"  name="files[${element.id}]"  class="form-control file" data-id="${element.id}">    
                                            </td> 
                                        </tr>
                                    `;
                            product_list.push({
                                id,
                                scrap_qty,
                                defective_qty,
                                inward_qty,

                            });


                        });

                        $("#prodList").html(products)
                        console.log(product_list)

                    },
                    complete: function() {
                        $("#loader").hide();
                    },
                    error: function(result) {
                        toastr.error(result.responseJSON.message);
                    }
                });

            });



            $(document).on("keyup", '.scrap_qty', function() {
                var id = parseInt($(this).data("id"))

                var scrap_qty = parseInt($(this).val());
                var product = product_list.find(item => item.id === id);

                if (product) {

                    product.scrap_qty = scrap_qty;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }

            })

            $(document).on("keyup", '.defective_qty', function() {
                var id = parseInt($(this).data("id"))

                var defective_qty = parseInt($(this).val());
                var product = product_list.find(item => item.id === id);

                if (product) {

                    product.defective_qty = defective_qty;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }

            })

            $(document).on("keyup", '.inward_qty', function() {
                var id = parseInt($(this).data("id"))

                var inward_qty = parseInt($(this).val());
                var product = product_list.find(item => item.id === id);

                if (product) {

                    product.inward_qty = inward_qty;
                    console.log("Updated Product List:", product_list);
                } else {
                    toastr.error("Something went wrong");
                    return;
                }

            });
            $("#Save").on("click", function() {
                $(".file").each(function() {
                    const id = $(this).data("id");
                    const product = product_list.find(item => item.id === id);
                    if (product) {
                        product.fileName = this.files[0] ? this.files[0].name :
                        null; // Optional: File name for reference
                    }
                });

                $('#prod_list').val(JSON.stringify(product_list));

                if (product_list.length === 0) {
                    toastr.error("Select at least one product");
                    return;
                }

                console.log(product_list);
                $("#frmMain").submit();
            });


            document.addEventListener("change", (event) => {
                if (event.target.classList.contains("file")) {
                    const id = event.target.dataset.id;
                    const product = product_list.find(item => item.id == id);
                    if (product) {
                        product.file = event.target.files[0]; // Store the file in the `product_list`
                    }
                    console.log(product_list);
                }
            });



        });
    </script>
@endsection
