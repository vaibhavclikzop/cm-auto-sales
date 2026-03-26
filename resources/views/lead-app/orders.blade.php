@extends('lead-app.layouts.main')
@section('main-section')
    <style>
        @media (max-width: 768px) {

            /* Hide table on mobile */
            table.dataTable {
                display: none;
            }

            /* Mobile card wrapper */
            .order-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 14px;
                margin-bottom: 14px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            }

            .order-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .order-id {
                font-weight: 700;
                font-size: 15px;
                color: #0d6efd;
            }

            .order-status {
                font-size: 12px;
                padding: 4px 10px;
                border-radius: 20px;
                text-transform: capitalize;
                font-weight: 600;
            }

            .status-pending {
                background: #fff3cd;
                color: #856404;
            }

            .status-complete {
                background: #d4edda;
                color: #155724;
            }

            .status-cancel {
                background: #f8d7da;
                color: #721c24;
            }

              .status-processing {
                background: orange;
                color: white;
            }

            .order-body {
                font-size: 14px;
                line-height: 1.6;
            }

            .order-body div {
                display: flex;
                justify-content: space-between;
                margin-bottom: 6px;
            }

            .order-body span {
                font-weight: 600;
                color: #555;
            }

            .order-footer {
                margin-top: 10px;
                text-align: right;
            }

            .order-footer .btn {
                border-radius: 20px;
                font-size: 13px;
                padding: 6px 14px;
            }
        }
    </style>

    <div class="card">
        <div class="card-header">
            <a href="/lead-app/orders?status=pending"
                class="btn @if (request('status') == 'pending') btn-success
            @else
                btn-primary @endif">New
            </a>
            <a href="/lead-app/orders?status=processing"
                class="btn @if (request('status') == 'processing') btn-success
            @else
                btn-primary @endif">Pending
            </a>
            <a href="/lead-app/orders?status=complete"
                class="btn @if (request('status') == 'complete') btn-success
            @else
                btn-primary @endif">Completed
            </a>
            <a href="/lead-app/orders?status=cancel"
                class="btn @if (request('status') == 'cancel') btn-success
            @else
                btn-primary @endif">Cancel
            </a>

        </div>
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
        <div class="card-body table-responsive">
            <div class="d-md-none">
                @foreach ($orders as $item)
                    <div class="order-card">

                        <!-- Header -->
                        <div class="order-header">
                            <div class="order-id">
                                Order #{{ $item->order_id }}
                            </div>

                            <div class="order-status status-{{ $item->status }}">
                                {{ $item->status }}
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="order-body">
                            <div>
                                <span>Customer</span>
                                <div>{{ $item->company }}</div>
                            </div>

                            <div>
                                <span>Date</span>
                                <div>{{ date('d-m-Y', strtotime($item->created_at)) }}</div>
                            </div>

                            <div>
                                <span>City</span>
                                <div>{{ $item->city }}</div>
                            </div>

                            <div>
                                <span>Description</span>
                                <div class="text-end" style="max-width:65%">
                                    {{ $item->description }}
                                </div>
                            </div>

                            <div>
                                <span>User</span>
                                <div>{{ $item->user }}</div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="order-footer">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    Actions
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">




                                    <li>
                                        <a class="dropdown-item" href="/lead-app/order-view/{{ $item->id }}">
                                            View Order
                                        </a>
                                    </li>

                                    @if ($item->status == 'pending')
                                        {{-- <li>
                                            <a class="dropdown-item" href="/new-order?id={{ $item->id }}">
                                                Edit Order
                                            </a>
                                        </li> --}}
                                        {{-- <li>
                                            <button class="dropdown-item text-danger cancelOrder"
                                                value="{{ $item->id }}">
                                                Cancel Order
                                            </button>
                                        </li> --}}
                                    @endif
                                </ul>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>

    </div>
@endsection
