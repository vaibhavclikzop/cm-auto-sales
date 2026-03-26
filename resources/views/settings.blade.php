@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Settings</title>
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Settings</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('SaveSettings') }}" class="row needs-validation" novalidate
                enctype="multipart/form-data">
                @csrf
                <div class="col-md-12 mb-3">
                    <img src="/logo/{{ $settings->img }}" alt="" width="280px">

                </div>
                <div class="col-md-3">

                    <label for="">Image</label>
                    <input type="file" name="image" class="form-control">

                </div>
                <div class="col-md-3">
                    <label for="">Image Width (in Pixels)</label>
                    <input type="number" name="img_width" class="form-control" value="{{ $settings->img_width }}">

                </div>
                <div class="col-md-6">
                    <label for="">Company Name</label>
                    <input type="text" class="form-control" name="company_name" value="{{ $settings->company_name }}">

                </div>
                <div class="col-md-12 mt-2">
                    <label for="">Address</label>
                    <textarea name="address" id="" class="form-control">{{ $settings->address }}</textarea>

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">Contact Person Name</label>
                    <input type="text" class="form-control" name="contact_person"
                        value="{{ $settings->contact_person }}">

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">Number</label>
                    <input type="number" class="form-control" name="number" value="{{ $settings->number }}">

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $settings->email }}">

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">GST</label>
                    <input type="text" class="form-control" name="gst_no" value="{{ $settings->gst_no }}">

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">Invoice Prefix</label>
                    <input type="text" class="form-control" name="invoice_prefix"
                        value="{{ $settings->invoice_prefix }}">

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">Invoice No</label>
                    <input type="text" class="form-control" name="invoice_no" value="{{ $settings->invoice_no }}">

                </div>
                <div class="col-md-3 mt-2">
                    <label for="">Transaction Password</label>
                    <input type="text" class="form-control" name="transaction_password"
                        value="{{ $settings->transaction_password }}">

                </div>
                {{-- <div class="col-md-12 mt-2">
                    <label for="">Terms and conditions</label>
                 <textarea name="terms_conditions" id="terms_conditions" class="form-control">{{$settings->terms_conditions}}</textarea>

                </div> --}}
                <div class="col-md-12 text-center mt-4">
                    <button class="btn btn-primary" type="submit">Update</button>

                </div>

            </form>
        </div>

    </div>
    <script>
        var terms_conditions = new RichTextEditor("#terms_conditions");
    </script>
@endsection
