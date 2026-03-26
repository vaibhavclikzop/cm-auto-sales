@extends('salesapp.layouts.main')
@section('main-section')
    <div class="">
        <div class="row mt-2">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between;">
                        <div></div>
                        <div>


                        </div>

                    </div>
                    <div class="card-body table-responsive text-uppercase" id="PrintOrder" style="text-transform: uppercase;">
                        <div class="d-flex">
                            <div>
                                <img src="/logo/{{ $setting->img }}" alt=""
                                    style="width: 164px; margin-right: 5px;">
                                <!-- <h2 style="font-weight:bold">QULITE LED INDIA</h2> -->
                            </div>
                            <div style="width:  100%;">
                                <hr style="border:solid 6px; width: 100%; color:orange">
                            </div>

                        </div>
                        <div class="mt-5 d-flex" style="justify-content: space-between;font-size:11px ">
                            <div>
                                <span class="fw-bold">Reference No:-</span> QUOTE- {{ $data->id }}
                                <br>To,
                                <br> {{ $data->leadDetails->company_name }} {{ $data->leadDetails->customerDetails->name }}
                                <br>
                                {{ $data->leadDetails->customerDetails->address }},
                                <br> {{ $data->leadDetails->customerDetails->city }}
                                {{ $data->leadDetails->customerDetails->state }}
                                <br>
                                {{ $data->leadDetails->customerDetails->number }} <br>

                                GST : {{ $data->leadDetails->customerDetails->gst }} <br>


                                {{ $data->leadDetails->address }} {{ $data->leadDetails->city }}
                                {{ $data->leadDetails->state }} <br>
                                Subject :- Performa Invoice <br>
                                Name :- {{ $data->name }}




                            </div>
                            <div>
                                <span class="fw-bold">Date :-</span> {{ date('d-m-y', strtotime($data->created_at)) }}
                                <br><br>
                                <span class="fw-bold">Sales Person</span> <br>
                                <span class="fw-bold">User :-</span> {{ $data->leadDetails->userDetails->name ?? 'NA' }}
                                <br>
                                <span class="fw-bold">Number :-</span> {{ $data->leadDetails->userDetails->phone ?? 'NA' }}
                                <br>
                                <span class="fw-bold">Role :-</span>
                                {{ $data->leadDetails->userDetails->role->name ?? 'NA' }} <br>

                            </div>
                        </div>
                        <div class="d-flex" style="font-size:11px">
                            <p class="fw-bold">Dear sir,<br>
                                We acknowledge for the requirement and as desired please find below our best prices :-
                            </p>
                        </div>

                        <div>
                            <div class="mt-3 table-responsive">
                                <table class="" id="myTable">
                                    <thead>
                                        <tr style="border: solid 1px; padding: 5px;font-size:11px  ">
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">S.No
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">Code
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Article No
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Picture</th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Description
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">LED
                                                Power
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">F
                                                Color</th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">R
                                                Color</th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                C.Temp</th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Optics</th>
                                            <th scope="col" style="border: solid 1px; padding: 5px;font-size:11px  ">CRI
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">IP
                                                Rating
                                            </th>

                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Product</th>

                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">Qty
                                            </th>
                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Price</th>


                                            <th scope="col" style="border: solid 1px; padding: 5px; font-size:11px ">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sno = 1;
                                            $total = 0;
                                            $total_gst = 0;
                                            $without_gst_total = 0;
                                        @endphp
                                        @foreach ($det as $item)
                                            @php

                                                $gross = $item->qty * $item->price;
                                                $discount = ($gross * $item->discount) / 100;
                                                $netInclusive = $gross - $discount;

                                                $netExclusive = $netInclusive / 1.18;
                                                $gst = $netInclusive - $netExclusive;

                                                $total += $netExclusive;
                                                $total_gst += $gst;

                                                $without_gst_total +=
                                                    $item->qty * $item->price -
                                                    (($item->qty * $item->price) / 100) * $item->discount;
                                            @endphp


                                            <tr style="line-height:1;">
                                                <td style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px"
                                                    @if ($data->is_order == 0) editTable="true" contenteditable="true" data-id="{{ $item->id }}"
                                        data-field="code" class="update" @endif>
                                                    {{ $item->code }}</td>

                                                <td style="border: solid 1px; padding: 5px; text-transform: uppercase; font-size:11px"
                                                    @if ($data->is_order == 0) editTable="true" contenteditable="true" data-id="{{ $item->id }}"
                                        data-field="product_code" class="update" @endif>
                                                    {{ $item->product_code }}</td>

                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px ">
                                                    {{ $item->article_no }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    <img src="/product images/{{ $item->img }}" width="45px">
                                                </td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->description }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->wattage }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->fixture_color }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->r_color }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->cct }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->beam_angle }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->cri }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->ip_rating }}</td>

                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->name }}</td>

                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ $item->qty }}</td>
                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ number_format(($item->price - ($item->price / 100) * $item->discount) / 1.18, 2) }}
                                                </td>


                                                <td
                                                    style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                    {{ number_format(($item->qty * $item->price - (($item->qty * $item->price) / 100) * $item->discount) / 1.18, 2) }}
                                                </td>


                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="15" class="text-center"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                Total</td>
                                            <td colspan="2"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                {{ number_format($total, 2) }}</td>

                                        </tr>
                                        <tr>
                                            <td colspan="15" class="text-center "
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                Freight Charges</td>
                                            <td colspan="2" class="updateQuoteValue"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px"
                                                editTable="true" contenteditable="true" data-id="{{ $data->id }}"
                                                data-field="freight_charges">
                                                {{ number_format($data->freight_charges, 2) }}</td>

                                        </tr>
                                        <tr>
                                            <td colspan="15" class="text-center"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                Sub Total</td>
                                            <td colspan="2"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                {{ number_format($data->freight_charges + $total, 2) }}</td>

                                        </tr>
                                        <tr>
                                            <td colspan="15" class="text-center"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                GST (18%)</td>
                                            <td colspan="2"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                {{ number_format($total_gst, 2) }}</td>

                                        </tr>
                                        <tr>
                                            <td colspan="15" class="text-center"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                Grand Total</td>
                                            <td colspan="2"
                                                style="border: solid 1px; padding: 5px;  text-transform: uppercase;font-size:11px">
                                                {{ number_format($total_gst + $data->freight_charges + $total, 2) }}</td>

                                        </tr>
                                    </tbody>

                                </table>
                            </div>


                        </div>
                        <div>
                            <h4 class="fw-bold"><u>Terms and Conditions</u>
                            </h4>

                            {!! $data->terms_conditions !!}

                            Above mentioned quote is valid for 15 days. <br>
                            Every product's warranty 2 years. Flos products - 5 years<br>

                            Quote is for supply only.<br>
                            Payment- 100% in advance<br>
                            Delivery - within 25-30 working days after advance payment. focus - 25-30 days, flos - 50-60
                            days<br>

                            Above mentioned prices are ex-showroom panchkula, delivery charges extra.<br>
                            All disputes subjected to Panchkula jurisdiction only.

                        </div>
                        <div>
                            <hr style="border:solid 2px; width: 100%; color:black">
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>

                            </div>
                            <div>
                                www.qu-lite.com
                            </div>
                            <div>
                                quliteledindia@gmail.com
                            </div>
                            <div>
                                Plot no. 402, Industrial Area Phase 2, <br>
                                Panchkula, Haryana, India 134109
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
