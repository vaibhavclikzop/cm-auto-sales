@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Scrap Stock</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Scrap Stock</h4>
            </div>
            <div class="">


                <a class="btn btn-primary" href="add-scrap-stock"><i class="fa fa-plus"></i> Add Scrap Stock</a>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Image</th>
                      
                        <th> Product</th>
                        <th>Location</th>
                        <th>Qty</th>
                        <th>Scrap Qty</th>
                        <th>Defective Qty</th>
                        <th>Inward Qty</th>
                      
                        <th>Update at</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($scrap as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td><a href="/scrap files/{{ $item->file }}" target="_blank"><img
                                        src="/scrap files/{{ $item->file }}" width="60px"> </a></td>
                        
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->scrap_qty }}</td>
                            <td>{{ $item->defective_qty }}</td>
                            <td>{{ $item->inward_qty }}</td>
                          
                            <td>{{ $item->updated_at }}</td>
                           
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>



    <form action="{{ route('/') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="modalId">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                           Sold Amount
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <label for="">Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
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
        $(document).on("click", ".sold", function() {
            $("#id").val($(this).val())
            $("#modalId").modal("show")
        });
    </script>
@endsection
