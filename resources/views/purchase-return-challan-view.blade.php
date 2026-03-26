@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Purchase Order View</h4>
            </div>
            <div class="">

                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>
            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div style="border: solid 1px; padding:5px">

                <div style="display: flex; justify-content: space-between; color:black; font-weight: bold">
                    <div>
                        <img src="/logo/{{ $po_mst->img }}" alt="" style="width: 190px ;">

                    </div>
                    <div style="text-align: center">

                        <h4 style="font-size: 28px; font-weight: bolder">{{ $po_mst->company }}</h4>
                        <p style="font-size:14px">

                            {!! $po_mst->company_address !!}
                            <br>
                            <span style="text-transform: none"> Phone : {{ $po_mst->company_number }}
                                E-Mail : {{ $po_mst->company_email }} </span>
                            <br>
                            GST : {{ $po_mst->gst_no }} <br>

                        </p>
                    </div>
                    <div>
                        <div style=" text-align: center">

                            <div
                                style="border: solid 1px; padding: 3px 10px; box-shadow: 2px 2px 2px black; font-size:11px; text-align: center">
                                Purchase Return
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
                                <strong> Number </strong> <br>
                                <strong> Address </strong> <br>



                            </div>
                            <div style="margin-left: 20px">
                                {{ $po_mst->vendor }} <br>
                                {{ $po_mst->number }} <br>
                                {{ $po_mst->address }} <br>




                            </div>
                        </div>

                    </div>
                    <div>

                        <div style="display: flex">
                            <div>
                                <strong> Challan/Bill No : </strong> <br>
                                <strong>Challan/Bill Date </strong> <br>


                                <strong>Remarks. </strong>
                            </div>
                            <div style="margin-left: 20px">

                                GR-{{ $po_mst->id }} <br>

                                {{ date('d-m-Y', strtotime($po_mst->created_at)) }} <br>


                                {{ $po_mst->description }}

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
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Product Name</th>

                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Part Code.</th>
                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">HSN No.</th>


                        <th style="border: 1px solid gray;padding:1px 4px; font-size:11px">Qty</th>
                



                        @foreach ($po_det as $item)
                            <tr>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $sno++ }}</td>
                                <td
                                    style="; white-space: normal; word-wrap: break-word; border: 1px solid gray;padding:1px 4px; font-size:11px">
                                    {{ $item->product_name }}</td>

                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $item->part_no }}
                                </td>
                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px">{{ $item->hsn_code }}
                                </td>





                                <td style="border: 1px solid gray;padding:1px 4px; font-size:11px"> {{ $item->qty }}
                                    {{ $item->uom }}</td>

                          

                            </tr>
                        @endforeach




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

                            <h6 class="" style="font-size: 10px">For {{ $po_mst->company }}</h6>

                            <p class="mt-1">Authorized Signatory</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection
