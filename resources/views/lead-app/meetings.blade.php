@extends('lead-app.layouts.main')
@section('main-section')
    <div class="card border-0 d-block d-md-none">
        <div class="card-header bg-white fw-bold">
            Meetings
        </div>

        <div class="card-body p-2" style="background:#f5f7fb;">
            @foreach ($data as $item)
                <div class="mb-3 p-3"
                    style="
                    background:#ffffff;
                    border-radius:16px;
                    box-shadow:0 6px 18px rgba(0,0,0,0.08);
                    border-left:5px solid #4f46e5;
                ">

                    <!-- TITLE + STATUS -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div style="font-size:15px;font-weight:700;color:#111;">
                            {{ $item->title }}
                        </div>

                        <button
                            style="
                            background:#eef2ff;
                            color:#4f46e5;
                            padding:4px 10px;
                            font-size:11px;
                            border-radius:20px;
                            font-weight:600;
                            border:none;
                        "
                            type="button" data-data="{{ @json_encode($item) }}" class="btnEditMeeting">
                            {{ ucfirst($item->status) }}
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </button>


                    </div>

                    <!-- DESCRIPTION -->
                    <div style="font-size:13px;color:#555;margin-bottom:8px;">
                        {{ $item->description }}
                    </div>
                    <div class="d-flex align-items-center mb-2" style="font-size:12px;color:#444;">
                        <i class="fas fa-street-view me-2" style="color:#4f46e5;"></i>
                        {{ $item->location }}
                    </div>
                    <!-- TIME -->
                    <div class="d-flex align-items-center mb-2" style="font-size:12px;color:#444;">
                        <i class="fas fa-clock me-2" style="color:#4f46e5;"></i>
                        {{ $item->start_time }} – {{ $item->end_time }}
                    </div>

                    <!-- LOCATIONS -->
                    @if ($item->start_location)
                        <div class="d-flex">
                            <div style="font-size:12px;color:#444;margin-bottom:6px;">

                                <a href="https://www.google.com/maps?q={{ $item->start_location }}" target="_blank"
                                    title="View Location on Google Maps">
                                    <i class="fas fa-map-marker-alt me-1" style="color:#16a34a;"></i>
                                    Start Location
                                </a>


                            </div>
                            <div style="font-size:12px;color:#444;margin-bottom:6px;" class="mx-2">
                                @if ($item->end_location)
                                    <a href="https://www.google.com/maps?q={{ $item->end_location }}" target="_blank"
                                        title="View Location on Google Maps">
                                        <i class="fas fa-map-marker-alt me-1" style="color:#16a34a;"></i>
                                        End Location
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @php
                        $start = \Carbon\Carbon::parse($item->start_time);
                        $end = \Carbon\Carbon::parse($item->end_time);

                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }

                        $diff = $start->diff($end);
                    @endphp
                  


                    @if ($item->end_time)
                        <div style="font-size:12px;color:#444;margin-bottom:6px;">
                            <i class="fas fa-flag-checkered me-1" style="color:#dc2626;"></i>
                            {{ str_pad($diff->h, 2, '0', STR_PAD_LEFT) }} H:
                            {{ str_pad($diff->i, 2, '0', STR_PAD_LEFT) }} M:
                            {{ str_pad($diff->s, 2, '0', STR_PAD_LEFT) }} S
                        </div>
                    @endif
                       <div style="font-size:13px;color:#555;margin-bottom:8px;">
                        {{ $item->remarks }}
                    </div>
                    <!-- TYPE + CUSTOMER -->
                    <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                        <span
                            style="
                            background:#ecfeff;
                            color:#0891b2;
                            padding:4px 10px;
                            border-radius:14px;
                            font-weight:600;
                        ">
                            {{ ucfirst($item->meeting_type) }}
                        </span>

                        <span style="color:#555;font-weight:600;">
                            {{ $item->customer }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between">

                        <div class="mt-3 col-4">
                            @if ($item->meeting_link)
                                <a href="{{ $item->meeting_link }}" target="_blank"
                                    style="
                               display:block;
                               text-align:center;
                               background:#4f46e5;
                               color:#fff;
                               padding:8px;
                               border-radius:10px;
                               font-size:13px;
                               font-weight:600;
                               text-decoration:none;
                           ">
                                    Join Meeting
                                </a>
                            @endif

                        </div>

                        <div class="col-4 mt-3">
                            @if (!$item->start_location && !$item->end_location)
                                <button
                                    style="
                               display:block;
                               text-align:center;
                               background:green;
                               color:#fff;
                               padding:8px;
                               border-radius:10px;
                               font-size:13px;
                               font-weight:600;
                               text-decoration:none;
                               outline:none;
                               border:none;
                           "
                                    type="button" class="btnStartMeeting" value="{{ $item->id }}">
                                    Start Meeting
                                </button>
                            @elseif($item->start_location && !$item->end_location)
                                <button
                                    style="
                               display:block;
                               text-align:center;
                               background:red;
                               color:#fff;
                               padding:8px;
                               border-radius:10px;
                               font-size:13px;
                               font-weight:600;
                               text-decoration:none;
                               outline:none;
                               border:none;
                           "
                                    type="button" class="btnStopMeeting" value="{{ $item->id }}">
                                    Stop Meeting
                                </button>
                            @elseif($item->start_location && $item->end_location)
                                <button
                                    style="
                               display:block;
                               text-align:center;
                               background:darkblue;
                               color:#fff;
                               padding:8px;
                               border-radius:10px;
                               font-size:13px;
                               font-weight:600;
                               text-decoration:none;
                               outline:none;
                               border:none;
                           "
                                    type="button">
                                    Completed
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <form action="{{ route('/lead-app/SaveMeeting') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="editMeetingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Edit Meeting
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" id="id" hidden>
                            <div class="col-12">
                                <label for="">Meeting Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>

                            </div>

                            <div class="col-12 mt-3">
                                <label for="">Description</label>
                                <textarea type="text" name="description" id="description" class="form-control"></textarea>

                            </div>
                            <div class="col-6 mt-3">
                                <label for="">Start Time</label>
                                <input type="datetime-local" name="start_time" id="title" class="form-control"
                                    required>

                            </div>

                            <div class="col-6 mt-3">
                                <label class="form-label">Meeting Type</label>
                                <select name="meeting_type" class="form-control" required>
                                    <option value="">All Types</option>
                                    <option value="internal">Internal
                                    </option>
                                    <option value="client">Client</option>
                                    <option value="team">Team</option>
                                    <option value="general">General
                                    </option>
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Location</label>
                                <input type="" name="location" id="location" class="form-control">

                            </div>

                            <div class="col-6 mt-3">
                                <label for="">Virtual Meeting Link</label>
                                <input type="url" name="meeting_link" id="meeting_link" class="form-control">

                            </div>


                            <div class="col-12 mt-3">
                                <label for="">Customer</label>
                                <select name="customer_id" id="customer_id" required class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($headerCustomer as $item)
                                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                    @endforeach
                                </select>

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


    <form action="{{ route('/lead-app/startMeeting') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="startMeetingModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Start Meeting
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" hidden class="location" name="location">
                        <input type="hidden" hidden name="id" id="startID">
                        You are going to start your meeting...
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




    <form action="{{ route('/lead-app/StopMeeting') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="stopMeetingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Stop Meeting
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" hidden class="location" name="location">
                        <input type="hidden" hidden name="id" id="stopID">
                        You are going to Stop your meeting... <br>
                        <label for="" class="mt-3">Remarks</label>
                        <textarea name="remarks" id="" class="form-control" required></textarea>
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
        $(document).on("click", ".btnEditMeeting", function() {
            let data = $(this).data("data");
            $.each(data, function(key, value) {
                $("input[name='" + key + "']").val(value)
                $("textarea[name='" + key + "']").val(value)
                $("select[name='" + key + "']").val(value)
            })
            $("#editMeetingModal").modal("show")
        });

        $(document).on("click", ".btnStartMeeting", function() {
            $("#startID").val($(this).val())
            $("#startMeetingModal").modal("show")
        });

        $(document).on("click", ".btnStopMeeting", function() {
            $("#stopID").val($(this).val())
            $("#stopMeetingModal").modal("show")
        })
    </script>


    <script>
        window.onload = function() {
            getLocation();
        };

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        console.log("Latitude:", latitude);
                        console.log("Longitude:", longitude);

                        $(".location").val(latitude + ", " + longitude)
                    },
                    function(error) {
                        alert("Location access denied or unavailable");
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
@endsection
