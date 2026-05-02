@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Product</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Product</h4>
            </div>
            <div class="">
                <form method="GET" class="row g-2 mb-3">

                    <div class="col-md-2">
                        <select name="brand" class="form-control">
                            <option value="">All Brands</option>
                            @foreach ($brand as $item)
                                <option value="{{ $item->id }}" {{ request('brand') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search name / part / HSN"
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-1">
                        <select name="per_page" class="form-control">
                            @foreach ([20, 50, 100] as $size)
                                <option value="{{ $size }}"
                                    {{ request('per_page', 20) == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex">
                        <button class="btn btn-primary mx-1">Search</button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                    </div>

                </form>

                <form method="POST" action="{{ route('updateBulkDiscount') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <label for="">Discount</label>
                            <input type="number" step="0.01" name="discount" class="form-control" required
                                placeholder="Enter Discount">

                        </div>
                        <div class="col-4">
                            <button class="btn btn-primary mt-4" type="submit">Update</button>
                        </div>
                    </div>
                </form>

            </div>
            <div>

                @if ($rolePermissions->where('permission_name', 'product')->where('view', 1)->where('create', 1)->isNotEmpty())
                    <button type="button" class="btn btn-dark float-end" data-bs-toggle="modal"
                        data-bs-target="#importModal"><i class="fa fa-download"></i> Import Products</button>

                    <button type="button" class="btn btn-primary add mx-2"><i class="fa fa-plus"></i> Add Product</button>
                @endif
            </div>
        </div>
        <div class="card-body">

            {!! session('msg') !!}
            <table class="table">
                <thead>
                    <tr>
                        <th>S.no</th>


                        <th> Brand</th>
                        <th> Category</th>
                        <th> Sub Category</th>
                        <th> Name</th>
                        <th> Part Code</th>
                        <th> HSN Code</th>
                        <th> Barcode</th>
                        <th> MRP</th>
                        <th> Sale Price</th>
                        <th> Purchase Price</th>
                        <th> Min Stock</th>
                        <th> Unit Type</th>
                        <th> Warranty</th>
                        <th> Active</th>


                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($products as $item)
                        @php
                            $active = '';
                            if ($item->active == 1) {
                                $active = "<span class='badge bg-success'>Active</span>";
                            } else {
                                $active = "<span class='badge bg-danger'>In Active</span>";
                            }
                        @endphp
                        <tr>
                            <td>{{ $sno++ }}</td>



                            <td>{{ $item->brand_name }}</td>
                            <td>{{ $item->category_name }}</td>
                            <td>{{ $item->sub_category_name }}</td>
                            <td style="word-wrap: break-word; white-space: normal;">
                                {{ $item->name }}
                            </td>

                            <td>{{ $item->part_no }}</td>
                            <td>{{ $item->hsn_code }}</td>
                            <td>{{ $item->bar_code }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->sale_price }}</td>
                            <td>{{ $item->purchase_price }}</td>
                            <td>{{ $item->min_stock }}</td>
                            <td>{{ $item->unit_type }}</td>
                            <td>{{ $item->warranty_days }}</td>
                            <td>{!! $active !!}</td>



                            <td>

                                @if ($rolePermissions->where('permission_name', 'product')->where('view', 1)->where('edit', 1)->isNotEmpty())
                                    <button class="btn btn-primary btn-sm edit" type="button"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                        data-brand_id="{{ $item->brand_id }}" data-category_id="{{ $item->category_id }}"
                                        data-sub_category_id="{{ $item->sub_category_id }}"
                                        data-category_name="{{ $item->category_name }}"
                                        data-sub_category_name="{{ $item->sub_category_name }}"
                                        data-part_no="{{ $item->part_no }}" data-price="{{ $item->price }}"
                                        data-min_stock="{{ $item->min_stock }}" data-unit_type="{{ $item->uom }}"
                                        data-warranty_days="{{ $item->warranty_days }}"
                                        data-hsn_code="{{ $item->hsn_code }}" data-description="{{ $item->description }}"
                                        data-active="{{ $item->active }}" data-sale_price="{{ $item->sale_price }}"
                                        data-purchase_price="{{ $item->purchase_price }}"
                                        data-product_location="{{ $item->product_location }}"
                                        data-discount="{{ $item->discount }}" data-gst="{{ $item->gst }}"><i
                                            class="fa fa-pencil" aria-hidden="true"></i></button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
            <div class="d-flex justify-content-end">
                {{ $products->links() }}
            </div>

        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-lg">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('SaveProduct') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add Product</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">
                        <div class="col-md-4">
                            <label for="">Image</label>
                            <input type="file" class="form-control" name="file">

                        </div>

                        <div class="col-md-4">
                            <label for="">Brand</label>
                            <select name="brand_id" id="brand_id" class="form-control" required>
                                <option value="">Select Brand</option>
                                @foreach ($brand as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4">
                            <label for="">Category</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">--Select category--</option>
                            </select>

                        </div>
                        <div class="col-md-4 mt-3 ">
                            <label for="">Sub Category</label>
                            <select id="sub_category_id" name="sub_category_id" class="form-control" required>
                                <option value="">--Select Sub Category--</option>
                            </select>


                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">Product Name</label>
                            <input id="name" name="name" class="form-control" placeholder="Enter Product Name"
                                required>

                        </div>

                        <div class="col-md-4  mt-3">
                            <label for="">Part Code</label>
                            <input id="part_no" name="part_no" class="form-control" placeholder="Enter Product No"
                                required>

                        </div>
                        <div class="col-md-4 mt-3 ">
                            <label for="">HSN No</label>
                            <input id="hsn_code" name="hsn_code" class="form-control" placeholder="Enter HSN Code"
                                required>

                        </div>


                        <div class="col-md-4 mt-3 ">
                            <label for="">MRP</label>
                            <input type="number" step="0.01" id="price" name="price" class="form-control"
                                placeholder="Enter Price" required>

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">Sale Price</label>
                            <input type="number" step="0.01" id="sale_price" name="sale_price" class="form-control"
                                placeholder="Enter Price" required>

                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="">Purchase Price</label>
                            <input type="number" step="0.01" id="purchase_price" name="purchase_price"
                                class="form-control" placeholder="Enter Price" required>

                        </div>

                        <div class="col-md-4 mt-3">
                            <label for="">GST</label>
                            <input type="number" class="form-control" name="gst" id="gst" required>

                        </div>
                        <div class="col-md-4 mt-3 ">
                            <label for="">Discount</label>
                            <input type="number" id="discount" name="discount" class="form-control"
                                placeholder="Enter discount" step="0.01" required>

                        </div>

                        <div class="col-md-4 mt-3 ">
                            <label for="">Minimum Stock</label>
                            <input type="number" id="minimum_stock" name="minimum_stock" class="form-control"
                                placeholder="Enter Minimum Stock" required>

                        </div>

                        <div class="col-md-4  mt-3  ">
                            <label for="">UOM (Unit of Mesurement)</label>
                            <select id="uom" name="uom" class="form-control" required>
                                <option value="">--Select uom--</option>
                                @foreach ($unit_type as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>


                        </div>


                        <div class="col-md-4 mt-3  ">
                            <label for="">Warranty (in Days)</label>
                            <input type="number" id="warranty_days" name="warranty_days" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3  ">
                            <label for="">Product Location</label>
                            <input type="" id="product_location" name="product_location" class="form-control"
                                required>
                        </div>
                        <div class="col-md-12 mt-3  ">
                            <label for="">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="col-md-4  mt-3  ">
                            <label for="">Active</label>
                            <select id="active" name="active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>


                        </div>






                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <form action="{{ route('ImportProducts') }}" method="POST" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Import Products</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <div>
                                <a class="btn btn-success" href="import-products.csv"
                                    download="import-products.csv">Download Sample File</a>
                            </div>

                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-danger" role="alert">
                                    <strong>Instructions</strong>
                                </div>
                                <div class="mx-3">
                                    <ul style="list-style:decimal">
                                        <li>First download sample file.</li>
                                        <li>Add your data in sample file.</li>


                                        <li>Part No must be unique.</li>

                                    </ul>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).data("id"));
            $("#name").val($(this).data("name"));
            $("#category_id").html('<option value=' + $(this).data("category_id") + '>' + $(this).data(
                "category_name") + '</option>');
            $("#sub_category_id").html('<option value=' + $(this).data("sub_category_id") + '>' + $(this).data(
                "sub_category_name") + '</option>');
            $("#brand_id").val($(this).data("brand_id"));
            $("#part_no").val($(this).data("part_no"));
            $("#product_type").val($(this).data("product_type"));
            $("#price").val($(this).data("price"));
            $("#minimum_stock").val($(this).data("min_stock"));
            $("#uom").val($(this).data("unit_type"));
            $("#warranty_days").val($(this).data("warranty_days"));
            $("#active").val($(this).data("active"));
            $("#hsn_code").val($(this).data("hsn_code"));
            $("#description").val($(this).data("description"));
            $("#sale_price").val($(this).data("sale_price"));
            $("#gst").val($(this).data("gst"));
            $("#product_location").val($(this).data("product_location"));

            $("#purchase_price").val($(this).data("purchase_price"));
            $("#discount").val($(this).data("discount"));
            $("#modal_name").text("Update Product");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add Product");
            $("#id").val("");
            $("#exampleModal").modal("show");
        });

        $("#brand_id").on("change", function() {
            $.ajax({
                url: "/GetCategory",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Category----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#category_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

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
    </script>
@endsection
