@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>PI View</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>PI View</h4>
            </div>
            <div class="">


                <button type="button" onclick="printcontent()" class="btn btn-primary"><i class="fa fa-print"
                        aria-hidden="true"></i> Print</button>


            </div>
        </div>
        <div class="card-body" id="PrintOrder">
            <div class="text-center">
                <img src="/logo/{{ $setting->img }}" width="180px">
            </div>

            <div style="display: flex; justify-content: space-between; border: solid 1px; padding: 8px;">
                <div>
                    <h3>{{ $setting->company_name }}</h3>
                    <p>{!! $setting->address !!}
                        <br>
                        E-Mail : {{ $setting->email }} <br>
                        Phone : {{ $setting->number }} <br>
                        GST : {{ $setting->gst_no }}
                        Delivery Date : {{ $setting->gst_no }}

                    </p>
                    <h4>PI</h4>

                </div>


                <div>
                    <div style="text-align: right;">
                        <h6>Order ID : {{ $order_mst->order_id }}</h6>
                        <h4>{{ $order_mst->customer_name }}</h4>
                        <p>
                            {{ $order_mst->address }}<br>
                            {{ $order_mst->city }}, {{ $order_mst->state }}, {{ $order_mst->pincode }}<br>
                            {{ $order_mst->email }}<br>
                            {{ $order_mst->number }}<br>
                            {{ $order_mst->gst }}<br>
                            {{ $order_mst->delivery_date }}

                        </p>


                    </div>
                </div>
            </div>
            <div class="">
                <hr>
                <h6>Products</h6>
                @php
                    $sno = 1;
                @endphp
                <table class="table">
                    <thead>
                        <th>S.No</th>
                        <th>Product</th>
                        <th>Article No</th>
                        <th>Qty</th>
                    
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($order_det as $item)
                            @php
                                $total += $item->price * $item->qty - $item->discount;
                            @endphp
                            <tr>
                                <td>{{ $sno++ }}</td>
                                <td>{{ $item->product }}</td>
                                <td>{{ $item->article_no }}</td>
                                <td>{{ $item->qty }}</td>
                       
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->discount }}</td>
                                <td>{{ $item->price * $item->qty - $item->discount }}</td>
                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">Total</th>
                            <th>{{ $total }}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            <div class="d-flex mt-4 justify-content-between">
                <div>
                    <p><b><u><i>Terms & Conditions</i></u></b></p>
                    <ol style="list-style:number;">


                    </ol>
                </div>
                <div>
                    <h6 class="float-end">For {{ $setting->company_name }}</h6>

                    <p class="mt-5">Authorized Signatory</p>
                </div>

            </div>




        </div>

    </div>
@endsection
