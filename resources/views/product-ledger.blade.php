@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Product Ledger </h4>
            </div>
            <div class="">



                <form action="">
                    <div class="row">
                        <div class="col-3">
                            <label for=""> Products </label>
                            <select name="product_id" id="product_id" class="form-control" onchange="this.form.submit();">
                                <option value="">Select</option>
                                @foreach ($products as $item)
                                    <option value="{{$item->id}}" {{request("product_id") == $item->id ? "selected" : ""}}>{{$item->part_no}} / {{$item->name}} </option>
                                @endforeach
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
                        <th>Type</th>
                        <th>Party</th>
                        <th>Invoice ID</th>
                        <th>Invoice Date</th>
                        <th>Location</th>
                        <th>Part Code</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Balance Qty</th>

         

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($ledger as $item)
                    <tr>
                        <td>{{$sno++}}</td>
                        <td>{{$item->type}}</td>
                        <td>{{$item->party}}</td>
                        <td>{{$item->invoice_id}}</td>
                        <td>{{date("d-m-Y",strtotime($item->created_at)) }}</td>
                        <td>{{$item->location}}</td>
                        <td>{{$item->part_code}}</td>
                        <td style="white-space: normal; word-wrap: break-word;">{{$item->product}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{$item->balance_qty}}</td>
                    </tr>
                        
                    @endforeach

                </tbody>



            </table>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("#product_id").select2();
        })
    </script>
@endsection
