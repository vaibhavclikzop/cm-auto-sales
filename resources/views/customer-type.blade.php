@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Customer Type</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Customer Type</h4>
            </div>
            <div class="">
                @if ($rolePermissions->where('permission_name', 'party_type')->where('view', 1)->where('create', 1)->isNotEmpty())
                    <button type="button" class="btn btn-primary add"><i class="fa fa-plus"></i> Add</button>
                @endif

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>

                        <th> Name</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>


                            <td>{{ $item->name }}</td>





                            <td>

                                @if ($rolePermissions->where('permission_name', 'party_type')->where('view', 1)->where('edit', 1)->isNotEmpty())
                                    <button class="btn btn-primary btn-sm edit" type="button"
                                        data-data="{{ @json_encode($item) }}"><i class="fa fa-pencil"
                                            aria-hidden="true"></i></button>
                                    {{-- <a href="/customer-type-price-list/{{ $item->id }}"
                                        class="btn btn-info btn-sm">Price List</a> --}}
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>



    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('saveCustomerType') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><span id="modal_name"> Add </span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">

                        <input type="hidden" name="id" id="id">
                        <div class="col-md-12 mt-2">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).on("click", ".edit", function() {
            var data = $(this).data("data");
            $.each(data, function(key, value) {
                $("input[name='" + key + "']").val(value)
                $("select[name='" + key + "']").val(value)
                $("textarea[name='" + key + "']").val(value)
            });
            $("#modal_name").text("Update");
            $("#exampleModal").modal("show");
        });


        $(".add").on("click", function() {
            $("#modal_name").text("Add");



            $("#id").val("");

            $("#exampleModal").modal("show");
        });


        $("#company_id").on("change", function() {
            $.ajax({
                url: "/getWarehouse",
                type: "POST",
                data: {
                    id: $(this).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    var html = "";
                    html += '<option value="">----Select Warehouse----</option>';
                    result.forEach(element => {

                        html += '<option value="' + element.id + '">' + element.name +
                            '</option>';
                    });
                    $("#warehouse_id").html(html)
                },
                error: function(result) {
                    console.log(result);
                }
            });

        })
    </script>
@endsection
