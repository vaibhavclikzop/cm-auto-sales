@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Product Wise Sale Report</h4>
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
                        <div class="col-3">
                            <label for=""> Type </label>
                            <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                <option value="">Select</option>
                                <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Invoice</option>
                                <option value="0" {{ request('type') == '0' ? 'selected' : '' }}>Pick Ticket</option>
                            </select>
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
                        <th>Part Code</th>
                        <th>Product</th>
                        <th>Qty</th>
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
                            <td>{{ $item->part_no }}</td>
                            <td style="max-width:150px; white-space: normal; word-break: break-word;">{{ $item->name }}
                            </td>
                            <td>{{ $item->qty }}</td>
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
@endsection
