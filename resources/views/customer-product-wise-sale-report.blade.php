@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Customer/Product Report</h4>
            </div>
            <div class="">



                <form action="">
                    <div class="row">

                        <div class="col-md-2">
                            <label for="">Customers</label>
                            <select name="customer_id" id="customer_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Select</option>
                                @foreach ($customers as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('customer_id') == $item->id ? 'selected' : '' }}>{{ $item->company }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="">DSR</label>
                            <select name="user_id" id="user_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Select</option>
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('user_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label for=""> From Date </label>
                            <input type="date" name="fromDate" class="form-control" id=""
                                value="{{ request('fromDate') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-2">
                            <label for=""> To Date </label>
                            <input type="date" name="toDate" class="form-control" id=""
                                value="{{ request('toDate') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-3">
                            <label for=""> Part Code/Name </label>
                            <input type="search" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Search by product code/Name">

                        </div>
                        <div class="col-1">
                            <br>
                            <button class="btn btn-primary" type="submit">Search</button>
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
                         <th>Party Code</th>
                        <th>Customer</th>
                       
                        <th>DSR</th>
                        <th>District</th>
                        <th>City</th>
                        <th>Order Date</th>
                        <th>PT Date</th>
                        <th>Invoice Date</th>
                        <th>Invoice ID</th>
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
                        $total_qty = 0;
                    @endphp
                    @foreach ($data as $item)
                        @php
                            $total_qty += $item->qty;
                        @endphp
                        <tr>
                            <td>{{ $sno++ }}</td>
                               <td >
                                {{ $item->party_code }}
                            </td>
                            <td style="min-width:150px; white-space: normal; word-break: break-word;">{{ $item->company }}
                            </td>
                         
                            <td>{{ $item->user ?? 'NA' }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->city1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->order_date)) }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->pt_date)) }}</td>
                            <td>
                                {{ $item->invoice_date ? date('d-m-Y', strtotime($item->invoice_date)) : 'No E-Invoice' }}
                            </td>
                            <td>{{ $item->invoice_id }}</td>
                            <td>{{ $item->part_no }}</td>
                            <td style="min-width:150px; white-space: normal; word-break: break-word;">{{ $item->name }}
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

                <tfoot>
                    <tr>
                        <th colspan="12" style="text-align: right">Total</th>
                        <th>{{ $total_qty }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>

                    </tr>
                </tfoot>


            </table>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("#customer_id, #user_id").select2();
        })
    </script>
@endsection
