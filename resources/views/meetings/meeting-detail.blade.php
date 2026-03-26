<div class="modal fade" id="meetingDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-semibold text-dark">Meeting Details</h5>
                        <small class="opacity-75 text-muted">Complete information about the meeting</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="meetingDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Loading meeting details...</p>
                </div>
            </div>
        </div>
    </div>
</div>