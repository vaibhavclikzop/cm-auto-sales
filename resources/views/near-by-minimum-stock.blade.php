@extends('layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            <div class="page-title">
                <h4>Current Stock</h4>
            </div>
            <form method="GET" {{ route('current-stock') }}>
                <div class="d-flex mt-4">
                    <div>

                        <label for="">Location</label>
                        <select name="location" id="" class="form-control">
                            <option value="">Select</option>
                            @foreach ($location as $item)
                                <option value="{{ $item->id }}"  {{ request('location') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach

                        </select>

                    </div>
                    <div>
                        <button class="btn btn-info mx-2 mt-4" type="submit">Search</button>
                    </div>

                </div>
            </form>
        </div>
        <div class="card-body" id="">

            @php
                $sno = 1;
            @endphp
            <table class="table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Product Name</th>
                        <th>Article No</th>
                        <th>Location</th>
                        <th>Min Stock</th>
                        <th>Stock</th>
                        <th>Update at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($current_stock as $item)
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->product }}</td>
                            <td>{{ $item->article_no }}</td>
                       
                            <td>{{ $item->location }}</td>
                            <td>{{ $item->min_stock }}</td>
                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->updated_at }}</td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>
@endsection
