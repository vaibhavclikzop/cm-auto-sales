@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">
        @foreach ($data as $item)
            <div class="row mt-2">
                <div class="col-12 px-0">
                    <div class="card">
                        <div class="card-header" style="background-color: white">
                            <div class="row">


                                <div class="col-7">


                                    <p class="small" style="margin: 0;padding;0">
                                        {{ $item->leadDetails->customerDetails->name }}</p>
                                    <p class="text-secondary small"> {{ $item->leadDetails->customerDetails->number }}<br>
                                        {{ $item->leadDetails->customerDetails->address }}</p>
                                </div>
                                <div class="col-5" style="text-align: right">
                                    <div>
                                        <a href="https://wa.me/91{{ $item->leadDetails->customerDetails->number }}"
                                            target="_blank" style="text-decoration: none">
                                            <i class="fa-brands fa-whatsapp text-success"></i>
                                        </a>
                                        <a href="tel:+91{{ $item->leadDetails->customerDetails->number }}"
                                            style="text-decoration: none">
                                            <i class="fa-solid fa-phone text-primary mx-1"></i>
                                        </a>

                                    </div>


                                    <p class="small"> {{ $item->name ? $item->name : 'NA' }}<br>
                                       Lead ID : {{ $item->leadDetails->id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
      <a href="{{ request('status') == 'generated quote' ? url('/sales-app/quote-view/'.$item->id) : url('/sales-app/pi-view/'.$item->id) }}" 
   style="text-decoration: none; color:black">
                                <div class="row">
                                    <div class="col-4">
                                        <p> <span class="small">Source</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->source ? $item->leadDetails->source->name : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Electrician</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->electricianDetails ? $item->leadDetails->electricianDetails->name : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Architect</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->architectDetails ? $item->leadDetails->architectDetails->name : 'NA' }}</span>
                                        </p>

                                    </div>

                                </div>

                                <div class="row mt-2">
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Type</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->type ? $item->leadDetails->type : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Category</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->propertyCategory ? $item->leadDetails->propertyCategory->name : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Sub Category</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->propertySubCategory ? $item->leadDetails->propertySubCategory->name : 'NA' }}</span>
                                        </p>

                                    </div>

                                </div>


                                <div class="row mt-2">
                                    <div class="col-4">
                                        <p class="small"> <span class="small">Property Stage</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->type ? $item->leadDetails->type : 'NA' }}</span>
                                        </p>

                                    </div>
                                    <div class="col-8">
                                        <p class="small"> <span class="small">Lead Address</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->address ? $item->leadDetails->address : 'NA' }}

                                                {{ $item->leadDetails->city ? $item->leadDetails->city : 'NA' }}<br>{{ $item->leadDetails->state ? $item->leadDetails->state : 'NA' }}

                                            </span>
                                        </p>

                                    </div>

                                </div>

                                <div class="row mt-2">
                                    <div class="col-6">
                                        <p class="small"> <span class="small">Update Date</span> <br>
                                            <span class="text-secondary small"> {{ $item->leadDetails->updated_at }}</span>
                                        </p>

                                    </div>
                                    <div class="col-6">
                                        <p class="small"> <span class="small">Last Comment</span> <br>
                                            <span
                                                class="text-secondary small">{{ $item->leadDetails->last_comment ? $item->leadDetails->last_comment : 'NA' }}



                                            </span>
                                        </p>

                                    </div>

                                </div>
                            </a>

                            {{-- <div class="row mt-2">
                                <a class="btn btn-theme btn-sm" href="/sales-app/quote-view/{{$item->id}}">Download</a>

                            </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection
