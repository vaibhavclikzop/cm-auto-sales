@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Category Wise Report </h4>
            </div>
            <div class="">



                <form action="">
                    <div class="row">
                        <div class="col-3">
                            <label for=""> From Date </label>
                            <input type="date" name="fromDate" class="form-control" id=""
                                value="{{ request('fromDate') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-3">
                            <label for=""> To Date </label>
                            <input type="date" name="toDate" class="form-control" id=""
                                value="{{ request('toDate') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>


            </div>
        </div>
        <div class="card-body">
            <div id="chart"></div>

            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Category</th>
                     
                  
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Special Discount</th>
                        <th>Taxable</th>
                        <th>GST</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
              
                            <td style="max-width:150px; white-space: normal; word-break: break-word;">{{ $item->name }}
                            </td>
                     
                            <td>{{ $item->amount }}</td>
                            <td>{{ $item->first_discount }}</td>
                            <td>{{ $item->discount }}</td>
                            <td>{{ $item->amount - $item->first_discount - $item->discount }}</td>
                            <td>{{ $item->gst }}</td>
                            <td>{{ $item->amount - $item->first_discount - $item->discount + $item->gst }}</td>
                        </tr>
                    @endforeach
                </tbody>



            </table>
        </div>

    </div>
    <script>
        $(document).ready(function() {
       
        })
    </script>
@endsection
