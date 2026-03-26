@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">

        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header" style="background-color: white">
                        <div class="row">


                            <div class="col-8">


                                <p class="small" style="margin: 0;padding;0">{{ $lead->customerDetails->name }}</p>
                                <p class="text-secondary small"> {{ $lead->customerDetails->number }}<br>
                                    {{ $lead->customerDetails->address }}</p>
                            </div>
                            <div class="col-4" style="text-align: right">



                                {{ $lead->status ? $lead->status->name : 'NA' }}<br>
                                {{ $lead->id }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sales-app/updateLead') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" value="{{request("id")}}" name="id">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Source</label>
                                    <select name="source_id" id="source_id" class="form-control" required>
                                        <option value="">Select Source</option>
                                        @foreach ($sources as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $lead->source_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-6 mt-3">
                                    <label for="">Electrician</label>
                                    <select name="electrician_id" id="electrician_id" class="form-control">
                                        <option value="">Select Electrician</option>
                                        @foreach ($electrician as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $lead->electrician_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 mt-3">
                                    <label for="">Architect</label>
                                    <select name="architect_id" id="architect_id" class="form-control">
                                        <option value="">Select Architect</option>
                                        @foreach ($architect as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $lead->architect_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 mt-3">
                                    <label for="">Property Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="Residential"
                                            {{ $lead->type == 'Residential' ? 'selected' : '' }}>
                                            Residential</option>
                                        <option value="Commercial"
                                            {{ $lead->type == 'Commercial' ? 'selected' : '' }}>
                                            Commercial</option>
                                    </select>
                                </div>

                                <div class="col-6 mt-3">
                                    <label for="">Category</label>
                                    <select name="category_id" id="property_category_id" class="form-control">
                                        <option value="">Select category</option>
                                        @if ($lead->propertyCategory)
                                            <option value="{{ $lead->propertyCategory->id }}" selected>
                                                {{ $lead->propertyCategory->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-6 mt-3 d-none">
                                    <label for="">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-control">
                                        <option value="">Select sub category</option>
                                        @if ($lead->propertySubCategory)
                                            <option value="{{ $item->propertySubCategory->id }}">
                                                {{ $item->propertySubCategory->name }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-6 mt-3">
                                    <label for="">Property Stage</label>
                                    <select name="property_stage" id="property_stage" class="form-control">
                                        <option value="">Select property stage</option>
                                        @foreach ($property_stage as $item)
                                            <option value="{{ $item->name }}"
                                                {{ $lead->property_stage == $item->name ? 'selected' : '' }}>
                                                {{ $item->name }} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 mt-3">
                                    <label for="">Time to finalize order</label>
                                    <input type="date" name="finalize_date" class="form-control" required
                                        value="{{ $lead->finalize_date }}">


                                </div>
                                <div class="col-12 ">
                                    <hr>

                                </div>
                                <div class="col-md-3">
                                    <label for="">Client</label>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value="">Select Client</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $lead->client_id == $item->id ? 'selected' : '' }}> {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-3 mt-3">
                                    <label for="">Company Name</label>
                                    <input name="company_name" id="company_name" class="form-control"
                                        value="{{ $lead->company_name }}">
                                </div>
                                <div class="col-6 mt-3">
                                    <label for="">State</label>
                                    <select name="state" id="state" class="form-control">
                                        <option value="">---Select State---</option>
                                        @foreach ($state as $item)
                                            <option value="{{ $item->state }}"
                                                {{ $lead->state == $item->state ? 'selected' : '' }}>{{ $item->state }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6 mt-3">
                                    <label for="">City</label>
                                    <select name="city" id="city" class="form-control">
                                        <option value="">---Select City---</option>
                                        @if ($lead->city)
                                            <option value="{{ $lead->city }}" selected>
                                                {{ $lead->city }}
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="">Address</label>
                                    <textarea name="address" id="address" class="form-control"> {{ $lead->address }}</textarea>

                                </div>

                                <div class="mt-2 col-12">
                                    <button class="btn btn-primary w-100" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
