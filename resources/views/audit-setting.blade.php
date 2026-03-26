@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Audit Setting</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Audit Setting</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('SaveAuditReport') }}" class="row needs-validation" novalidate
                enctype="multipart/form-data">
                @csrf

                <div class="col-md-4">
                    <label for="">Select Location</label>
                    <select class="form-control" name="location_id" id="location_id" required>
                        <option value="">Select Location</option>
                        @foreach ($location as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-md-8">
                    <label for="">Remarks (If any)</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Enter remarks if any">

                </div>
                <div class="col-md-12 mt-3">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th><input type="checkbox" id="all_check"></th>
                                <th>Name</th>
                                <th>Article No</th>
                                <th>Current Stock</th>
                                <th>Updated at</th>
                            </tr>

                        </thead>
                        <tbody id="prod_list">

                        </tbody>

                    </table>
                </div>

                <div class="col-md-12 text-center mt-3">
                    <button type="submit" class="btn btn-primary">Submit</button>

                </div>

            </form>
        </div>

    </div>

    <script>
        $("#location_id").on("change", function() {

            $.ajax({
                url: "/GetCSProducts",
                type: "POST",
                data: {
                    id: $(this).val(),

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(result) {

                    var html = "";
                    var sno = 1;
                    result.forEach(element => {
                        html += `
                                    <tr>
                                        <td>${sno++}</td>
                                        <td> <input type="checkbox" class="all_check" name="check[]" value="${element.id}"> </td>
                                        <td>${element.name}</td>
                                        <td>${element.article_no}</td>
                                        <td>${element.stock}</td>
                                        <td>${element.updated_at}</td>
                                    </tr>
                                `;

                    });
                    $("#prod_list").html(html)
                },
                complete: function() {
                    $("#loader").hide();
                },
                error: function(result) {
                    toastr.error(result.responseJSON.message);
                }
            });
        });
        $("#all_check").on("click",function(){
            if($(this).prop("checked")){
                $(".all_check").prop("checked",true)
            }else{
                $(".all_check").prop("checked",false)
            }
        })
    </script>
@endsection
