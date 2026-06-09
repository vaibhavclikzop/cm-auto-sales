@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Sale Return Report </h4>
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
                        <th>Customer</th>
                        <th>Part Code</th>
                        <th>Product</th>
                        <th>Invoice No</th>
                        <th>Return ID</th>
                        <th>Return Date</th>
           
                        <th> Return Qty</th>
                        <th> Taxable Amt.</th>
                        <th> GST Amt.</th>
                        <th> Total Amt.</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>

                            <td style="max-width:150px; white-space: normal; word-break: break-word;">{{ $item["customer"] }}
                            </td>
                            <td>{{ $item["part_no"] }}</td>
                            <td style="  white-space: normal; word-break: break-word;">{{ $item["product"] }}</td>
                            <td>{{ $item["invoice_id"] }}</td>
                            <td>{{ $item["id"] }}</td>
                            <td>{{ $item["invoice_date"] }}</td>
                         

                            <td>{{ $item["qty"] }}</td>
                            <td>{{ $item["taxable_amount"] }}</td>
                            <td>{{ $item["gst_amount"] }}</td>
                            <td>{{ $item["total_amount"] }}</td>

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
