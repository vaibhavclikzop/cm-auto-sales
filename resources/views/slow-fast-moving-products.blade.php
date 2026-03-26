@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header ">
            <div class="page-title">
                <h4>Slow Fast Moving Products </h4>
            </div>
            <div class="">



                <form action="">
                    <div class="row">




                        <div class="col-2">
                            <label for=""> Type </label>
                            <select name="type" id="type" class="form-control" required onchange="this.form.submit()">
                                <option value="">Select</option>
                                <option value="asc" {{ request('type') == 'asc' ? 'selected' : '' }}>Slow</option>
                                <option value="desc" {{ request('type') == 'desc' ? 'selected' : '' }}>Fast</option>

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
                        <th>Part Code</th>
                        <th>Product</th>
                        <th>Qty</th>


                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->part_no }}</td>
                            <td style="white-space: normal; word-break: break-word;">{{ $item->name }}</td>

                            <td>{{ $item->qty }}</td>

                        </tr>
                    @endforeach
                </tbody>


            </table>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("#type").select2();
        })
    </script>
@endsection
