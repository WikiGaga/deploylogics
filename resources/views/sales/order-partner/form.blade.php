@extends('layouts.template')
@section('title', 'OrderPartner')

@section('pageCSS')
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['partner_code'];
                $membership_type = 0;
                $member_status = 0;
            }
            if($case == 'edit'){
                $id = $data['current']->partner_id;
                $code = $data['current']->partner_code;
                $name = $data['current']->partner_name;
                $local_name = $data['current']->partner_local_name;
                $type = $data['current']->partner_type;
                $status = $data['current']->partner_entry_status;
                $image = $data['current']->partner_image;
                $referenced_by = $data['current']->referenced_by;
                $home_delivery = $data['current']->home_delivery;
                $address = $data['current']->partner_address;
                $latitude = $data['current']->partner_latitude;
                $longitude = $data['current']->partner_longitude;
                $city_id = $data['current']->city_id;
                $region_id = $data['current']->region_id;
                $zip_code = $data['current']->partner_zip_code;
                $contact_person = $data['current']->partner_contact_person;
                $contact_person_mobile = $data['current']->partner_contact_person_mobile;
                $po_box = $data['current']->partner_po_box;
                $phone_1 = $data['current']->partner_phone_1;
                $mobile_no = $data['current']->partner_mobile_no;
                $fax = $data['current']->partner_fax;
                $whatapp_no = $data['current']->partner_whatapp_no;
                $email = $data['current']->partner_email;
                $website = $data['current']->partner_website;
                $remarks = $data['current']->partner_remarks;
                $membership_type = $data['current']->membership_type_id;
                $member_status = $data['current']->member_status;
                $partner_account_id = $data['current']->partner_account_id;
                $strn_no = $data['current']->partner_strn_no;
                $can_scale = $data['current']->partner_can_scale;
                $additional_tax = $data['current']->partner_additional_tax;
                $contact_persons = isset($data['current']->contact_person) ? $data['current']->contact_person : [] ;
                $partner_branches = isset($data['current']->partner_branches) ? $data['current']->partner_branches : [] ;
            }
    @endphp
    @permission($data['permission'])
    <form id="customer_form" class="kt-form" method="post" action="{{ action('Sales\OrderPartnerController@store',isset($id)?$id:"") }}" enctype="multipart/form-data">
        @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <div class="col-lg-6">
                        <div class="erp-page--title">
                            {{isset($code)?$code:""}}
                        </div>
                        <div>
                            @if(isset($partner_account_id))
                                Account Id: <b>{{$partner_account_id}}</b>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label"> Name: <span class="required"> * </span></label>
                            <div class="col-lg-6">
                                <input type="text" name="partner_name" class="form-control erp-form-control-sm medium_text" value="{{isset($name)?$name:""}}">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Local Name:</label>
                            <div class="col-lg-6">
                                <input type="text" onkeyup="arabicValue(partner_local_name)" dir="rtl" name="partner_local_name" class="form-control erp-form-control-sm medium_text" value="{{isset($local_name)?$local_name:""}}">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Partner Type: <span class="required"> * </span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2 form-group">
                                    <select class="form-control erp-form-control-sm kt-select2" id="partner_type" name="partner_type">
                                        <option value="0">Select</option>
                                        
                                        @foreach($data['type'] as $list_type)
                                            @php $select_type = isset($type)?$type:0; @endphp
                                            <option value="{{$list_type->customer_type_id}}" {{$list_type->customer_type_id == $select_type ?"selected":""}}>{{$list_type->customer_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group-block row">
                            <label class="col-lg-6 erp-col-form-label">Active:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:0; @endphp
                                            <input type="checkbox" name="partner_entry_status" {{ $entry_status == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="partner_entry_status" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label"></label>
                            <div class="col-lg-6">
                                @php
                                    $image_url = isset($image)?'/images/'.$image:"";
                                @endphp
                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                    @if($image_url)
                                        <div class="kt-avatar__holder" style="background-image: url({{$image_url}})"></div>
                                    @else
                                        <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                    @endif
                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change image">
                                        <i class="fa fa-pen"></i>
                                        <input type="file" name="partner_image" accept="image/png, image/jpg, image/jpeg">
                                    </label>
                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel image">
                                            <i class="fa fa-times"></i>
                                        </span>
                                </div>
                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                            </div>
                        </div>
                    </div>
                </div>{{-- end row--}}
                <ul class="erp-main-nav nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#generalinfo" role="tab">General Information</a>
                    </li>
                   
                </ul>
                <div class="tab-content">

                    <div class="tab-pane active" id="generalinfo" role="tabpanel">

                        <div class="form-group-block row">
                            
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Home Delivery: <span class="required"> * </span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2 form-group">
                                            <select class="form-control erp-form-control-sm kt-select2" id="home_delivery" name="home_delivery">
                                                <option value="0">Select</option>
                                               
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="form-group-block row">
                                    <label class="col-lg-3 erp-col-form-label">Customer Address:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" rows="2" name="partner_address" class="form-control erp-form-control-sm double_text">{{isset($address)?$address:""}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                     
                        {{-- end row--}}
                       
                        {{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">City : <span class="required">*</span></label>
                                    <div class="col-lg-6" >
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="city_id" name="city_id">
                                                <option value="0">Select</option>
                                                @foreach($data['city'] as $country)
                                                    <optgroup label="{{$country->country_name}}">
                                                        @foreach($country->country_cities as $city)
                                                            @php $select_city_id = isset($city_id)?$city_id:0; @endphp
                                                            <option value="{{$city->city_id}}" {{$city->city_id == $select_city_id ?"selected":""}}>{{$city->city_name}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Zip Code :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_zip_code" class="form-control erp-form-control-sm large_no validNumber text-left" value="{{isset($zip_code)?$zip_code:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Contact Person Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_contact_person_name" class="form-control erp-form-control-sm small_text" value="{{isset($contact_person)?$contact_person:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Contact Person Mobile No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_contact_person_mobile_no" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($contact_person_mobile)?$contact_person_mobile:""}}">
                                    </div>
                                </div>
                            </div>
                           
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">PO Box :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_po_box" class="form-control erp-form-control-sm mob_no validNumber text-left"  value="{{isset($po_box)?$po_box:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Phone No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_phone_1" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($phone_1)?$phone_1:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_mobile_no" class="form-control erp-form-control-sm mob_no validNumber text-left"  value="{{isset($mobile_no)?$mobile_no:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">FAX  :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_fax" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($fax)?$fax:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Whatsapp No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_whatapp_no" class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{isset($whatapp_no)?$whatapp_no:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Email :</label>
                                    <div class="col-lg-6">
                                        <input type="email" name="partner_email" class="form-control erp-form-control-sm small_text" value="{{isset($email)?$email:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Website :</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="partner_website" class="form-control erp-form-control-sm small_text" value="{{isset($website)?$website:""}}">

                                    </div>
                                </div>
                            </div>
                            
                        </div>{{-- end row--}}
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="form-group-block row">
                                    <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" rows="2" name="partner_remarks" class="form-control erp-form-control-sm double_text">{{isset($remarks)?$remarks:""}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                    </div>{{--tabend--}}

                    {{--@include('sales.customer.tabs')--}}
                </div>
            </div>
        </div>
    </div>
    </form>
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <!-- <script>
        $('#city_id').on('change',function(e){
            var city_id = $(this).val();
            if(city_id != "0"){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '{{ route("getAreaByCityId") }}',
                    method : 'POST',
                    data : {"city_id" : city_id},
                    async : false,
                    beforeSend : function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success : function(response,status){
                        $('body').removeClass('pointerEventsNone');
                        $('#customer_area_id').html('');
                        if(response.status == 'success'){
                            var areas = response.data;
                            var option = '';
                            option += '<option value="0">Select</option>';
                            areas.forEach((el) => {
                                option += '<option value="'+ el.area_id +'">'+el.area_name+'</option>';
                            });
                            $('#customer_area_id').append(option);
                        }else{
                            toastr.error('No Areas In This City');
                        }
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error(response.responseJSON.message);
                    },
                });
            }
        });
    </script> -->
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/customer.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        $('#selectItemsBtn').on('click',function (e) {
            e.preventDefault();
            var selected=[];
            $('#selectItems :selected').each(function(){
                selected.push($(this).val());
            });
            var url = ""
            for(var i=0;selected.length>i;i++){
                url += "/"+selected[i];
            }
            if(url == ""){
                alert("Please Select Items");
                return false;
            }
            var data_url = '/select-items'+url;

            $('#kt_modal_KTDatatable_local').modal('show').find('.modal-content').load(data_url);
            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });

        })


        var arr_text_Field = [
            // keys = id, fieldClass, message, readonly(boolean), require(boolean)
            {
                'id':'contactp_dtl_name',
                'fieldClass':'moveIndex medium_text',
                'require':true,
                'message':'Enter Name'
            },{
                'id':'contactp_dtl_cont_no',
                'fieldClass':'moveIndex mob_no validNumber text-left',
            },
            {
                'id':'contactp_dtl_address',
                'fieldClass':'moveIndex double_text',
            }
        ];
        var  arr_hidden_field = ['contactp_dtl_id'];

        var arr_customer_text_Field = [
            {
                'id':'customer_name',
                'fieldClass':'moveIndex medium_text field_readonly',
                'require':true,
                'message':'Enter Name'
            },{
                'id':'customer_contact',
                'fieldClass':'moveIndex mob_no validNumber text-left field_readonly',
            },{
                'id':'customer_address',
                'fieldClass':'moveIndex double_text field_readonly',
            }
        ];
        var  arr_customer_hidden_field = ['customer_id'];




function sendWhatsAppMessage() {

var button = document.getElementById("whatsappmessagebtn");
if (button) {
    buttonIcons = button.innerHTML;
    button.disabled = true;
    button.textContent = 'Sending..';
}

// var cust_code = $('#customer_id').val();
var cust_code = @json($id);
// var amount = $('#pro_tot').val();
var title = @json($data['page_data']['title']);

 console.log(cust_code);
//  console.log(amount);
 console.log(title);


$.ajax({
    url: '/customer/fetch-customer-info',
    type: 'GET',
    data: {
        cust_code: cust_code
    },
    success: function(response) {
        const data = response;

        if (!data || !data.phone) {
            toastr.error("Customer phone number not found");
            if (button) {
                button.innerHTML = buttonIcons;
                button.disabled = false;
            }
            return;
        }

        var to = formatPakPhoneNumber(data.phone);
        const invoiceNumber = @json($code);
        // const invoiceDate = EntryDate;

        // console.log(to);
        // return false;

        to = formatPakPhoneNumber('03097274927');
        // to = formatOmanPhoneNumber('9156 4500');

        // generatePdfAttachment(CODE, 'SI', EntryDate, ReportType, CompCode, function(filePath) {
        //     if (!filePath) {
        //         console.error("Error generating PDF");
        //         filePath = '';
        //     }
        // });

                filePath = '';
        const message = `Thank you for your valued order\n(*Order no # ${invoiceNumber}*.\n\nThank you and regards,\nwww.deploylogics.com`;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/customer/whatsapp-message-sending',
                type: 'POST',
                data: {
                    to: to,
                    message: message,
                    filePath: filePath,
                    invoiceNumber: invoiceNumber,
                    title: title,
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        toastr.success("WhatsApp message sent at " + to);
                        if (button) {
                        button.innerHTML = buttonIcons + '<i class="icon wb-check" aria-hidden="true"></i>';
                        button.disabled = false;
                        }
                    } else {
                        toastr.error("Failed to send message check connection");
                        if (button) {
                        button.innerHTML = buttonIcons;
                        button.disabled = false;
                        }
                    }

                },
                error: function() {
                    toastr.error("Failed to send WhatsApp message.");
                    if (button) {
                    button.innerHTML = buttonIcons;
                    button.disabled = false;
                    }
                }
            });
    },
    error: function() {
        toastr.error("Failed to fetch customer data.");
        if (button) {
        button.innerHTML = buttonIcons;
        }
    }

});

}




    </script>
    <script src="{{ asset('js/pages/js/sale/customer_row_repeat.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>  --}}
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script>
        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }

        $('.issue_date, .issue_date_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });
        $('.expiry_date, .expiry_date_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });
    </script>
@endsection

