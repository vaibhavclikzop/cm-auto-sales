@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">
        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="position-fixed bottom-0 end-0 z-index-5 m-2 pb-ios"><a href="#"
                        class="btn btn-lg shadow btn-square btn-theme rounded-circle"><i data-feather="edit"
                            class="align-middle"></i></a></div>
                <div class="container mt-3" id="main-content">
                    <div class="coverimg height-140 w-100 rounded position-relative bg-secondary"></div>
                    <div class="row z-index-1 mb-4 mt--75 position-relative">
                        <div class="col-12 col-md-auto text-center">
                            <figure class="avatar avatar-120 rounded-circle coverimg mx-3 mb-2">
                                <i class="fa fa-user-circle text-dark" style="font-size: 8rem" aria-hidden="true"></i>

                            </figure>
                            <p class="h5"><span class="position-relative"> {{ $data->name }} <span
                                        class="position-absolute top-50 start-100 translate-middle p-1 bg-success rounded-circle mx-2"></span></span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 col-xxl-8">
                            <div class="row gx-3">
                                <div class="col mb-3">
                                    <p class="text-secondary">{{ $data->role->name }} <br><i class="bi bi-clock"></i> Full
                                        Time</p>
                                </div>

                            </div>

                        </div>

                    </div>
                    <form action="{{ route('sales-app/updateProfile') }}" method="POST" class="needs-validation"
                        novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <label for="">Name</label>
                                <input type="text" name="name" class="form-control" required
                                    value="{{ $data->name }}">
                            </div>
                            <div class="col-12 mt-2">
                                <label for="">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ $data->email }}" required>

                            </div>
                            <div class="col-12 mt-2">
                                <label for="">Phone</label>
                                <input type="number" name="phone" id="phone" class="form-control"
                                    value="{{ $data->phone }}">

                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="">Address</label>
                                <textarea name="address" id="address" class="form-control">{{ $data->address }}</textarea>

                            </div>
                            <div class="col-12 mt-2 mt-3">
                                <label for="">State</label>
                                <select name="state" id="stateSimple" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($state as $item)
                                        <option value="{{ $item->state }}"
                                            {{ $data->state == $item->state ? 'Selected' : '' }}>
                                            {{ $item->state }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-12 mt-2 mt-3">
                                <label for="">City</label>
                                <select name="city" id="citySimple" class="form-control">
                                    <option value="">Select</option>
                                    @if ($data->city)
                                        <option value="{{ $data->city }}" selected> {{ $data->city }}</option>
                                    @endif

                                </select>
                            </div>
                            <div class="col-12 mt-2 mt-3">
                                <label for="">Pincode</label>
                                <input type="number" name="pincode" id="pincode" value="{{ $data->pincode }}"
                                    class="form-control">

                            </div>
                            <div class="col-12 mt-2 mt-3">
                                <label for="">Password</label>
                                <input type="" name="password" id="password" value="{{ $data->password }}"
                                    class="form-control" required>

                            </div>
                            <div class="col-12 mt-2 mt-3">
                                <button class="btn btn-theme mb-3 w-100" type="submit">Update</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
