@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Defective Stock</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Defective Stock</h4>
            </div>
            <div class="">


                <a class="btn btn-primary" href="add-defective-stock"><i class="fa fa-plus"></i> Add Defective Stock</a>

            </div>
        </div>
        <div class="card-body">
            <table class=" table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Product</th>
                        <th>Location</th>
                        <th>Stock</th>
                        <th>Updated at</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($defective_stock as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
@endsection
