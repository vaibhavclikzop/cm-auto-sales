@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Customer Wise Sale Report</h4>
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



            const graphData = @json($pieData);

            const labels = graphData.map(i => i.customer);
            const values = graphData.map(i => i.total);

            var options = {
                series: values,
                chart: {
                    width: 550,
                    type: 'pie',
                },
                labels: labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();


        })
    </script>
@endsection
