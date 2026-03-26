@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Party Type</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Customer</h4>
            </div>
            <div class="">
                <form action="{{ route('updateCustomerPrice0') }}" method="POST" class="needs-validation d-flex" novalidate>
                    @csrf
                    <input type="hidden" name="customer_type_id" value="{{request("id")}}" hidden>
                    <div>
                        <label for="">Price Type</label>
                        <select name="price_type" id="" class="form-control">
                            <option value="percentage">Percentage</option>
                            <option value="price">Price</option>

                        </select>
                    </div>
                    <div class="mx-2">
                        <label for=""> Type</label>
                        <select name="type" id="" class="form-control">
                            <option value="increment">increment</option>
                            <option value="decrement">decrement</option>

                        </select>
                    </div>
                    <div>
                        <label for=""> Value</label>
                        <input type="number" step="0.01" name="value" class="form-control" required>
                    </div>
                    <div class="mx-2">
                        <button class="btn btn-success mt-4" type="submit">Update</button>
                    </div>
                </form>

            </div>
            <div>

            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.no</th>

                        <th> Part No</th>
                        <th> Product Name</th>
                        <th> Price</th>
                        <th> Action</th>


                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;
                    @endphp
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>


                            <td>{{ $item->product->part_no }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->price }}</td>
                            <td>
                                <input type="number" step="0.01" value="{{ $item->price }}"
                                    data-id="{{ $item->id }}" class="form-control changePrice">
                            </td>







                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

    <script>
        $(document).on("keyup", ".changePrice", function() {
            let id = $(this).data("id");
            let price = $(this).val();
            $.ajax({
                url: "/updateCustomerPrice",
                type: "POST",
                data: {
                    id: id,
                    price: price,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {

                },
                error: function(result) {
                    console.log(result);
                }
            });

        })
    </script>
@endsection
