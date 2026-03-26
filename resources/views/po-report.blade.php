@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>PO Report </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>PO Report</h4>
            </div>

        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <td>S.No</td>
                        <td>Product</td>
                        <td>PO Qty</td>
                        <td>Order Qty</td>
                        <td>Current Stock</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->demand_qty }}</td>
                            <td>{{ $item->order_qty }}</td>
                            <td>{{ $item->current_stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
