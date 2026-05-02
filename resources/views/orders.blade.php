@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Orders</title>
    @endpush
    <style>
        .dropdown-menu {
            z-index: 9999 !important;

        }

        table {
            overflow: visible !important;
        }

        .table-responsive {
            overflow: visible !important;
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>
                    @php
                        $status = request('status');
                    @endphp
                    @if ($status == 'pending')
                        New
                    @elseif ($status == 'processing')
                        Pending
                    @elseif ($status == 'complete')
                        Completed
                    @else
                        Cancelled
                    @endif

                    Orders
                </h4>
            </div>
            <div>

            </div>
            <div class="">




            </div>
        </div>
        <div class="row my-2 mx-2">

            <div class="col-md-3">
                <div class="card bg-dark text-">
                    <div class="card-body" style="color: #fff !important;">
                        <h5 style="color: #fff !important;">Total Orders</h5>
                        <h4 style="color: #fff !important;">{{ $totalOrders }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info" style="color:white !important;">
                    <div class="card-body" style="color: #fff !important;">
                        <h5 style="color: #fff !important;">Total Order Value</h5>
                        <h4 style="color: #fff !important;">₹ {{ number_format($totalOrderValue, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary" style="color:white !important;">
                    <div class="card-body" style="color: #fff !important;">
                        <h5 style="color: #fff !important;">Pending Order Value</h5>
                        <h4 style="color: #fff !important;">₹ {{ number_format($totalPendingOrderValue, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 style="color: #fff !important;">In Stock Value</h5>
                        <h4 style="color: #fff !important;">₹ {{ number_format($totalStockValue, 2) }}</h4>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Order ID</th>
                        <th> Customer Name</th>
                        <th>Order Date</th>
                        <th>City</th>

                        <th>Order Value</th>
                        <th>in stock Value</th>
                        <th>PT Value</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($orders as $item)
                        <tr>
                            <th>{{ $sno++ }}</th>
                            <th>{{ $item->order_id }} <br>
                            @if ($item->is_merge==1)
                                <span class="badge bg-success">Merge Order</span>
                            @endif    
                            </th>
                            <th>{{ $item->company }}</th>

                            <th>{{ date('d-m-y', strtotime($item->created_at)) }}</th>
                            <th>{{ $item->city }}</th>
                            <th>{{ $item->order_value }}</th>
                            <td>{{ number_format($item->instock_value, 2) }}</td>
                            <th>{{ $item->pt_value }}</th>
                            <th>{{ $item->description }}</th>
                            <th>{{ $item->status }}</th>
                            <th>{{ $item->user }}</th>
                            <th>

                                <div class="d-flex">

                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if ($item->status != 'complete' && $item->status != 'cancel')
                                                <li><a class="dropdown-item"
                                                        href="/outward-stock?customer_id={{ $item->customer_id }}&order_id={{ $item->id }}">Raise
                                                        Pick Ticket</a>
                                                </li>
                                            @endif




                                            @if ($item->status != 'cancel')
                                                <li><a class="dropdown-item"
                                                        href="/outward-order-list?status=pending&id={{ $item->id }}">
                                                        Pick Ticket List</a>
                                                </li>
                                            @endif
                                            <li><a class="dropdown-item" href="/order-view/{{ $item->id }}">View
                                                    Order</a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="/order-view/{{ $item->id }}?status=pending">Pending Items</a>
                                            </li>
                                            @if ($item->status == 'pending' || $item->status == 'processing')
                                                @if ($rolePermissions->whereIn('permission_name', ['pending_order', 'completed_order'])->where('view', 1)->where('edit', 1)->isNotEmpty())
                                                    <li><a class="dropdown-item btn btn-info cancelOrder"
                                                            href="/new-order?id={{ $item->id }}" type="button">Edit
                                                            Order</a>
                                                    </li>
                                                @endif
                                                @if (
                                                    $item->status == 'pending' &&
                                                        $rolePermissions->whereIn('permission_name', ['pending_order', 'completed_order'])->where('view', 1)->where('del', 1)->isNotEmpty())
                                                    <li><button class="dropdown-item btn btn-danger cancelOrder"
                                                            value="{{ $item->id }}" type="button">Cancel
                                                            Order</button>
                                                    </li>
                                                @endif
                                            @endif

                                            @if ($rolePermissions->whereIn('permission_name', ['pending_order', 'completed_order'])->where('view', 1)->where('del', 1)->isNotEmpty())
                                                @if ($item->status != 'complete' && $item->status != 'pending')
                                                    <li>
                                                        <button class="btn btn-danger dropdown-item scrapOrder"
                                                            type="button" value="{{ $item->id }}">Scrap
                                                            Order</button>
                                                    </li>
                                                @endif
                                            @endif



                                        </ul>
                                    </div>
                                    {{-- <div class="mx-2">

                                        @if (request('status') == 'pending')
                                            <button class="btn btn-danger btn-sm deleteOrder" value="{{ $item->id }}"
                                                type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        @endif
                                    </div> --}}

                                </div>


                                {{-- 

                                <a href="/pi-order-view/{{ $item->id }}" class="btn btn-info btn-sm">
                                    PI
                                </a> --}}
                            </th>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>

    <div class="modal fade" id="cancelModal">
        <div class="modal-dialog" role="document">
            <form action="{{ route('cancelOrder') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Cancel Order

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="order_id" name="order_id">
                        <div class="container-fluid"> You are going to cancel your order</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('scrapOrder') }}" method="post" class="needs-validation" novalidate>
        @csrf

        <div class="modal fade" id="scarpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Scrap Order
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" hidden id="scrapID" name="id">
                        Are you sure you want to scrap this order?
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
        $(document).on("click", ".cancelOrder", function() {
            $("#order_id").val($(this).val())
            $("#cancelModal").modal("show");
        });

        $(document).on("click", ".scrapOrder", function() {
            $("#scrapID").val($(this).val())
            $("#scarpModal").modal("show")
        });
    </script>
@endsection
