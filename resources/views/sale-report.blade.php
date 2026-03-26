@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Sale Report</h4>
            </div>
            <div class="">



                <form action="">
                    <div class="row">


                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label for="">City</label>
                            <select name="city" id="city" class="form-control" onchange="this.form.submit()">
                                <option value="">city</option>
                                @foreach ($city as $item)
                                    <option value="{{ $item->city1 }}"
                                        {{ request('city') == $item->city1 ? 'selected' : '' }}>{{ $item->city1 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
                        <th>Sno</th>

                        <th>Consignee/Buyer</th>

                        <th>INVOICE NO</th>
                        <th>INVOICE DATE</th>

                        <th style="text-align:right;">Total </th>
                        <th style="text-align:right;">Discount </th>
                        <th style="text-align:right;">Spec. Disc </th>
                        <th style="text-align:right;">Taxable </th>
                        <th style="text-align:right;">OUTPUT IGST</th>
                        <th style="text-align:right;">OUTPUT SGST</th>
                        <th style="text-align:right;">OUTPUT CGST</th>
                        <th style="text-align:right;">Grand Total </th>

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
                                {{ $item->customer }}
                            </td>
                            <td>{{ $item->invoice_id }}</td>

                            <td>{{ date('d-m-Y', strtotime($item->invoice_convert_date)) }}</td>

                            <td>{{ $item->total }}</td>
                            <td>{{ $item->discount }}</td>
                            <td>{{ $item->discount2 }}</td>
                            <td>{{ $item->taxable_amount }}</td>

                            <td>
                                @if ($item->gst_type == 'IGST')
                                    {{ $item->gst ?? 0 }}
                                @else
                                    0
                                @endif

                            </td>
                            <td>
                                @if ($item->gst_type == 'CGST')
                                    {{ $item->gst / 2 ?? 0 }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>
                                @if ($item->gst_type == 'CGST')
                                    {{ $item->gst / 2 ?? 0 }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ $item->taxable_amount + $item->gst }}</td>
                        </tr>
                    @endforeach
                </tbody>


            </table>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $("#customer_id").select2();
            $("#city").select2();



            const graphData = @json($graphData);

            const labels = graphData.map(i => i.month);
            const values = graphData.map(i => i.total);

            var options = {
                series: [{
                    name: "Amount",
                    data: values
                }],
                chart: {
                    height: 250,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                title: {
                    text: 'Sale Report Month Wise',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: labels,
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        })
    </script>
@endsection
