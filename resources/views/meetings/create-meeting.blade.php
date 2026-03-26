<div class="modal fade" id="createMeetingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-calendar-plus text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-semibold">Schedule New Meeting</h5>
                        <small class="opacity-75">Fill in the details to schedule a meeting</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createMeetingForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">Meeting Title <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-heading  "></i>
                                </span>
                                <input type="text" name="title" class="form-control border-start-0 ps-0" 
                                    placeholder="Enter meeting title" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                    <i class="fas fa-align-left  "></i>
                                </span>
                                <textarea name="description" class="form-control border-start-0 ps-0" 
                                rows="3" placeholder="Enter meeting description (optional)"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-clock  "></i>
                                </span>
                                <input type="datetime-local" name="start_time" class="form-control border-start-0 ps-0" required>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-clock  "></i>
                                </span>
                                <input type="datetime-local" name="end_time" class="form-control border-start-0 ps-0" required>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <label class="form-label">Meeting Type <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-calendar-alt  "></i>
                                </span>
                                <select name="meeting_type" class="form-select border-start-0 ps-0" required>
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
                                    <i class="fas fa-map-marker-alt  "></i>
                                </span>
                                <input type="text" name="location" class="form-control border-start-0 ps-0" 
                                    placeholder="Office, Meeting Room, etc.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Virtual Meeting Link</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-video  "></i>
                                </span>
                                <input type="url" name="meeting_link" class="form-control border-start-0 ps-0" 
                                    placeholder="https://meet.google.com/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Related Customer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user-tie  "></i>
                                </span>
                                <select name="customer_id" class="form-select border-start-0 ps-0">
                                    <option value="">Select Customer </option>
                                    @foreach($customers as $customer)
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