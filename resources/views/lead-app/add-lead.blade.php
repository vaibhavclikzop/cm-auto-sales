@extends('lead-app.layouts.main')
@section('main-section')
    <div class="card">
        <div class="card-header">
            Add Lead
        </div>
        <div class="card-body">
            <form class="needs-validation" novalidate method="POST" action="{{ route('lead-app.saveLead') }}">
                @csrf
                <div class="row">
                    <input type="hidden" name="id" id="id">


                    <div class="col-md-12 ">
                        <label for="">Name</label>
                        <input type="text" name="name" id="name" class="form-control">

                    </div>

                    <div class="col-md-12  mt-3">
                        <label for="">Number</label>
                        <input type="number" name="number" id="number" class="form-control" required>

                    </div>

                    <div class="col-md-12  mt-3">
                        <label for="">Email</label>
                        <input type="email" name="email" id="email" class="form-control">

                    </div>



                    <div class="col-md-12 mt-3">
                        <label for="">Classification</label>
                        <select name="classification" id="classification" class="form-control">
                            <option value="">Select</option>
                            <option value="Hot">Hot</option>
                            <option value="Cold">Cold</option>
                            <option value="Warm">Warm</option>

                        </select>

                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select</option>
                            @foreach ($Leadstatus as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-6 mt-3">
                        <label for="">Remind Date</label>
                        <input type="date" name="remind_date" id="remind_date" class="form-control">

                    </div>
                    <div class="col-6 mt-3">
                        <label for="">Remind Time</label>
                        <input type="time" name="remind_time" id="remind_time" class="form-control">

                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" required></textarea>

                    </div>
                    <div class="col-md-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary w-100" id="">Save Lead</button>
                    </div>



                </div>

            </form>
        </div>

    </div>
@endsection
