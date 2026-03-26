@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">

        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header" style="background-color: white">
                        <div class="row">


                            <div class="col-8">


                                <p class="small" style="margin: 0;padding;0">{{ $lead->customerDetails->name }}</p>
                                <p class="text-secondary small"> {{ $lead->customerDetails->number }}<br>
                                    {{ $lead->customerDetails->address }}</p>
                            </div>
                            <div class="col-4" style="text-align: right">
                                <div>
                                    <a href="https://wa.me/91{{ $lead->customerDetails->number }}" target="_blank"
                                        style="text-decoration: none">
                                        <i class="fa-brands fa-whatsapp text-success"></i>
                                    </a>
                                    <a href="tel:+91{{ $lead->customerDetails->number }}" style="text-decoration: none">
                                        <i class="fa-solid fa-phone text-primary mx-1"></i>
                                    </a>
                                    <a href="/sales-app/lead-edit/{{ $lead->id }}" style="text-decoration: none">
                                        <i class="fa-solid fa-pencil text-danger"></i>
                                    </a>
                                </div>


                                {{ $lead->status ? $lead->status->name : 'NA' }}<br>
                                {{ $lead->id }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <a href="/sales-app/lead-details/{{ $lead->id }}" style="text-decoration: none; color:black">
                            <div class="row">
                                <div class="col-4">
                                    <p> <span class="small">Source</span> <br>
                                        <span
                                            class="text-secondary small">{{ $lead->source ? $lead->source->name : 'NA' }}</span>
                                    </p>

                                </div>
                                <div class="col-4">
                                    <p class="small"> <span class="small">Electrician</span> <br>
                                        <span
                                            class="text-secondary small">{{ $lead->electricianDetails ? $lead->electricianDetails->name : 'NA' }}</span>
                                    </p>

                                </div>
                                <div class="col-4">
                                    <p class="small"> <span class="small">Architect</span> <br>
                                        <span
                                            class="text-secondary small">{{ $lead->architectDetails ? $lead->architectDetails->name : 'NA' }}</span>
                                    </p>

                                </div>

                            </div>

                            <div class="row mt-2">
                                <div class="col-4">
                                    <p class="small"> <span class="small">Property Type</span> <br>
                                        <span class="text-secondary small">{{ $lead->type ? $lead->type : 'NA' }}</span>
                                    </p>

                                </div>
                                <div class="col-4">
                                    <p class="small"> <span class="small">Property Category</span> <br>
                                        <span
                                            class="text-secondary small">{{ $lead->propertyCategory ? $lead->propertyCategory->name : 'NA' }}</span>
                                    </p>

                                </div>
                                <div class="col-4">
                                    <p class="small"> <span class="small">Property Sub Category</span> <br>
                                        <span
                                            class="text-secondary small">{{ $lead->propertySubCategory ? $lead->propertySubCategory->name : 'NA' }}</span>
                                    </p>

                                </div>

                            </div>


                            <div class="row mt-2">
                                <div class="col-4">
                                    <p class="small"> <span class="small">Property Stage</span> <br>
                                        <span class="text-secondary small">{{ $lead->type ? $lead->type : 'NA' }}</span>
                                    </p>

                                </div>
                                <div class="col-8">
                                    <p class="small"> <span class="small">Lead Address</span> <br>
                                        <span class="text-secondary small">{{ $lead->address ? $lead->address : 'NA' }}

                                            {{ $lead->city ? $lead->city : 'NA' }}<br>{{ $lead->state ? $lead->state : 'NA' }}

                                        </span>
                                    </p>

                                </div>

                            </div>

                            <div class="row mt-2">
                                <div class="col-6">
                                    <p class="small"> <span class="small">Update Date</span> <br>
                                        <span class="text-secondary small"> {{ $lead->updated_at }}</span>
                                    </p>

                                </div>
                                <div class="col-6">
                                    <p class="small"> <span class="small">Last Comment</span> <br>
                                        <span
                                            class="text-secondary small">{{ $lead->last_comment ? $lead->last_comment : 'NA' }}



                                        </span>
                                    </p>

                                </div>

                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-2   px-0">
                <div class="card">
                    <div class="card-header bg-white">
                        Products
                    </div>
                    <div class="card-body">
                            <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name </th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($product as $item)
                                <tr>
                                    <td>{{ $sno++ }}</td>
                              <td>{{ optional($item->productDetails)->name ?? 'NA' }}</td>
                                    <td>{{ $item->qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                    </div>
                </div>
            


            </div>


            <div class="col-12 mt-2  px-0">
                <div class="card">
                    <div class="card-header bg-white">
                        Remarks
                    </div>
                </div>
                <div class=" table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Status</th>
                                <th>Remind Date</th>
                                <th>Remind Time</th>
                                <th>Remarks</th>
                                <th>User</th>
                                <th>Created at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sno = 1;
                            @endphp
                            @foreach ($lead_remarks as $item)
                                <tr>
                                    <td>{{ $sno++ }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->remind_date }}</td>
                                    <td>{{ $item->remind_time }}</td>
                                    <td>{{ $item->comment }}</td>
                                    <td>{{ $item->user }}</td>
                                    <td>{{ $item->created_at }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>


            </div>
        </div>


    </div>
@endsection
