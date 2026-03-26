@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Dispatch Plan </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Dispatch Plan</h4>
            </div>
            <div>

                <button class="btn btn-info" type="button" id="finalDispatch"> <i class="fa fa-car" aria-hidden="true"></i>
                    Final Dispatch</button>
                <button class="btn btn-primary" type="button" id="btnAllocate"><i class="fa fa-truck" aria-hidden="true"></i>
                    Allocate Vehicle</button>
                <button onclick="downloadCSV()" type="button" class="btn btn-secondary">Export to CSV</button>

            </div>

        </div>
        <div class="card-body">
            <table class="table  ">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th><input type="checkbox" id="checks"></th>
                    <th>Invoice ID </th>
                        <th>Party Code </th>
                        <th>Customer </th>
                        <th>Address </th>
                        <th>City </th>
    
                        <th>Invoice Date </th>
                        <th>Item Total</th>
                        <th>Total Qty</th>

                        <th>User </th>
                        <th>MAP </th>
                        <th>Action </th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>
                                <input type="checkbox" class="checks" name="checks[]" value="{{ $item->id }}">
                            </td>
                                      <td>{{ $item->invoice_id }}</td>
                            <td>{{ $item->party_code }}</td>
                            <td>{{ $item->customer }}</td>
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->city }}</td>
        


                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->item_total }}</td>
                            <td>{{ $item->total_qty }}</td>

                            <td>{{ $item->user }}</td>
                            <td>
                                <a href="https://www.google.com/maps?q={{ $item->coordinates }}" target="_blank">
                                    <i class="fa fa-street-view" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                <a href="/invoice-view/{{ $item->id }}" class="btn btn-primary btn-sm"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>


                        </tr>
                    @endforeach
                </tbody>

            </table>


        </div>
        <div class="card-body">
            <h5>Dispatch Plan</h5>

            <table class="table" id="myTable">


                <thead class="">
                    <tr>
                        <th>S.No</th>
                        <th>Vehicle Name</th>
                        <th>Vehicle No</th>
                        <th>Vehicle 2</th>
                      <th>Invoice ID</th>
                        <th>Party Code</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>City</th>
               
                        <th>Invoice Date</th>
                        <th>Dispatch Date</th>
                        <th>No. of Box</th>
                        <th>Item Total</th>
                        <th>Total Qty</th>
                        <th>User</th>
                        <th>MAP</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>


                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($dispatch_plan as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>
                                @if ($item->vehicle_name)
                                    {{ $item->vehicle_name }}
                                @else
                                    {{ $item->transport_name }}
                                @endif


                            </td>
                            <td>
                                @if ($item->vehicle_no)
                                    {{ $item->vehicle_no }}
                                @else
                                    {{ $item->tracking_no }}
                                @endif





                            </td>
                            <td>{{ $item->vehicle_name2 }} <br>{{ $item->vehicle_no2 }}</td>
                                <td>{{ $item->invoice_id }}</td>
                            <td> {{ $item->party_code }} </td>
                            <td>{{ $item->customer }} </td>
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->city }}</td>
               
                            <td>{{ $item->invoice_date }}</td>
                            <td>{{ $item->transport_date }}</td>
                            <td>{{ $item->no_of_box }}</td>
                            <td>{{ $item->item_total }}</td>
                            <td>{{ $item->total_qty }}</td>
                            <td>{{ $item->user }}</td>
                            <td>
                                <a href="https://www.google.com/maps?q={{ $item->coordinates }}" target="_blank">
                                    <i class="fa fa-street-view" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-danger">Unverified</span>
                            </td>
                            <td>
                                <a href="/invoice-view/{{ $item->id }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>



        </div>

    </div>


    <form action="{{ route('updateDispatchPlan') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="vehicleAllocate" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Allocate Vehicle
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" id="ticketIDs" name="ids" hidden>
                                <label for="">Vehicle </label>
                                <select name="transport_id" id="transport_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($mot as $item)
                                        <option value="{{ $item->id }}">{{ $item->vehicle_name }} /
                                            {{ $item->vehicle_no }} </option>
                                    @endforeach
                                    <option value="0">Other</option>
                                </select>
                            </div>

                            <div class="col-md-6 mt-2 other">
                                <label for="">Transport Name</label>
                                <input type="text" name="transport_name" class="form-control">
                            </div>
                            <div class="col-md-6 mt-2 other">
                                <label for="">Tracking No</label>
                                <input type="text" name="tracking_no" class="form-control">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label for="">Date</label>
                                <input type="date" name="date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label for="">No. of Box</label>
                                <input type="number" name="no_of_box" class="form-control">
                            </div>

                            <div class="col-md-12 mt-2">
                                <label for="">Second Vehicle</label>
                                <input type="checkbox" id="second_vehicle">
                            </div>
                            <div class="col-md-12" id="second_mot">

                                <label for="">Vehicle </label>
                                <select name="transport_id2" id="transport_id2" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($mot as $item)
                                        <option value="{{ $item->id }}">{{ $item->vehicle_name }} /
                                            {{ $item->vehicle_no }} </option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label for="">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <form action="{{ route('finalDispatchPlan') }}" method="post" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="finalModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Final Dispatch
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <script>
        $("#checks").on("click", function() {
            if ($(this).prop("checked")) {
                $(".checks").prop("checked", true)
            } else {
                $(".checks").prop("checked", false)
            }
        });
        $("#second_mot").hide();
        $("#second_vehicle").on("click", function() {
            if ($(this).prop("checked")) {
                $("#second_mot").show(500);
            } else {
                $("#second_mot").hide(500);
            }

        })

        $("#btnAllocate").on("click", function() {

            let checkedValues = $(".checks:checked")
                .map(function() {
                    return this.value;
                })
                .get()
                .join(", ");
            if (!checkedValues) {
                toastr.error("Select at least one ticket")
                return
            }
            $("#ticketIDs").val(checkedValues)
            $("#vehicleAllocate").modal("show")
        });

        $(".other").hide();
        $("#transport_id").on("change", function() {
            if ($(this).val() == 0) {
                $(".other").show(500);
            } else {
                $(".other").hide(500);
            }
        });


        $("#finalDispatch").on("click", function() {
            $("#finalModal").modal("show")
        });

        function downloadCSV() {
            let table = document.getElementById("myTable");
            let rows = table.querySelectorAll("tr");
            let csv = [];

            rows.forEach(row => {
                let cols = row.querySelectorAll("td, th");
                let rowData = Array.from(cols).map(col => `"${col.innerText}"`).join(",");
                csv.push(rowData);
            });

            let csvContent = csv.join("\n");
            let blob = new Blob([csvContent], {
                type: "text/csv;charset=utf-8;"
            });
            let link = document.createElement("a");

            link.setAttribute("href", URL.createObjectURL(blob));
            link.setAttribute("download", "dispatch.csv");
            link.style.display = "none";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
