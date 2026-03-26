@extends('salesapp.layouts.main')
@section('main-section')
    <div class="row mt-2">
        <div class="col-12 px-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="page-title">
                        <h4>Add Lead</h4>
                    </div>

                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" id="UploadForm" novalidate
                        action="{{ route('sales-app/saveLead') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Source</label>
                                <select name="source_id" id="source_id" class="form-control" required>
                                    <option value="">Select Source</option>
                                    @foreach ($sources as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Electrician</label>
                                <select name="electrician_id" id="electrician_id" class="form-control">
                                    <option value="">Select Electrician</option>
                                    @foreach ($electrician as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mt-3">
                                <label for="">Architect</label>
                                <select name="architect_id" id="architect_id" class="form-control">
                                    <option value="">Select Architect</option>
                                    @foreach ($architect as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Property Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Category</label>
                                <select name="category_id" id="property_category_id" class="form-control">
                                    <option value="">Select category</option>

                                </select>
                            </div>

                            <div class="col-6 mt-3 d-none">
                                <label for="">Sub Category</label>
                                <select name="sub_category_id" id="sub_category_id" class="form-control">
                                    <option value="">Select sub category</option>

                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Property Stage</label>
                                <select name="property_stage" id="property_stage" class="form-control">
                                    <option value="">Select property stage</option>
                                    @foreach ($property_stage as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Time to finalize order</label>
                                <input type="date" name="finalize_date" class="form-control" required>


                            </div>
                            <div class="col-12 ">
                                <hr>

                            </div>
                            <div class="col-md-3">
                                <label for="">Client</label>
                                <select name="client_id" id="client_id" class="form-control" required>
                                    <option value="">Select Client</option>
                                    @foreach ($customers as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-3 mt-3">
                                <label for="">Company Name</label>
                                <input name="company_name" id="company_name" class="form-control">
                            </div>
                            <div class="col-6 mt-3">
                                <label for="">State</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">---Select State---</option>
                                    @foreach ($state as $item)
                                        <option value="{{ $item->state }}">{{ $item->state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="">City</label>
                                <select name="city" id="city" class="form-control">
                                    <option value="">---Select City---</option>
                                </select>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label for="">Address</label>
                                <textarea name="address" id="address" class="form-control"></textarea>

                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" required></textarea>
                            </div>

                            <div class="col-12 mt-3">
                                <hr>
                                <h4>Products</h4>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="wattage_id" id="wattage_id" class="form-control filter">
                                    <option value="">Select Wattage</option>
                                    @foreach ($wattage as $item)
                                        <option value="'{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="fixture_color_id" id="fixture_color_id" class="form-control filter">
                                    <option value="">Select F Color</option>
                                    @foreach ($fixture_color as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mt-2">
                                <select name="r_color_id" id="r_color_id" class="form-control filter">
                                    <option value="">Select R Color</option>
                                    @foreach ($r_color as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="cct_id" id="cct_id" class="form-control filter">
                                    <option value="">Select CCT</option>
                                    @foreach ($cct as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mt-2">
                                <select name="series_name_id" id="series_name_id" class="form-control filter">
                                    <option value="">Select series name</option>
                                    @foreach ($series_name as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="beam_angle_id" id="beam_angle_id" class="form-control filter">
                                    <option value="">Select beam angle</option>
                                    @foreach ($beam_angle as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="category_id" id="category_id" class="form-control filter">
                                    <option value="">Select Category</option>
                                    @foreach ($fp_category as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="sub_category_id" id="sub_category_id" class="form-control filter">
                                    <option value="">Select sub Category</option>
                                    @foreach ($fp_sub_category as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-2">
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">Select product</option>
                                </select>
                            </div>
                            <div class="col-4 mt-2">
                                <input type="number" id="qty" name="qty" class="form-control"
                                    placeholder="Qty">
                            </div>
                            <div class="col-2 mt-2">
                                <button class="btn btn-primary" type="button" id="addProduct">Add</button>
                            </div>

                            <div class="col-12 mt-3">
                                <hr>
                            </div>
                            <div class="col-md-12 mt-2">
                                <hr>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Product </th>

                                            <th>Qty</th>

                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody id="prodList">

                                    </tbody>

                                </table>
                                <input type="hidden" id="prod_list" name="prod_list">

                            </div>
                            <div class="col-md-12 mt-5 text-center">
                                <button class="btn btn-success" id="btnSubmit" type="submit">Save Order</button>

                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- Include Choices.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        // Store Choices instances for each select
        const selectInstances = {};

        // Initialize all selects with search
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select').forEach(function(select) {
                selectInstances[select.id] = new Choices(select, {
                    searchEnabled: true,
                    itemSelectText: '',
                    removeItemButton: false,
                    shouldSort: false, // 🔹 keep original order
                    shouldSortItems: false // 🔹 don't sort selected items either
                });
            });
        });

        // Helper to update any select after AJAX
        function updateSelect(selectId, placeholder, data) {
            if (!selectInstances[selectId]) return;

            selectInstances[selectId].clearChoices();
            selectInstances[selectId].setChoices([{
                    value: '',
                    label: placeholder,
                    selected: true,
                    disabled: true
                },
                ...data.map(el => ({
                    value: el.id,
                    label: el.name
                }))
            ]);
        }


        $("#type").on("change", function() {
            $.ajax({
                url: "/getPropertyCategory",
                type: "POST",
                data: {
                    type: $(this).val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    updateSelect('property_category_id', '----Select Category----', result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        });

        // 2️⃣ Subcategory update
        $("#category_id").on("change", function() {
            $.ajax({
                url: "/getPropertySubCategory",
                type: "POST",
                data: {
                    id: $(this).val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    updateSelect('sub_category_id', '----Select Sub Category----', result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        });

        // 3️⃣ Product update via filters
        $(document).on("change", ".filter", function() {
            let data = {
                wattage_id: $('#wattage_id').val(),
                fixture_color_id: $('#fixture_color_id').val(),
                r_color_id: $('#r_color_id').val(),
                cct_id: $('#cct_id').val(),
                series_name_id: $('#series_name_id').val(),
                beam_angle_id: $('#beam_angle_id').val(),
                category_id: $('#category_id').val(),
                sub_category_id: $('#sub_category_id').val(),
            };

            $.ajax({
                url: "/getFPProducts",
                type: "POST",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    updateSelect('product_id', '----Select Product----', result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        });

        // 4️⃣ Product adding logic stays the same
        let product_list = [];
        let sno = 1;

        $("#customer_id").on("change", function() {
            $("#prodList").html("");
            product_list = [];
        });

        $("#addProduct").on("click", function() {
            let product_id = parseInt($("#product_id").val());
            let product_name = $("#product_id").find(":selected").text();
            let qty = parseInt($("#qty").val());
            let price = parseFloat($("#price").val());
            let gst = $("#gst").val();
            let gst_type = $("#gst_type").val();

            if (!product_id || isNaN(product_id)) {
                toastr.error("Select a valid Product");
                return;
            }
            if (!qty || isNaN(qty) || qty <= 0) {
                toastr.error("Enter a valid quantity");
                return;
            }
            if (product_list.find(p => p.product_id === product_id)) {
                toastr.error("Product already exists");
                return;
            }

            let html = `<tr class="product${product_id}">
                        <td>${sno++}</td>
                        <td>${product_name}</td>
                        <td>${qty}</td>
                        <td>
                            <button type="button" class="btn btn-danger remove btn-sm" data-id="${product_id}">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`;

            $("#prodList").append(html);
            product_list.push({
                product_id,
                qty
            });
            $("#product_id").val("");
            $("#qty").val("");
        });

        $(document).on("click", ".remove", function() {
            let id = parseInt($(this).data("id"));
            $(`.product${id}`).remove();
            product_list = product_list.filter(p => p.product_id !== id);
        });

        $("#UploadForm").on("submit", function(e) {
            e.preventDefault();
            if (this.checkValidity()) {
                $('#prod_list').val(JSON.stringify(product_list));
                $("#btnSubmit").attr("disabled", true);
                this.submit();
            } else {
                this.reportValidity();
            }
        });
    </script>
@endsection
