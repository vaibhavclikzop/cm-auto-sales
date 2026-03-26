@extends('salesapp.layouts.main')
@section('main-section')
    <input type="text" id="leadSearch" class="form-control mb-1" placeholder="Search leads...">

    <div class="lead-cards">
        @foreach ($lead as $item)
            <div class="row mt-2 lead-card">
                <div class="col-12 px-0">
                    <div class="card">
                        <div class="card-header" style="background-color: white">
                            <div class="row">


                                <div class="col-7">


                                    <p class="small" style="margin: 0;padding;0">{{ $item->customerDetails->name }}</p>
                                    <p class="text-secondary small"> {{ $item->customerDetails->number }}<br>
                                        {{ $item->customerDetails->address }}</p>
                                </div>
                                <div class="col-5" style="text-align: right">
                                    <div>
                                        <a href="https://wa.me/91{{ $item->customerDetails->number }}" target="_blank"
                                            style="text-decoration: none">
                                            <i class="fa-brands fa-whatsapp text-success"></i>
                                        </a>
                                        <a href="tel:+91{{ $item->customerDetails->number }}" style="text-decoration: none">
                                            <i class="fa-solid fa-phone text-primary mx-1"></i>
                                        </a>
                                        <a href="/sales-app/lead-edit/{{ $item->id }}" style="text-decoration: none">
                                            <i class="fa-solid fa-pencil text-danger"></i>
                                        </a>
                                    </div>


                                    <p class="small"> {{ $item->status ? $item->status->name : 'NA' }}<br>
                                        {{ $item->id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <a href="/sales-app/lead-details/{{ $item->id }}"
                                style="text-decoration: none; color:black">
                                <div class="row">
                                    <div class="col-4">
                                        <p> <span class="small">Source</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->source ? $item->source->name : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Electrician</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->electricianDetails ? $item->electricianDetails->name : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Architect</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->architectDetails ? $item->architectDetails->name : 'NA' }}</span>
                                        </p>

                                    </div>

                                </div>

                                <div class="row mt-2">
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Type</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->type ? $item->type : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Category</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->propertyCategory ? $item->propertyCategory->name : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Sub Category</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->propertySubCategory ? $item->propertySubCategory->name : 'NA' }}</span>
                                        </p>

                                    </div>

                                </div>


                                <div class="row mt-2">
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Stage</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->type ? $item->type : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-8">
                                        <p class="small"> <span class="small">Lead Address</span> <br>
                                            <span class="text-secondary small">{{ $item->address ? $item->address : 'NA' }}

                                                {{ $item->city ? $item->city : 'NA' }}<br>{{ $item->state ? $item->state : 'NA' }}

                                            </span>
                                        </p>

                                    </div>

                                </div>

                                <div class="row mt-2">
                                    <div class="col-6">
                                        <p class="small"> <span class="small">Update Date</span> <br>
                                            <span class="text-secondary small"> {{ $item->updated_at }}</span>
                                        </p>

                                    </div>
                                    <div class="col-6">
                                        <p class="small"> <span class="small">Last Comment</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->last_comment ? $item->last_comment : 'NA' }}



                                            </span>
                                        </p>

                                    </div>

                                </div>
                            </a>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <button class="btn btn-primary btn-sm btnUpdateStatus" type="button"
                                        value="{{ $item->id }}">Update Status</button>
                                    @if ($item->quotes_status == 'requested quote')
                                        <button class="btn btn-success btn-sm" type="button"> Requested</button>
                                    @else
                                        <button class="btn btn-theme btn-sm requestQuote" type="button"
                                            value="{{ $item->id }}">Request
                                            Quote</button>
                                    @endif

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <form action="{{ route('sales-app/updateStatus') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="updateStatus" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Update Status
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <input type="hidden" name="lead_id" id="sLeadID">
                        <div class="col-12">
                            <label for="">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" required></textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Select</option>
                                @foreach ($Leadstatus as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-12 mt-3">
                            <label for="">Remind Date</label>
                            <input type="date" name="remind_date" id="remind_date" class="form-control">

                        </div>
                        <div class="col-12 mt-3">
                            <label for="">Remind Time</label>
                            <input type="time" name="remind_time" id="remind_time" class="form-control">

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


    <form action="{{ route('sales-app/requestQuote') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="modal fade" id="requestQuoteModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Request
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <input type="hidden" name="lead_id" id="quoteLeadID">


                        <h6>Are you sure you want to send request to generate quote?</h6>
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
        $(document).on("click", ".btnUpdateStatus", function() {

            $("#sLeadID").val($(this).val())
            $("#updateStatus").modal("show");
        });

        $(document).on("click", ".requestQuote", function() {

            $("#quoteLeadID").val($(this).val())
            $("#requestQuoteModal").modal("show");
        });
        $(document).ready(function() {
            $("#leadSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                $(".lead-cards .lead-card").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
@endsection
