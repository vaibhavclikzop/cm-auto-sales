@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order</h4>
            </div>
            <div class="">

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>PO ID</th>
                        <th>Vendor Name</th>
                        <th>User Name</th>


                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($po_mst as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->po_id }}</td>
                            <td>{{ $item->vendor_name }}</td>
                            <td>{{ $item->user_name }}</td>


                            <td>{{ date('d-m-Y h:i A ', strtotime($item->created_at)) }}</td>
                            <td>
                                @if ($status == 'pending')
                                    @if ($rolePermissions->where('permission_name', 'generating_po')->where('view', 1)->where('edit', 1)->isNotEmpty())
                                        <button class="btn btn-sm btn-info editStatus" type="button"
                                            data-id="{{ $item->id }}">Generate PO</button>

                                        <a href="/generate-po?id={{ $item->id }}" class="btn btn-success btn-sm"><i
                                                class="fa fa-pencil" aria-hidden="true"></i></a>
                                    @endif

                                    @if ($rolePermissions->where('permission_name', 'generating_po')->where('view', 1)->where('del', 1)->isNotEmpty())
                                        <button class="btn btn-danger btn-sm btnDelete" value="{{ $item->id }}"
                                            type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    @endif
                                @endif
                                <a class="btn btn-primary btn-sm" href="/purchase-order-view/{{ $item->id }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

    <form action="{{ route('SaveGeneratePO') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Generate PO
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">

                        <h4>You are going to generate PO</h4>


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


    <form action="{{ route('deletePO') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="modalTitleId">
                            Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" hidden id="deleteID" name="id">
                        Are you sure you want to delete this PO?
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
            $(document).on("click", ".editStatus", function() {
                $("#id").val($(this).data("id"))
                $("#modalId").modal("show")
            })

            $(document).on("click", ".btnDelete", function() {
                $("#deleteID").val($(this).val())
                $("#deleteModal").modal("show")
            })
        });
    </script>
@endsection
