@php
//essential for header
    $heading = strtoupper($data['title']);
    $businessName = strtoupper(auth()->user()->business->business_short_name);
    $pdf_link = $data['print_link'];
    $print_type = $data['type'];

    $code = $data['current']->agreement_code;
    $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->rent_agreement_date))));
    $startDate = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->rent_agreement_start_date))));
    $endDate = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->rent_agreement_end_date))));
    $period = $data['current']->rent_agreement_period;
    $amount = $data['current']->rent_agreement_amount;
    $totalRent = $data['current']->rent_agreement_total_rent;
    $advance = $data['current']->rent_advance_paid;
    $opening_balance = $data['current']->rent_opening_balance;
    $remarks = $data['current']->rent_agreement_remarks;
    $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
    $city = isset($data['current']->city_id) ? $data['current']->city_id :'';
    $location = isset($data['current']->location->rent_location_name) ? $data['current']->location->rent_location_name :'';
    $firstParty = isset($data['current']->firstParty) ? $data['current']->firstParty :[];
    $secondParty = isset($data['current']->secondParty) ? $data['current']->secondParty :[];
@endphp
@permission($data['permission'])
    @extends('layouts.print_layout')
    @section('title', $heading)
    @section('heading', $heading)

    @section('pageCSS')
    @endsection

    @section('content')
    <table class="tableData" style="margin-top: 5px">
        <tbody>
            <tr>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Code :</span>
                        <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Agreement Date:</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Agreement Start Date:</span>
                        <span class="normal normal-block">{{isset($startDate)?$startDate:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Agreement End Date:</span>
                        <span class="normal normal-block">{{isset($endDate)?$endDate:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Agreement Period:</span>
                        <span class="normal normal-block">{{isset($period)?$period:''}} Months</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Rent Amount:</span>
                        <span class="normal normal-block">{{isset($amount)?$amount:''}} OMR</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Advance Paid:</span>
                        <span class="normal normal-block">{{isset($advance)?$advance:''}} OMR</span>
                    </div>
                </td>
                <td width="33.33%"></td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">First Party:</span>
                        <span class="normal normal-block">{{isset($firstParty->party_name)?$firstParty->party_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Second Party:</span>
                        <span class="normal normal-block">{{isset($secondParty->party_name)?$secondParty->party_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Location:</span>
                        <span class="normal normal-block">{{isset($location)?$location:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">City:</span>
                        <span class="normal normal-block">{{isset($city)?$city:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Opening Balance:</span>
                        <span class="normal normal-block">{{isset($opening_balance)?number_format($opening_balance , 3):''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Total Rent:</span>
                        <span class="normal normal-block">{{isset($totalRent)?$totalRent:''}} OMR</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <table  class="tableData data_listing" id="document_table_data" style="margin-top: 10px">
        <thead>
            <tr>
                <th class="dtl-head">Sr #</th>
                <th class="dtl-head alignleft">Date</th>
                <th class="dtl-head aligncenter">Amount</th>
                <th class="dtl-head aligncenter">Discount</th>
                <th class="dtl-head aligncenter">Balance</th>
                <th class="dtl-head">Description</th>
                <th class="dtl-head">Status</th>
                <th class="dtl-head">Paid Date</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($dtls))
                @php $rentAmount = $rentDiscount = $rentBalance = $rentPaid = 0; @endphp
                @foreach($dtls as $dtl)
                    @php
                        $rentAmount += $dtl->rent_agreement_dtl_amount;
                        $rentDiscount += $dtl->rent_agreement_dtl_discount;
                        $rentBalance += $dtl->rent_agreement_dtl_balance;
                        if($dtl->rent_agreement_dtl_status == 1){
                            $rentPaid += $dtl->rent_agreement_dtl_amount;
                        }
                    @endphp
                    <tr>
                        <td class="dtl-contents aligncenter">{{ $loop->iteration }}</td>
                        <td class="dtl-contents">{{ date('d-m-Y' , strtotime($dtl->rent_agreement_dtl_date) ) }}</td>
                        <td class="dtl-contents alignright">{{ $dtl->rent_agreement_dtl_amount }}</td>
                        <td class="dtl-contents alignright">{{ $dtl->rent_agreement_dtl_discount }}</td>
                        <td class="dtl-contents alignright">{{ $dtl->rent_agreement_dtl_balance }}</td>
                        <td class="dtl-contents">{{ $dtl->rent_agreement_dtl_desc }}</td>
                        <td class="dtl-contents aligncenter">@if($dtl->rent_agreement_dtl_status == 1) Paid @else Unpaid @endif</td>
                        <td class="dtl-contents aligncenter">{{ isset($dtl->voucher_date) ? date('d-m-Y' , strtotime($dtl->voucher_date)) : '' }}</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td class="dtl-head alignright">Total:</td>
                <td class="dtl-contents"></td>
                <td class="dtl-contents alignright">{{ number_format($rentAmount, 3) }}</td>
                <td class="dtl-contents alignright">{{ number_format($rentDiscount , 3) }}</td>
                <td class="dtl-contents alignright">{{ number_format($rentBalance , 3) }}</td>
                <td class="dtl-contents"></td>
                <td class="dtl-contents alignright"></td>
                <td class="dtl-contents"></td>
            </tr>
        </tbody>
    </table>
    <br/><br/>
    <table class="tab">
        <tbody>
            <tr>
                <th class="heading alignright">Total Paid Amount: {{ number_format($rentPaid , 3) }} OMR</th>
            </tr>
        </tbody>
    </table>
    <table class="tab">
        <tbody>
            <tr>
                <th class="heading alignleft">Remarks:</th>
            </tr>
            <tr>
                <td class="normal alignleft paddingNotes">{{$remarks}}</td>
            </tr>
        </tbody>
    </table>
    @endsection

    @section('customJS')
    @endsection
@endpermission
