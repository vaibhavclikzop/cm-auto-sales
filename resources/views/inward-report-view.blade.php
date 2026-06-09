@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Material Receipt Note</h4>
            </div>
            <div>

                <form action="" method="GET">
                    Duplicate Copy <input type="checkbox" name="duplicate" class="mx-2" onclick="this.form.submit()"
                        @if (request('duplicate') == 'on') checked @endif>

                    <button type="button" class="btn btn-primary" onclick="printcontent()">
                        Print
                    </button>
                </form>
            </div>

        </div>
        <div class="card-body" id="PrintOrder">
            <div style="border: solid 1px; padding:5px">

                <div style="display: flex; justify-content: space-between; color:black; font-weight: bold">
                    <div>
                        <img src="/logo/{{ $stock_inward_mst->img }}" alt="" style="width: 190px ;">

                    </div>
                    <div style="text-align: center">

                        <h4 style="font-size: 28px; font-weight: bolder">{{ $stock_inward_mst->name }}</h4>
                        <p style="font-size:14px">

                            {!! $stock_inward_mst->address !!}
                            <br>
                            <span style="text-transform: none"> Phone : {{ $stock_inward_mst->number }}
                                E-Mail : {{ $stock_inward_mst->email }} </span>
                            <br>
                            GST : {{ $stock_inward_mst->gst_no }} <br>

                        </p>
                    </div>
                    <div>
                        <div style=" text-align: center">
                            <div style="margin-top:0px; font-size:11px">
                                @if (request('duplicate') == 'on')
                                    Duplicate Copy
                                @else
                                    Original Copy
                                @endif

                            </div>
                            <div
                                style="border: solid 1px; padding: 3px 10px; box-shadow: 2px 2px 2px black; font-size:11px; text-align: center">
                                Goods Receipt
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="padding: 0px; margin: 0">
                <div style="display: flex; justify-content: space-between; font-size: 10px">
                    <div>
                        <h6 style="font-size:14px">Vendor Details</h6>
                        <div style="display: flex">
                            <div>
                                <strong> Vendor Name </strong> <br>
                                <strong>Warehouse </strong> <br>
                                <strong>In Location </strong> <br>


                            </div>
                            <div style="margin-left: 20px">
                                {{ $stock_inward_mst->vendor }} <br>

                                {{ $stock_inward_mst->warehouse }}<br>
                                {{ $stock_inward_mst->location }}<br>


                            </div>
                        </div>

                    </div>
                    <div>

                        <div style="display: flex">
                            <div>
                                <strong> Challan/Bill No : </strong> <br>
                                <strong> Invoice No : </strong> <br>
                                <strong>Challan/Bill Date </strong> <br>
                                <strong>R.M Date </strong> <br>

                                <strong>Remarks. </strong>
                            </div>
                            <div style="margin-left: 20px">

                                GR-{{ $stock_inward_mst->id }} <br>
                                {{ $stock_inward_mst->inv }} <br>

                                {{ date('d-m-Y', strtotime($stock_inward_mst->invoice_date)) }} <br>
                                {{ date('d-m-Y', strtotime($stock_inward_mst->received_material_date)) }} <br>

                                {{ $stock_inward_mst->description }}

                            </div>
                        </div>

                    </div>

                </div>



                <div class="">
                    <hr>

                    @php
                        $sno = 1;
                    @endphp
                    <table class="" style="width: 100%">

                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">S.No</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Brand</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Product Name</th>

                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Part Code.</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Product Location</th>


                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Qty</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Rate/Pcs</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Taxable Amt.</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">GST</th>

                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Total</th>


                        @php
                            $total_qty = 0;
                            $grand_total = 0;
                            $taxable = 0;
                            $totalRate = 0;
                            $totalTaxable = 0;
                            $totalGST = 0;
                        @endphp
                        @foreach ($stock_inward_det as $item)
                            @php
                                $total_qty += $item->qty;
                             
                                $rate = round($item->price, 2);

                                $taxable = round($rate * $item->qty, 2);

                                $gst = round(( ($item->price/100*$item->gst)) * $item->qty, 2);
                                $totalRate += $rate;
                                $totalTaxable += $taxable;
                                $totalGST += $gst;
                                   $grand_total += $item->qty * $item->price+$gst;
                            @endphp
                            <tr>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $sno++ }}</td>
                                <td
                                    style="; white-space: normal; word-wrap: break-word; border: 1px solid gray;padding:1px 4px; font-size:11px">
                                    {{ $item->brand }}</td>
                                <td
                                    style="; white-space: normal; word-wrap: break-word; border: 1px solid gray;padding:1px 4px; font-size:11px">
                                    {{ $item->product_name }}</td>

                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $item->part_no }}
                                </td>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                    {{ $item->product_location }}
                                </td>





                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px"> {{ $item->qty }}
                                    {{ $item->uom }}</td>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px"> {{ $rate }}
                                </td>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px"> {{ $taxable }}
                                </td>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $item->gst }} %
                                    <br> {{ $gst }}
                                </td>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                    {{ $item->qty * $item->price+$gst }}
                                </td>

                            </tr>
                        @endforeach
                        <tr>
                            <th colspan="5"
                                style="border: 1px solid gray;padding:1px 4px; font-size:11px; text-align: right">Total
                            </th>
                            <th colspan="2" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $total_qty }}</th>
                            {{-- <th colspan="" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $totalRate }}</th> --}}
                            <th colspan="" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $totalTaxable }}</th>
                            <th colspan="" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $totalGST }}</th>
                            <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $grand_total }}</th>
                        </tr>
                        <tr>
                            <th colspan="8"
                                style="border: 1px solid gray;padding:1px 4px; font-size:11px; text-align: right">Discount
                            </th>
                            <th colspan="" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $stock_inward_mst->discount }} % </th>


                            <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ ($grand_total / 100) * $stock_inward_mst->discount }}</th>
                        </tr>
                        <tr>
                            <th colspan="8"
                                style="border: 1px solid gray;padding:1px 4px; font-size:11px; text-align: right">Adjust
                                Amount
                            </th>
                            <th colspan="" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $stock_inward_mst->adj_amt_type ?? 'NA' }} </th>


                            <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                {{ $stock_inward_mst->adj_amt }}</th>
                        </tr>

                        <tr>
                            <th colspan="8"
                                style="border: 1px solid gray;padding:1px 4px; font-size:11px; text-align: right">Total
                                Amount
                            </th>
                            <th colspan="" style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                            </th>


                            <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">
                                @php
                                    $total = $grand_total - ($grand_total / 100) * $stock_inward_mst->discount;
                                    if ($stock_inward_mst->adj_amt_type == 'credit') {
                                        $total = $total + $stock_inward_mst->adj_amt;
                                    } elseif ($stock_inward_mst->adj_amt_type == 'debit') {
                                        $total = $total - $stock_inward_mst->adj_amt;
                                    }
                                @endphp
                                {{ $total }}</th>
                        </tr>




                    </table>
                </div>
                <div class="d-flex mt-1 justify-content-between">
                    <div style="border: solid 1px black; width: 40%; padding:10px; font-size: 10px">
                        <p>
                            Material Report <br>
                            Material OK <br>
                            Qty. <br> <br>
                            Material Rejection <br>
                            Qty.
                        </p>
                    </div>
                    <div style="border: solid 1px black; width: 60%; padding:10px; font-size: 10px">
                        <div style="text-align: center">


                            <p>Receiver Signature : </p>
                            <hr>

                            <h6 class="" style="font-size: 10px">For {{ $stock_inward_mst->name }}</h6>

                            <p class="mt-1">Authorized Signatory</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>


    </div>

    </div>
@endsection
