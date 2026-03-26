@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Special Offer</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Special Offer</h4>
            </div>
            <div class="">
                @if ($rolePermissions->where('permission_name', 'special_offer')->where('view', 1)->where('create', 1)->isNotEmpty())
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bilkImport">
                        <i class="fa fa-download" aria-hidden="true"></i> Bulk Import
                    </button>
                @endif

                @if ($rolePermissions->where('permission_name', 'special_offer')->where('view', 1)->where('del', 1)->isNotEmpty())
                    <button class="btn btn-danger" type="button" id="btnDelete">Delete</button>
                @endif


            </div>
        </div>
        <div class="card-body">

            @if ($rolePermissions->where('permission_name', 'special_offer')->where('view', 1)->where('create', 1)->isNotEmpty())
                {!! session('msg') !!}
                <form action="{{ route('saveSpecialOffer') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Product</label>
                            <select name="product_id[]" id="product_id" class="form-control" required multiple>
                                <option value="">Select</option>
                                @foreach ($products as $item)
                                    <option value="{{ $item->id }}"> {{ $item->part_no }} {{ $item->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label for="">Special Offer</label>
                            <input type="number" step="0.01" class="form-control" name="discount" required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label for="">Valid Till</label>
                            <input type="date" class="form-control" name="expire_date" required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label for="">Save</label> <br>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            @endif


            <div class="mt-2">
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th><input type="checkbox" id="check"></th>
                            <th>Part No</th>
                            <th>Name</th>
                            <th>HSN Code</th>
                            <th>Sale Price</th>
                            <th>Discount</th>
                            <th>Valid Till</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <input type="checkbox" class="checks" name="checks[]" value="{{ $item->id }}">
                                </td>
                                <td>{{ $item->product->part_no }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->product->hsn_code }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->discount }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->expire_date)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>



    <form action="{{ route('deleteSpecialOffer') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="deleteIDS" name="id" hidden>
                        Are you sure you want to delete?
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

    <form action="{{ route('importSpecialOffer') }}" method="POST" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="bilkImport" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Bulk Import
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-6">

                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <div class="col-6">

                                <a href="/import-special-offer.csv" download="/import-special-offer.csv"
                                    class="btn btn-danger btn-sm float-end">Download Sample</a>
                            </div>
                            <div class="col-12 mt-3">
                                <ol>
                                    <li>Date type must be dd/mm/year example : 31/01/2026</li>
                                </ol>
                            </div>

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
        $(document).ready(function() {
            $("#product_id").select2()

            $("#check").on("click", function() {
                if ($(this).prop("checked")) {
                    $(".checks").prop("checked", true)
                } else {
                    $(".checks").prop("checked", false)
                }
            });

            $("#btnDelete").on("click", function() {
                let ids = [];

                $(".checks:checked").each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) {
                    alert("Please select at least one record to delete");
                    return;
                }

                $("#deleteIDS").val(ids.join(","));
                $("#deleteModal").modal("show");

            })
        });
    </script>
@endsection
