@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Order Vs Invoice</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Order Vs Invoice</h4>
            </div>
            <div class="">
                <form action="" class="d-flex">
                    <div>
                        <label for="">From Date</label>
                        <input type="date" name="fromDate" class="form-control" value="{{ request('fromDate') }}"
                            onchange="this.form.submit()">
                    </div>
                    <div class="mx-2">
                        <label for="">From Date</label>
                        <input type="date" name="toDate" class="form-control" value="{{ request('toDate') }}"
                            onchange="this.form.submit()">
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Customer</th>
                        <th>Party Code</th>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Order Value</th>
                        <th>PT Date</th>
                        <th>PT ID</th>
                        <th>PT Value</th>
                        <th>Invoice Date</th>
                        <th>Invoice ID</th>
                        <th>Invoice Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->company }}</td>
                            <td>{{ $item->party_code }}</td>
                            <td>{{ $item->order_id }} <br>
                                @if ($item->is_merge == 1)
                                    <span class="badge bg-success">Merge</span>
                                @endif

                            </td>
                            <td>{{ $item->order_date }}</td>
                            <td>{{ $item->orderValue }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->outward_id }}</td>
                            <td>{{ $item->invoice_amount }}</td>

                            @if ($item->is_invoice == 1)
                                <td>{{ $item->invoice_convert_date }}</td>
                                <td>{{ $item->invoice_id }}</td>
                                <td>{{ $item->invoice_amount }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td>
                                <span class="badge bg-info">{{ $item->status }}</span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
