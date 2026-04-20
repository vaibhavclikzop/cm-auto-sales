@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Pick Ticket List</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Pick Ticket List</h4>
            </div>
            <div>

                <a class="btn {{ request()->status == 'pending' ? 'btn-success' : 'btn-primary' }}"
                    href="/outward-order-list?status=pending&id={{ request('id') }}">Pending</a>
                <a class="btn {{ request()->status == 'complete' ? 'btn-success' : 'btn-primary' }}"
                    href="/outward-order-list?status=complete&id={{ request('id') }}">Invoice Converted</a>

                <a class="btn {{ request()->status == 'cancel' ? 'btn-danger' : 'btn-primary' }}"
                    href="/outward-order-list?status=cancel&id={{ request('id') }}">Cancel</a>
            </div>

        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>PT ID </th>
                        <th>Customer </th>
                        <th>Order ID </th>


                        <th>Invoice Date </th>
                        <th>Invoice Amt. </th>
                        <th>City </th>
                        <th>Party Code </th>
                        <th>Status </th>
                        <th>User </th>
                        <th>Action </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($outward as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->outward_id }}</td>
                            <td>{{ $item->customer }}</td>
                            <td>{{ $item->order_id }}</td>


                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->total }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->party_code }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->user }}</td>
                            <td>


                                @if ($item->is_invoice == 0 && $item->status == 'pending')

                                                 @if ($rolePermissions->whereIn('permission_name', ['view_pick_tickets'])->where('view', 1)->where('del', 1)->isNotEmpty())
                                    <button class="btn btn-danger btn-sm cancelOrder" type="button"
                                        value="{{ $item->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Cancel this ticket">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    @endif

                                    @if ($rolePermissions->whereIn('permission_name', ['view_pick_tickets'])->where('view', 1)->where('edit', 1)->isNotEmpty())
                                        <a class="btn btn-success btn-sm" href="/outward-stock?out_id={{ $item->id }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Ticket">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    @endif
                                @endif


                                @if ($item->dispatch_status == 'pending' && $item->status != 'cancel' && $item->is_invoice == 1)
                                    <button class="btn btn-info btn-sm dispatch" value="{{ $item->id }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Send to dispatch">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </button>
                                @endif

                                <a class="btn btn-dark btn-sm" href="/outward-challan-view/{{ $item->id }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="View Ticket">
                                    <i class="fa fa-eye"></i>
                                </a>


                                <a class="btn btn-info btn-sm" href="/invoice-view/{{ $item->id }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="View Invoice"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

    <form action="{{ route('DispatchChallan') }}" method="POST">
        @csrf
        <div class="modal fade" id="dispatchModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Send to dispatch plan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="dispatch_id" name="id">
                        You are going to send this ticket to dispatch plan.
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
    <form action="{{ route('DeliveredChallan') }}" method="POST">
        @csrf
        <div class="modal fade" id="deliverehModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Delivery Order
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="delivery_id" name="id">
                        You are going to delivered this challan
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
    <form action="{{ route('convertToInvoice') }}" method="POST">
        @csrf
        <div class="modal fade" id="invoiceModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Convert To Invoice
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="invoiceID" name="id">
                        You are going to convert this Ticket to invoice <br>
                        <label for="">Invoice Amount</label>
                        <input type="text" id="invoiceAmt" disabled class="form-control">
                        <label for="" class="mt-3">Additional Discount</label>
                        <input type="number" step="0.01" name="discount" class="form-control" required
                            value="0">
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


    <form action="{{ route('cancelOutwardChallan') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="cancelModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="modalTitleId">
                            Cancel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ChallanID">
                        Are you sure you want to cancel this pick ticket? <br>
                        Once cancelled, this action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <script>
        $(document).on("click", ".dispatch", function() {
            $("#dispatch_id").val($(this).val())
            $("#dispatchModal").modal("show");
        });
        $(document).on("click", ".delivere", function() {
            $("#delivery_id").val($(this).val())
            $("#deliverehModal").modal("show");
        });

        $(document).on("click", ".convertInvoice", function() {
            $("#invoiceAmt").val($(this).data("total"))
            $("#invoiceID").val($(this).val())
            $("#invoiceModal").modal("show");
        });

        $(document).on("click", ".cancelOrder", function() {
            $("#ChallanID").val($(this).val())
            $("#cancelModal").modal("show")
        })
    </script>
@endsection
