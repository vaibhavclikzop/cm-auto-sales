@extends('layouts.main')
@section('main-section')



    <div class="page-content flex-grow-1 p-4" id="meetingsApp">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Meetings Management</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createMeetingModal">
                            <i class="fas fa-plus me-1"></i> Schedule New Meeting
                        </button>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3" id="filterForm">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled
                                </option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                    Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Meeting Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Internal
                                </option>
                                <option value="client" {{ request('type') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="team" {{ request('type') == 'team' ? 'selected' : '' }}>Team</option>
                                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2 ">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('meetings.index') }}" class="btn btn-danger w-100"><i
                                    class="fa-solid fa-eraser"></i> Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if ($meetings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover data-table-all">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Organizer</th>
                                        <th>Start/End Time</th>
                                        <th>Start/End Location</th>
                                        <th>Duration</th>
                                        <th>Remarks</th>

                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($meetings as $meeting)
                                        <tr class="meeting-card meeting-{{ $meeting->meeting_type }}">
                                            <td>
                                                <strong>{{ $meeting->title }}</strong>
                                                @if ($meeting->customer)
                                                    <br><small class="text-muted">Customer:
                                                        {{ $meeting->customer->name }}</small>
                                                @endif
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-soft-info view-meeting"
                                                        data-meeting-id="{{ $meeting->id }}" title="View Details">
                                                        <i class="fas fa-eye text-info"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-soft-primary edit-meeting"
                                                        data-meeting-id="{{ $meeting->id }}" title="Edit">
                                                        <i class="fas fa-edit  text-dark"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-soft-danger delete-meeting"
                                                        data-meeting-id="{{ $meeting->id }}" title="Delete">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </button>
                                                    @if (!$meeting->start_time && !$meeting->end_time)
                                                        <button class="btn btn-primary btn-sm btnStart" type="button"
                                                            value="{{ $meeting->id }}">Start</button>
                                                    @elseif ($meeting->start_time && !$meeting->end_time)
                                                        <button class="btn btn-danger btn-sm btnStop" type="button"
                                                            value="{{ $meeting->id }}">Stop</button>
                                                    @elseif ($meeting->start_time && $meeting->end_time)
                                                        <button class="btn btn-success btn-sm" type="button">
                                                            Completed</button>
                                                    @endif


                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($meeting->start_time)->format('M j, Y') }}
                                                </div>

                                                <small class="text-muted">
                                                    <div>
                                                        {{ \Carbon\Carbon::parse($meeting->start_time)->format('M j, Y') }}
                                                    </div>
                                                    -
                                                    <div>{{ \Carbon\Carbon::parse($meeting->end_time)->format('M j, Y') }}
                                                    </div>

                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge  text-dark">
                                                    {{ ucfirst($meeting->meeting_type) }}
                                                </span>
                                            </td>
                                            <td>{{ $meeting->organizer->name ?? 'N/A' }}</td>
                                            <td>
                                                Start :
                                                {{ $meeting->start_time ? date('h:i A d-m-Y', strtotime($meeting->start_time)) : 'NA' }}
                                                <br>
                                                End :
                                                {{ $meeting->end_time ? date('h:i A d-m-Y', strtotime($meeting->end_time)) : 'NA' }}

                                            </td>
                                            <td>
                                                <a href="https://www.google.com/maps?q={{ $meeting->start_location }}"
                                                    target="_blank" title="View Location on Google Maps" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-street-view" aria-hidden="true"></i>
                                                </a>

                                                <a href="https://www.google.com/maps?q={{ $meeting->end_location }}"
                                                    target="_blank" title="View Location on Google Maps" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-street-view" aria-hidden="true"></i>
                                                </a>

                                            </td>
                                            <td>
                                                @php
                                                    $start = \Carbon\Carbon::parse($meeting->start_time);
                                                    $end = \Carbon\Carbon::parse($meeting->end_time);

                                                    if ($end->lessThan($start)) {
                                                        $end->addDay();
                                                    }

                                                    $diff = $start->diff($end);
                                                @endphp

                                                {{ str_pad($diff->h, 2, '0', STR_PAD_LEFT) }} H:
                                                {{ str_pad($diff->i, 2, '0', STR_PAD_LEFT) }} M:
                                                {{ str_pad($diff->s, 2, '0', STR_PAD_LEFT) }} S


                                            </td>
                                            <td>
                                                {{ $meeting->remarks ?? 'N/A' }}
                                            </td>


                                            <td>
                                                <span class="badge bg-success">
                                                    {{ ucfirst(str_replace('_', ' ', $meeting->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $meetings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>No meetings found</h5>
                            <p class="text-muted">Schedule your first meeting to get started</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createMeetingModal">
                                <i class="fas fa-plus me-1"></i> Schedule Meeting
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- @include('modals.create-meeting')

@include('modals.meeting-detail')

@include('modals.edit-meeting') --}}

    <template id="participantTemplate">
        <div class="participant-item border rounded-3 p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-0 fw-semibold">Participant</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-participant">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Participant Type</label>
                    <select name="participants[__INDEX__][type]" class="form-select participant-type" required>
                        <option value="user">Staff User</option>
                        <option value="customer">Customer</option>
                        <option value="external">External Guest</option>
                    </select>
                </div>
                <div class="col-md-4 user-field">
                    <label class="form-label">Select User</label>
                    <select name="participants[__INDEX__][id]" class="form-select user-select">
                        <option value="">Choose User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 customer-field d-none">
                    <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                    <select name="participants[__INDEX__][id]" class="form-select customer-select">
                        <option value="">Choose Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 external-field d-none">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="participants[__INDEX__][name]" class="form-control external-name"
                        placeholder="Saurabh">
                </div>
                <div class="col-md-4 external-field d-none">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="participants[__INDEX__][email]" class="form-control external-email"
                        placeholder="saurabh@gmail.com">
                </div>
            </div>
        </div>
    </template>

    <div class="modal fade" id="createMeetingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-calendar-plus text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-semibold">Schedule New Meeting</h5>
                            <small class="opacity-75">Fill in the details to schedule a meeting</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="createMeetingForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">Meeting Title <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-heading  text-dark"></i>
                                    </span>
                                    <input type="text" name="title" class="form-control "
                                        placeholder="Enter meeting title" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                        <i class="fas fa-align-left  text-dark"></i>
                                    </span>
                                    <textarea name="description" class="form-control " rows="3"
                                        placeholder="Enter meeting description (optional)"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-clock  text-dark"></i>
                                    </span>
                                    <input type="datetime-local" name="start_time"
                                        class="form-control " required>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                                                                                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                                                                                                            <div class="input-group">
                                                                                                                <span class="input-group-text bg-light border-end-0">
                                                                                                                    <i class="fas fa-clock  text-dark"></i>
                                                                                                                </span>
                                                                                                                <input type="datetime-local" name="end_time" class="form-control " required>
                                                                                                            </div>
                                                                                                        </div> -->
                            <div class="col-md-6">
                                <label class="form-label">Meeting Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar-alt  text-dark"></i>
                                    </span>
                                    <select name="meeting_type" class="form-select " required>
                                        <option value="">Select Type</option>
                                        <option value="internal">Internal Meeting</option>
                                        <option value="client">Client Meeting</option>
                                        <option value="team">Team Meeting</option>
                                        <option value="general">General Meeting</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-map-marker-alt  text-dark"></i>
                                    </span>
                                    <input type="text" name="location" class="form-control "
                                        placeholder="Office, Meeting Room, etc.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Virtual Meeting Link</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-video  text-dark"></i>
                                    </span>
                                    <input type="url" name="meeting_link" class="form-control "
                                        placeholder="https://meet.google.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Related Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user-tie  text-dark"></i>
                                    </span>
                                    <select name="customer_id" class="form-select ">
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-calendar-check me-2"></i>Schedule Meeting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="meetingDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info text-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-semibold text-dark">Meeting Details</h5>
                            <small class="opacity-75 text-muted">Complete information about the meeting</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="meetingDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border  text-dark" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-3">Loading meeting details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-edit text-dark"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-semibold">Edit Meeting</h5>
                            <small class="opacity-75">Update meeting information</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMeetingForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4" id="editMeetingContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-3">Loading meeting data...</p>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="fas fa-save me-2"></i>Update Meeting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <form action="{{ route('meetings.startMeeting') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="startMeetingModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
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
                        You are going to start your meeting
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



    <form action="{{ route('meetings.stopMeeting') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="stopMeetingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Stop Meeting
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" hidden name="id" id="stopID">
                        <input type="hidden" hidden class="location" name="location">
                        You are going to stop your meeting

                        <div class="mt-3">
                            <label for="">Remarks</label>
                            <textarea name="remarks" id="" class="form-control"></textarea>
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

    <script>
        $(document).ready(function() {
            let participantIndex = 0;
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            initDateRangeFilter();

            function addParticipant() {
                const template = document.getElementById('participantTemplate');
                const clone = template.content.cloneNode(true);
                const html = clone.querySelector('.participant-item').outerHTML
                    .replace(/__INDEX__/g, participantIndex);

                $('#participantsContainer').append(html);
                const newItem = $('#participantsContainer .participant-item').last();
                newItem.find('.participant-type').change(function() {
                    handleParticipantTypeChange($(this));
                }).trigger('change');

                participantIndex++;
            }

            function handleParticipantTypeChange(select) {
                const item = select.closest('.participant-item');
                const type = select.val();
                item.find('.user-field, .customer-field, .external-field').addClass('d-none');
                if (type === 'user') {
                    item.find('.user-field').removeClass('d-none');
                } else if (type === 'customer') {
                    item.find('.customer-field').removeClass('d-none');
                } else if (type === 'external') {
                    item.find('.external-field').removeClass('d-none');
                }
            }

            $(document).on('click', '.remove-participant', function() {
                if ($('#participantsContainer .participant-item').length > 1) {
                    $(this).closest('.participant-item').remove();
                }
            });

            $('#createMeetingModal').on('shown.bs.modal', function() {
                participantIndex = 0;
                $('#participantsContainer').empty();
                addParticipant();
                const now = new Date();
                const startTime = new Date(now.getTime() + 60 * 60 * 1000);
                const endTime = new Date(startTime.getTime() + 60 * 60 * 1000);

                $('input[name="start_time"]').val(formatDateTime(startTime));
                $('input[name="end_time"]').val(formatDateTime(endTime));
            });

            $('#addParticipantBtn').click(addParticipant);
            $('#createMeetingForm').submit(function(e) {
                e.preventDefault();
                const startTime = new Date($('input[name="start_time"]').val());
                const endTime = new Date($('input[name="end_time"]').val());

                if (endTime <= startTime) {
                    toastr.error('End time must be after start time');
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('meetings.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#createMeetingModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Failed to create meeting');
                        }
                    }
                });
            });

            $(document).on('click', '.view-meeting', function() {
                const meetingId = $(this).data('meeting-id');
                $.ajax({
                    url: `/meetings/${meetingId}/details`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const meeting = response.meeting;
                            const modalContent = `
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>${meeting.title}</h4>
                                    <p class="text-muted">${meeting.description || 'No description'}</p>
                                    
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-2 text-muted">Start Time</h6>
                                                    <p class="card-text">${new Date(meeting.start_time).toLocaleString()}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-2 text-muted">End Time</h6>
                                                    <p class="card-text">${new Date(meeting.end_time).toLocaleString()}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p><strong>Location:</strong> ${meeting.location || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Type:</strong> ${meeting.meeting_type}</p>
                                        </div>
                                    </div>
                                    
                                    ${meeting.meeting_link ? `
                                                                                                                    <div class="alert alert-info">
                                                                                                                        <strong>Meeting Link:</strong> 
                                                                                                                        <a href="${meeting.meeting_link}" target="_blank">${meeting.meeting_link}</a>
                                                                                                                    </div>
                                                                                                                ` : ''}
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>Organizer</h6>
                                            <p>${meeting.organizer.name}</p>
                                            <p>${meeting.organizer.email}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h6>Participants (${meeting.participants.length})</h6>
                                            <div class="list-group">
                                                ${meeting.participants.map(participant => `
                                                                                                                                <div class="list-group-item">
                                                                                                                                    <div class="d-flex justify-content-between">
                                                                                                                                        <div>
                                                                                                                                            <strong>${participant.name}</strong><br>
                                                                                                                                            <small>${participant.email}</small>
                                                                                                                                        </div>
                                                                                                                                        <span class="participant-status participant-${participant.status}">
                                                                                                                                            ${participant.status}
                                                                                                                                        </span>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            `).join('')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 action-buttons">
                                <button class="btn btn-primary edit-meeting-btn" data-meeting-id="${meeting.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger delete-meeting-btn" data-meeting-id="${meeting.id}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        `;

                            $('#meetingDetailsContent').html(modalContent);
                            $('#meetingDetailsModal').modal('show');
                        }
                    }
                });
            });

            $(document).on('click', '.edit-meeting, .edit-meeting-btn', function() {
                const meetingId = $(this).data('meeting-id');
                $.ajax({
                    url: `/meetings/${meetingId}/details`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const meeting = response.meeting;
                            const editContent = `
                            <input type="hidden" name="meeting_id" value="${meeting.id}">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Meeting Title *</label>
                                    <input type="text" name="title" class="form-control" value="${meeting.title}" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3">${meeting.description || ''}</textarea>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Start Time *</label>
                                    <input type="datetime-local" name="start_time" class="form-control" 
                                        value="${formatDateTimeForInput(new Date(meeting.start_time))}" required>
                                </div>             
                                <div class="col-md-6">
                                    <label class="form-label">Meeting Type *</label>
                                    <select name="meeting_type" class="form-select" required>
                                        <option value="internal" ${meeting.meeting_type === 'internal' ? 'selected' : ''}>Internal</option>
                                        <option value="client" ${meeting.meeting_type === 'client' ? 'selected' : ''}>Client</option>
                                        <option value="team" ${meeting.meeting_type === 'team' ? 'selected' : ''}>Team</option>
                                        <option value="general" ${meeting.meeting_type === 'general' ? 'selected' : ''}>General</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control" value="${meeting.location || ''}">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Virtual Meeting Link</label>
                                    <input type="url" name="meeting_link" class="form-control" value="${meeting.meeting_link || ''}">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="scheduled" ${meeting.status === 'scheduled' ? 'selected' : ''}>Scheduled</option>
                                        <option value="in_progress" ${meeting.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                        <option value="completed" ${meeting.status === 'completed' ? 'selected' : ''}>Completed</option>
                                        <option value="cancelled" ${meeting.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        `;

                            $('#editMeetingContent').html(editContent);
                            $('#meetingDetailsModal').modal('hide');
                            $('#editMeetingModal').modal('show');
                        }
                    }
                });
            });

            $('#editMeetingForm').submit(function(e) {
                e.preventDefault();
                const meetingId = $('input[name="meeting_id"]').val();
                const formData = new FormData(this);

                $.ajax({
                    url: `/meetings/${meetingId}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editMeetingModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Failed to update meeting');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-meeting, .delete-meeting-btn', function(e) {
                e.preventDefault();
                const meetingId = $(this).data('meeting-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    backdrop: true,
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: `/meetings/${meetingId}`,
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    resolve(response);
                                },
                                error: function(xhr) {
                                    reject(xhr.responseJSON?.message ||
                                        'Failed to delete meeting');
                                }
                            });
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        toastr.success('The meeting has been deleted.')
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                }).catch((error) => {
                    toastr.error('Something went wrong.')
                });
            });

            function initDateRangeFilter() {
                $('#dateRange').change(function() {
                    const value = $(this).val();
                    const customFields = $('#customDateFields');

                    if (value === 'custom') {
                        if (customFields.length === 0) {
                            const html = `
                            <div class="row mt-3" id="customDateFields">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" 
                                        value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" 
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                        `;
                            $(this).closest('.col-md-3').after(`
                            <div class="col-md-6" id="customDateContainer">
                                ${html}
                            </div>
                        `);
                        }
                    } else {
                        $('#customDateContainer').remove();
                    }
                });

                if ($('#dateRange').val() === 'custom') {
                    $('#dateRange').trigger('change');
                }
            }

            function formatDateTime(date) {
                return date.toISOString().slice(0, 16);
            }

            function formatDateTimeForInput(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });


        $(document).on("click", ".btnStart", function() {
            $("#startID").val($(this).val())
            $("#startMeetingModal").modal("show")
        })

        $(document).on("click", ".btnStop", function() {
            $("#stopID").val($(this).val())
            $("#stopMeetingModal").modal("show")
        });
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
