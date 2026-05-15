@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Report Tally</title>
    @endpush
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            white-space: nowrap;
        }

        thead {
            background: #f2f2f2;
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Sale Report Tally</h4>
            </div>
            <div>
                <form action="" class="d-flex">
                    <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                    <input type="date" class="form-control mx-2" name="toDt" value="{{ request('toDt') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
            <div>
                <button id="exportToExcelTally" data-name="Tally Sale Report{{ Request('fromDt') }}{{ Request('toDt') }}"
                    class="btn btn-success float-end btn-sm mx-2">Export
                    to Excel</button>
            </div>

        </div>
        <div class="card-body table-responsive">
            <table class=" " id="exportTable">
                <thead>
                    <tr>
                        <th>Voucher Date</th>
                        <th>Voucher Type</th>
                        <th>Voucher No.</th>
                        <th>INVOICE NO</th>
                        <th>INVOICE DATE</th>
                        <th>Party Ledger Name</th>
                        <th>Party GSTIN/UIN</th>
                        <th>Taxable Amount</th>
                        <th>IGST Rate</th>
                        <th>OUTPUT IGST Amount</th>
                        <th>SGST Rate</th>
                        <th>OUTPUT SGST Amount</th>
                        <th>CGST Rate</th>
                        <th>OUTPUT CGST Amount</th>
                        <th>Total Amount</th>
                        <th>Narration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        @php

                            // GST RATE
                            $gstRate = 0;

                            if ($item->taxable_amount > 0) {
                                $gstRate = ($item->gst / $item->taxable_amount) * 100;
                            }

                            // IGST
                            $igstRate = $item->gst_type == 'IGST' ? number_format($gstRate, 2) : 0;

                            $igstAmount = $item->gst_type == 'IGST' ? number_format($item->gst, 2) : 0;

                            // CGST / SGST
                            $cgstRate = $item->gst_type == 'CGST' ? number_format($gstRate / 2, 2) : 0;

                            $sgstRate = $item->gst_type == 'CGST' ? number_format($gstRate / 2, 2) : 0;

                            $cgstAmount = $item->gst_type == 'CGST' ? number_format($item->gst / 2, 2) : 0;

                            $sgstAmount = $item->gst_type == 'CGST' ? number_format($item->gst / 2, 2) : 0;

                            // Grand Total
                            $grandTotal = $item->taxable_amount + $item->gst;

                        @endphp

                        <tr>

                            {{-- Voucher Date --}}
                            <td>
                                {{ date('d-m-Y', strtotime($item->invoice_convert_date)) }}
                            </td>

                            {{-- Voucher Type --}}
                            <td>
                                {{ $item->voucher_type }}
                            </td>

                            {{-- Voucher No --}}
                            <td>
                                {{ $item->invoice_id }}
                            </td>

                            {{-- Invoice No --}}
                            <td>
                                {{ $item->invoice_id}}
                            </td>

                            {{-- Invoice Date --}}
                            <td>
                                {{ date('d-m-Y', strtotime($item->invoice_convert_date)) }}
                            </td>

                            {{-- Party Ledger --}}
                            <td>
                                {{ $item->customer }}
                            </td>

                            {{-- GSTIN --}}
                            <td>
                                {{ $item->gst_no }}
                            </td>

                            {{-- Taxable Amount --}}
                            <td>
                                {{ number_format($item->taxable_amount, 2) }}
                            </td>

                            {{-- IGST Rate --}}
                            <td>
                                {{ $igstRate }}
                            </td>

                            {{-- IGST Amount --}}
                            <td>
                                {{ $igstAmount }}
                            </td>

                            {{-- SGST Rate --}}
                            <td>
                                {{ $sgstRate }}
                            </td>

                            {{-- SGST Amount --}}
                            <td>
                                {{ $sgstAmount }}
                            </td>

                            {{-- CGST Rate --}}
                            <td>
                                {{ $cgstRate }}
                            </td>

                            {{-- CGST Amount --}}
                            <td>
                                {{ $cgstAmount }}
                            </td>

                            {{-- Total Amount --}}
                            <td>
                                {{ number_format($grandTotal, 2) }}
                            </td>

                            {{-- Narration --}}
                            <td>
                                Sale Invoice #{{ $item->invoice_id }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    <script>
        $('#exportToExcelTally').click(function() {
            var name = $(this).data("name");

            var table = document.getElementById('exportTable');

            var ws = XLSX.utils.table_to_sheet(table, {
                raw: true
            });

            // Loop through all cells
            Object.keys(ws).forEach(function(cell) {
                if (cell[0] === '!') return;

                let value = ws[cell].v;

                // match dd/mm/yyyy
                if (typeof value === 'string' && /^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                    ws[cell].t = 's'; // force STRING
                    ws[cell].z = '@'; // text format
                }
            });

            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Report");

            XLSX.writeFile(wb, name + '.xlsx');
        });
    </script>
@endsection
