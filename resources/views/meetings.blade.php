@extends('layouts.main')
@section('main-section')
    @php
        use Carbon\Carbon;
    @endphp
    @push('title')
        <title>Meetings </title>
    @endpush
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>
                    Meetings
                </div>
                <div>
                    <button class="btn btn-primary add" type="button">Add</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table dataTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Meeting Hours</th>
                        <th>Start location</th>
                        <th>End location</th>
                        <th>User</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $sno = 1;

                    @endphp
                    @foreach ($data as $item)
                        @php

                            $start = Carbon::parse($item->start_time);
                            $end = Carbon::parse($item->end_time);
                            $diff = $start->diff($end);
                        @endphp
                        <tr>
                            <td>{{ $sno++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->address }}</td>
                            <td>{{ $item->start_time }}</td>
                            <td>{{ $item->end_time }}</td>
                            <td>
                                @if (empty($item->end_time))
                                    Meeting in progress
                                @else
                                    {{ $diff->h }} H {{ $diff->i }} M {{ $diff->s }} S
                                @endif
                            </td>
                            <td> <a class="btn btn-primary btn-sm"
                                    href="https://www.google.com/maps?q={{ $item->start_location }}" target="_blank"> <i
                                        class="fa fa-street-view" aria-hidden="true"></i></a> </td>
                            <td> <a class="btn btn-primary btn-sm"
                                    href="https://www.google.com/maps?q={{ $item->end_location }}" target="_blank"> <i
                                        class="fa fa-street-view" aria-hidden="true"></i></a> </td>
                            <td>{{ $item->user }}</td>
                            <td>
                                @if ($item->start_time && empty($item->end_time))
                                    <button class="btn btn-primary btn-sm stopMeeting" value="{{ $item->id }}">Stop
                                        Meeting</button>
                                @else
                                    <span>Meeting is Over</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>



    <form action="{{ route('SaveMeeting') }}" method="post" class="needs-validation" novalidate
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Meetings
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="location" id="location" class="location">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="">Number</label>
                                <input type="number" name="number" id="number" class="form-control" required>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="">Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="">Address</label>
                                <input type="" name="address" id="address" class="form-control">
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

    <form action="{{ route('StopMeeting') }}" method="post">
        @csrf
        <div class="modal fade" id="stopMeetingNodal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" name="location" id="" class="location">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Stop Meeting
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h4>You are going to stop this meeting</h4>
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
        $(".add").on("click", function() {
            $("#id").val("")
            $("#modalId").modal("show")
        })
        $(document).on("click", ".edit", function() {
            var data = $(this).data("data")

            $.each(data, function(i, o) {

                $("input[name=" + i + "]").val(o)
                $("select[name=" + i + "]").val(o)
            })
            $("#modalId").modal("show")
        });

        $(document).ready(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        $(".location").val(lat + ", " + lng);
                    },
                    function(error) {
                        alert('Error: ' + error.message);
                        $('#lat').text('Unavailable');
                        $('#lng').text('Unavailable');
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser.");
                $('#lat').text('Unsupported');
                $('#lng').text('Unsupported');
            }
        });

        $(document).on("click", ".stopMeeting", function() {
            $("#id").val($(this).val())
            $("#stopMeetingNodal").modal("show")
        })
    </script>
@endsection
