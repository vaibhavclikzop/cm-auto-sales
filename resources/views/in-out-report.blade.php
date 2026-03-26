@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>In Out Report</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>In Out Report</h4>
            </div>
             
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th> Name</th>
                        <th> Part No</th>
                        <th> In Qty</th>
                        <th> Out Qty</th>
                        <th> Current Stock</th>


             

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

           <td style="white-space: normal; word-break: break-word;">
    {{ $item->name }}
</td>

                            <td>{{ $item->part_no }}</td>
                            <td>{{ $item->in_qty }}</td>
                            <td>{{ $item->out_qty }}</td>
                            <td>{{ $item->current_stock }}</td>



                            
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>


 
@endsection
