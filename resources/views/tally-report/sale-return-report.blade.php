@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Sale Return Report Tally</title>
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
                <h4>Sale Return Report Tally</h4>
            </div>
            <div>
                <form action="" class="d-flex">
                    <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                    <input type="date" class="form-control mx-2" name="toDt" value="{{ request('toDt') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
            <div>
                <button id="exportToExcelTally" data-name="Sale Return Report{{ Request('fromDt') }}{{ Request('toDt') }}"
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
                        <th>Original INVOICE NO</th>
                        <th>Original INVOICE DATE</th>
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
                        <tr>

                            <td>
                                {{ \Carbon\Carbon::parse($item->voucher_date)->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $item->voucher_type }}
                            </td>

                            <td>
                                {{ $item->voucher_no }}
                            </td>

                            <td>
                                {{ $item->original_invoice_no }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->original_invoice_date)->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $item->party_ledger_name }}
                            </td>

                            <td>
                                {{ $item->party_gstin }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->taxable_amount, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->igst_rate, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->output_igst_amount, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->sgst_rate, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->output_sgst_amount, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->cgst_rate, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->output_cgst_amount, 2) }}
                            </td>

                            <td style="text-align: right;">
                                {{ number_format($item->total_amount, 2) }}
                            </td>

                            <td>
                                {{ $item->narration }}
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
