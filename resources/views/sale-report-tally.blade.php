@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Report Tally</h4>
            </div>
            <div class="">

                <form action="" class="d-flex">
                    <div>
                        <label for=""> From Date </label>
                        <input type="date" name="fromDate" class="form-control" id=""
                            value="{{ request('fromDate') }}" onchange="this.form.submit()">
                    </div>
                    <div>
                        <label for=""> To Date </label>
                        <input type="date" name="toDate" class="form-control" id=""
                            value="{{ request('toDate') }}" onchange="this.form.submit()">
                    </div>
                </form>


            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Consignee/Buyer</th>
                        <th>Voucher Type</th>
                        <th>Voucher No.</th>
                        <th>Voucher Ref. No.</th>
                        <th>INVOICE NO</th>
                        <th>INVOICE DATE</th>
                        <th>GSTIN/UIN</th>
                        <th style="text-align:right;">Taxable </th>
                        <th style="text-align:right;">Total </th>
                        <th style="text-align:right;">OUTPUT IGST</th>
                        <th style="text-align:right;">OUTPUT SGST</th>
                        <th style="text-align:right;">OUTPUT CGST</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->date)) }}</td>
                            <td>{{ $item->customer }}</td>
                            <td>{{ $item->voucher_type }}</td>
                            <td>{{ $item->voucher_no }}</td>
                            <td>-</td>
                            <td>{{ $item->invoice_id }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->invoice_date)) }}</td>
                            <td>{{ $item->gst_no }}</td>
                            <td>{{ $item->taxable_amount }}</td>
                            <td>{{ $item->taxable_amount+ $item->gst }}</td>
                            <td>
                                @if ($item->gst_type == 'IGST')
                                    {{ $item->gst ?? 0 }}
                                @else
                                    0
                                @endif

                            </td>
                            <td>
                                  @if ($item->gst_type == 'CGST')
                                    {{ $item->gst/2 ?? 0 }}
                                @else
                                    0
                                @endif
                            </td>
                            <td> @if ($item->gst_type == 'CGST')
                                    {{ $item->gst/2 ?? 0 }}
                                @else
                                    0
                                @endif</td>
                        </tr>
                    @endforeach
                </tbody>


            </table>
        </div>

    </div>
@endsection
