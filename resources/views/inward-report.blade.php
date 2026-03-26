@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Material Receipt Note</h4>
            </div>

        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>PO</th>
                        <th>Vendor</th>
                        <th>Location</th>
                        <th>Invoice</th>
                        <th>Invoice Date</th>
                        <th>R.M Date</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stock_inward_mst as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->po_name }}</td>
                            <td>{{ $item->vendor }}</td>
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->invoice_no }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->received_material_date }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->user }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                <a class="btn btn-info btn-sm" href="/inward-report-view/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>

                                @if ($rolePermissions->where('permission_name', 'mrn')->where('view', 1)->where('del', 1)->isNotEmpty())
                                    <button class="btn btn-danger btn-sm btnDelete" type="button"
                                        value="{{ $item->id }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>



    <form action="{{ route('deleteStockInward') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" hidden name="id" id="id">
                        You are going to delete this MRN
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
        $(document).on("click", ".btnDelete", function() {
            $("#id").val($(this).val())
            $("#deleteModal").modal("show")
        });
    </script>
@endsection
