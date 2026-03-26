@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Order vs Stock Report</h4>
            </div>
            <div class="">



                <form action="">
                    <div class="row">




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
                        <th>Order</th>
                        <th>CO Date</th>
                        <th>Party Type</th>
                        <th>Party Code</th>
                        <th>Party Name</th>
                        <th>Part Code</th>
                        <th>Product</th>
                        <th>Product Loc</th>
                        <th>Ordered Qty</th>
                        <th>Picked Qty</th>
                        <th>Invoice Qty</th>
                        <th>Pending Qty</th>
                        <th>Cancel Qty</th>
                        <th>Scrap Qty</th>
                        <th>Stock Qty</th>
                        <th>Selling Price</th>
                     
                    </tr>

                </thead>
                <tbody>
                    @php
                        $sno = 1;
                        $ordered_qty = 0;
                        $invoice_qty = 0;
                        $pending_qty = 0;
                    @endphp
                    @foreach ($data as $item)
                        @php

                            $ordered_qty += $item->ordered_qty;
                            $invoice_qty += $item->invoice_qty;

                            $pending_qty += $item->pending_qty;

                        @endphp
                        <tr>

                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->party_code }}</td>
                            <td style="white-space: normal; word-wrap: break-word;">
                                {{ $item->customer }}
                            </td>

                            <td>{{ $item->part_no }}</td>
                            <td style="white-space: normal; word-wrap: break-word;">
                                {{ $item->name }}
                            </td>

                            <td>{{ $item->product_location }}</td>

                            <td>{{ $item->ordered_qty }}</td>
                            <td>{{ $item->picked_qty }}</td>
                            <td>{{ $item->invoice_qty }}</td>
                            <td>

                                @if ($item->status == 'complete')
                                  0
                                @else
                                  {{ $item->pending_qty }}
                                @endif


                            </td>
                            <td>
                                {{$item->cancel_qty}}
                            </td>
                              <td>

                                @if ($item->status != 'complete')
                                  0
                                @else
                                  {{ $item->pending_qty }}
                                @endif


                            </td>
                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->sale_price }}</td>
                   
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="9" style="text-align: right">Total Qty </th>
                        <th>{{ $ordered_qty }}</th>
                        <th>{{ $invoice_qty }}</th>
                        <th>{{ $pending_qty }}</th>
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

        })
    </script>
@endsection
