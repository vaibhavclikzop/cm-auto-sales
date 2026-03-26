@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Current Stock</h4>
            </div>
            <form method="GET" {{ route('current-stock') }}>
                <div class="d-flex mt-4 justify-content-between">
                    <div>
                        <div class="d-flex">
                            <div>
                                <label for="">Show Record</label>
                                <select name="record" id="record" class="form-control" onchange="this.form.submit()">

                                    <option value="50" {{ request('record') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('record') == '100' ? 'selected' : '' }}>100</option>
                                    <option value="200" {{ request('record') == '200' ? 'selected' : '' }}>200</option>
                                    <option value="500" {{ request('record') == '500' ? 'selected' : '' }}>500</option>
                                    <option value="All" {{ request('record') == 'All' ? 'selected' : '' }}>All</option>


                                </select>
                            </div>
                            <div class="mx-5">

                                <label for="">Location</label>
                                <select name="location" id="" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select</option>
                                    @foreach ($location as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('location') == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex">
                        <div>
                            <input type="search" class="form-control" placeholder="Search" name="search"
                                value="{{ request('search') }}">
                        </div>
                        <div>
                            <button class="btn btn-info" type="submit">Search</button>
                        </div>
                        <div>
                            <button class="btn btn-primary mx-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">Import CS</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp
            <table class="table btnDataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Brand</th>
                        <th>Part Code</th>
                        <th>Product Location</th>
                        <th>Product Name</th>

                        <th>Location</th>
                        <th>Stock</th>
                        <th>Purchase Price</th>
                        <th>Total Amount</th>
                        <th>Created at</th>
                        <th>Update at</th>

                        <th>Action</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($current_stock as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->brand }}</td>
                            <td>{{ $item->article_no }}</td>
                            <td>{{ $item->product_location }}</td>
                            <td style="white-space: normal; word-wrap: break-word;">
                                {{ $item->product }}
                            </td>


                            <td>{{ $item->location }}</td>
                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->purchase_price }}</td>
                            <td>{{ $item->stock * $item->purchase_price }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->updated_at }}</td>

                            <td>
                                <button class="btn btn-primary btn-sm edit" type="button" value="{{ $item->id }}"><i
                                        class="fa fa-pencil" aria-hidden="true"></i></button>

                                <button class="btn btn-danger btn-sm updateLocation" type="button"
                                    value="{{ $item->product_id }}" data-name="{{ $item->product_location }}"> <i
                                        class="fa fa-street-view" aria-hidden="true"></i>
                                </button>

                                <button class="btn btn-secondary btn-sm view" type="button" value="{{ $item->id }}"><i
                                        class="fa fa-history" aria-hidden="true"></i></button>
                            </td>



                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
        <div class="card-footer">
            <div>
                {{ $current_stock->appends(request()->only(['search', 'record', 'location']))->links() }}

            </div>
        </div>

    </div>


    <form action="{{ route('SaveStock') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Stock Adjustment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <label>Qty</label>
                        <input type="number" class="form-control" name="qty" required>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="viewModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Stock Adjustment History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Qty</th>
                                <th>Created at</th>
                            </tr>
                        </thead>
                        <tbody id="viewList">

                        </tbody>

                    </table>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('BulkImportCS') }}" method="POST" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Import CS</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="file" name="file" class="form-control" required>

                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-dark btn-sm float-end" href="/import cs.csv"
                                    download="/import cs.csv">Download Sample</a>

                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('updateProductLocation') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Update Location
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="hidden" hidden name="product_id" id="product_id">
                            <label for="">Location Name</label>
                            <input type="text" name="product_location" id="product_location" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <script>
        $(document).on("click", ".edit", function() {
            $("#id").val($(this).val())

            $("#modalId").modal("show")
        });

        $(document).on("click", ".view", function() {
            var id = $(this).val();

            $.ajax({
                url: "/GetStockAdjustmentHistory",
                type: "POST",
                data: {
                    id: id,
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
                    result.forEach(element => {
                        html += `
                                <tr>
                                    <td>${sno++}</td>    
                                    <td>${element.qty}</td>    
                                    <td>${element.created_at}</td>    
                                </tr>
                            `;

                    });

                    $("#viewList").html(html)
                    $("#viewModal").modal("show")

                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });


        });
        $(document).ready(function() {
            $(".btnDataTable").DataTable({
                dom: 'Bfrtip', // 'B' = Buttons, 'f' = filter, 'r' = processing, 't' = table, 'i' = info, 'p' = pagination
                buttons: ["excel", "csv"],
                paging: false, // Disable pagination
                searching: false, // Disable search box
                info: false, // Disable "Showing 1 to X of Y"
                ordering: false, // Disable sorting
            });
            $(document).on("click", ".updateLocation", function() {
                $("#product_id").val($(this).val())
                $("#product_location").val($(this).data("name"))
                $("#locationModal").modal("show")
            });

        })
    </script>
@endsection
