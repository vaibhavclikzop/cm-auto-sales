<div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
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