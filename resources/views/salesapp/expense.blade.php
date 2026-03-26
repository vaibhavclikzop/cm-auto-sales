@extends('salesapp.layouts.main')
@section('main-section')
    @push('title')
        <title>Expense </title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Expense</h4>
            </div>

        </div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>File</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Status</th>

                    </tr>
                </thead>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td> <a href="/expense-file/{{ $item->file }}" download="/expense-file/{{ $item->file }}">File</a> </td>
                        <td>{{$item->category}}</td>
                        <td>{{$item->name}} <br> {{$item->description}} </td>
                        <td>{{$item->amount}}</td>
                        <td>{{$item->status}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
