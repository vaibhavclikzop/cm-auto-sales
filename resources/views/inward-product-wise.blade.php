@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>MRN Product Wise</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>MRN Product Wise</h4>
            </div>
            <div class="">


            </div>
        </div>
        <div class="card-body">

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
                        <th>Part Code</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Created at</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->po_name }}</td>
                            <td>{{ $item->vendor }}</td>
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->invoice_no }}</td>
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->received_material_date }}</td>
                            <td>{{ $item->part_no }}</td>
                            <td style="white-space: normal; word-wrap: break-word;">
                                {{ $item->product_name }}
                            </td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->qty * $item->price }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->user }}</td>
                            <td>{{ $item->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
@endsection
