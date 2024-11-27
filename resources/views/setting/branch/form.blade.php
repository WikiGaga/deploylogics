@extends('layouts.template')
@section('title', 'Branch')

@section('pageCSS')
    <link href="/assets/css/pages/wizard/wizard-1.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->branch_id;
            $name = $data['current']->branch_name;
            $short_name = $data['current']->branch_short_name;
            $short_name_arabic = $data['current']->branch_short_name_arabic;
            $name_arabic = $data['current']->branch_name_arabic;
            $city_id = $data['current']->city_id;
            $country_id = $data['current']->country_id;
            $business_id = $data['current']->business_id;
            $email = $data['current']->branch_email;
            $website = $data['current']->branch_website;
            $mobile = $data['current']->branch_mobile_no;
            $whatsapp = $data['current']->branch_whatsapp;
            $land_line= $data['current']->branch_land_line_no;
            $fax = $data['current']->branch_fax;
            $logo = $data['current']->branch_logo;
            $address = $data['current']->branch_address;
            $google_address = $data['current']->branch_google_address;
            $latitude = $data['current']->branch_latitude;
            $longitude = $data['current']->branch_longitude;
            $type_id = $data['current']->branch_type;
            $currency_id = $data['current']->branch_currency_id;
            $nature_id = $data['current']->branch_nature;
            $tax_certificate = $data['current']->branch_tax_certificate_no;
            $size = $data['current']->branch_size;
            $maximum_employment_size = $data['current']->branch_maximum_employment_size;
            $local_employment_size = $data['current']->branch_local_employment_size;
            $foreign_employment_size = $data['current']->branch_foreign_employment_size;
            $omanization_rate = $data['current']->branch_omanization_rate;
            $cr_no = $data['current']->branch_cr_no;
            $confirmation_email = $data['current']->branch_confirmation_email;
            $status = $data['current']->branch_active_status;
            $branch_account_code = $data['current']->branch_account_code;

        }
    @endphp
    @permission($data['permission'])
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid kt-wizard-v1 kt-wizard-v1--white" id="kt_wizard_v1" data-ktwizard-state="step-first">
                        <div class="kt-grid__item">

                            <!--begin: Form Wizard Nav -->
                            <div class="kt-wizard-v1__nav">

                                <!--doc: Remove "kt-wizard-v1__nav-items--clickable" class and also set 'clickableSteps: false' in the JS init to disable manually clicking step titles -->
                                <div class="kt-wizard-v1__nav-items kt-wizard-v1__nav-items--clickable">
                                    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step" data-ktwizard-state="current">
                                        <div class="kt-wizard-v1__nav-body">
                                            <div class="kt-wizard-v1__nav-icon">
                                                <i class="flaticon-bus-stop"></i>
                                            </div>
                                            <div class="kt-wizard-v1__nav-label">
                                                Branch Detail
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
                                        <div class="kt-wizard-v1__nav-body">
                                            <div class="kt-wizard-v1__nav-icon">
                                                <i class="flaticon-list"></i>
                                            </div>
                                            <div class="kt-wizard-v1__nav-label">
                                                Financial & Employment
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kt-wizard-v1__nav-item" data-ktwizard-type="step">
                                        <div class="kt-wizard-v1__nav-body">
                                            <div class="kt-wizard-v1__nav-icon">
                                                <i class="flaticon2-check-mark"></i>
                                            </div>
                                            <div class="kt-wizard-v1__nav-label">
                                                Confirmation
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end: Form Wizard Nav -->
                        </div>
                        <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v1__wrapper">

                            <!--begin: Form Wizard Form-->
                            <form class="kt-form" id="kt_form" method="post" action="{{ action('Setting\BranchController@store', isset($id)?$id:"") }}">
                                @csrf
                                <!--begin: Form Wizard Step 1-->
                                    <div class="kt-wizard-v1__content" data-ktwizard-type="step-content" data-ktwizard-state="current">
                                        <div class="kt-form__section kt-form__section--first">
                                            <div class="kt-wizard-v1__form">
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Name:<span class="required" aria-required="true"> * </span></label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_name" value="{{isset($name)?$name:''}}" class="form-control erp-form-control-sm small_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Short Name:<span class="required" aria-required="true"> * </span></label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_short_name" value="{{isset($short_name)?$short_name:''}}" class="form-control erp-form-control-sm short_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Local Name:<span class="required" aria-required="true"> * </span></label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_name_arabic" value="{{isset($name_arabic)?$name_arabic:''}}" class="form-control erp-form-control-sm small_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Short Local Name:<span class="required" aria-required="true"> * </span></label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_short_name_arabic" value="{{isset($short_name_arabic)?$short_name_arabic:''}}" class="form-control erp-form-control-sm short_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Company:<span class="required" aria-required="true"> * </span></label>
                                                            <div class="col-lg-6">
                                                                <div class="erp-select2">
                                                                    <select class="form-control kt-select2 erp-form-control-sm" name="business_id">
                                                                        <option value="0">Select</option>
                                                                        @php $business_id = isset($business_id)?$business_id:''@endphp
                                                                        @foreach($data['business'] as $business)
                                                                            <option value="{{$business->business_id}}" {{$business->business_id == $business_id?'selected':''}}>{{$business->business_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">City:</label>
                                                            <div class="col-lg-6">
                                                                <div class="erp-select2">
                                                                    <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="branch_city">
                                                                        <option value="0">Select</option>
                                                                        @php $city_id = isset($city_id)?$city_id:''@endphp
                                                                        @foreach($data['city'] as $country)
                                                                            <optgroup label="{{$country->country_name}}">
                                                                                @foreach($country->country_cities as $city)
                                                                                    <option value="{{$city->city_id}}" {{$city->city_id == $city_id?'selected':''}}>{{$city->city_name}}</option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Email:<span class="required" aria-required="true"> * </span></label>
                                                            <div class="col-lg-6">
                                                                <input type="email" name="branch_email" value="{{isset($email)?$email:''}}" class="form-control erp-form-control-sm small_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_mobile_no" value="{{isset($mobile)?$mobile:''}}" class="form-control erp-form-control-sm mob_no validNumber text-left">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">WhatsApp No:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_whatsapp_no" value="{{isset($whatsapp)?$whatsapp:''}}" class="form-control erp-form-control-sm mob_no validNumber text-left">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Land Line No:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_land_line_no" value="{{isset($land_line)?$land_line:''}}" class="form-control erp-form-control-sm mob_no validNumber text-left">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Website:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_website" value="{{isset($website)?$website:''}}" class="form-control erp-form-control-sm small_text">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Fax:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_fax" value="{{isset($fax)?$fax:''}}" class="form-control erp-form-control-sm mob_no validNumber text-left">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Address:</label>
                                                            <div class="col-lg-6">
                                                                <textarea type="text" name="branch_address"  class="form-control erp-form-control-sm large_text" rows="3">{{isset($address)?$address:''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Logo:</label>
                                                            <div class="col-lg-6">
                                                                @php
                                                                    $branch_profile = isset($logo)?'/images/'.$logo:"";
                                                                @endphp
                                                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                                                    @if($branch_profile)
                                                                        <div class="kt-avatar__holder" style="background-image: url({{$branch_profile}})"></div>
                                                                    @else
                                                                        <div class="kt-avatar__holder" style="background-image: url(/assets/media/project-logos/7.png)"></div>
                                                                    @endif
                                                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                                        <i class="fa fa-pen"></i>
                                                                        <input type="file" name="branch_profile" accept="image/png, image/jpg, image/jpeg">
                                                                    </label>
                                                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                                                        <i class="fa fa-times"></i>
                                                                    </span>
                                                                </div>
                                                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <label class="col-lg-3 erp-col-form-label">Google Map Address:</label>
                                                    <div class="col-lg-12">
                                                        <input class="form-control erp-form-control-sm large_text input-sm pac-target-input" type="text" value="{{isset($google_address)?$google_address:''}}"  name="branch_google_address" id="pac-input" placeholder="Enter a location" onchange="getltln('pac-input');" autocomplete="off" style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); font-weight: normal;">
                                                        <div>Latitude: <span id="res">{{isset($latitude)?$latitude:''}}</span></div>
                                                        <div>longitude: <span id="res2">{{isset($longitude)?$longitude:''}}</span></div>
                                                        <input type="hidden" name="branch_latitude" id="savelatitude" value="{{isset($latitude)?$latitude:'31.582045'}}">
                                                        <input type="hidden" name="branch_longitude" id="savelongitude" value="{{isset($longitude)?$longitude:'74.329376'}}">
                                                        <script type="text/javascript">

                                                            function getltln(p) {
                                                                //alert('sl');
                                                                //console.log('sl');
                                                                //console.log(p);
                                                                var geocoder = new google.maps.Geocoder();
                                                                var address = document.getElementById(p).value;

                                                                geocoder.geocode( { 'address': address}, function(results, status) {

                                                                    if (status == google.maps.GeocoderStatus.OK) {
                                                                        var latitude = results[0].geometry.location.lat();
                                                                        var longitude = results[0].geometry.location.lng();
                                                                        document.getElementById('res').innerHTML=latitude;
                                                                        document.getElementById('res2').innerHTML=longitude;
                                                                        document.getElementById('savelatitude').value=latitude;
                                                                        document.getElementById('savelongitude').value=longitude;


                                                                        var myLatlng = new google.maps.LatLng(latitude,longitude);
                                                                        var mapOptions = {
                                                                            zoom: 17,
                                                                            center: myLatlng
                                                                        }
                                                                        var map = new google.maps.Map(document.getElementById("map"), mapOptions);

                                                                        var marker = new google.maps.Marker({
                                                                            position: myLatlng,
                                                                            title:"Hello World!",
                                                                        });

                                                                        // To add the marker to the map, call setMap();
                                                                        marker.setMap(map);


                                                                        //var infowindow = new google.maps.InfoWindow();
                                                                        //var infowindowContent = document.getElementById('infowindow-content');
                                                                        //infowindow.setContent(infowindowContent);
                                                                        var marker = new google.maps.Marker({
                                                                            map: map,
                                                                            anchorPoint: new google.maps.Point(0, -29)
                                                                        });

                                                                        //infowindowContent.children['place-address'].textContent = address;
                                                                        //infowindow.open(map, marker);


                                                                    }
                                                                });


                                                            }

                                                            // This example requires the Places library. Include the libraries=places
                                                            // parameter when you first load the API. For example:
                                                            // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

                                                            function initMap() {
                                                                //  alert('s');
                                                                var map = new google.maps.Map(document.getElementById('map'), {
                                                                    center: {lat: 31.582045, lng: 74.329376},
                                                                    zoom: 14
                                                                });

                                                                var card = document.getElementById('pac-card');
                                                                var input = document.getElementById('pac-input');
                                                                var types = document.getElementById('type-selector');
                                                                var strictBounds = document.getElementById('strict-bounds-selector');

                                                                map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

                                                                var autocomplete = new google.maps.places.Autocomplete(input);

                                                                // Bind the map's bounds (viewport) property to the autocomplete object,
                                                                // so that the autocomplete requests use the current map bounds for the
                                                                // bounds option in the request.
                                                                autocomplete.bindTo('bounds', map);

                                                                var infowindow = new google.maps.InfoWindow();
                                                                var infowindowContent = document.getElementById('infowindow-content');
                                                                infowindow.setContent(infowindowContent);
                                                                var marker = new google.maps.Marker({
                                                                    map: map,
                                                                    anchorPoint: new google.maps.Point(0, -29)
                                                                });

                                                                autocomplete.addListener('place_changed', function() {
                                                                    infowindow.close();
                                                                    marker.setVisible(false);
                                                                    var place = autocomplete.getPlace();
                                                                    if (!place.geometry) {
                                                                        // User entered the name of a Place that was not suggested and
                                                                        // pressed the Enter key, or the Place Details request failed.
                                                                        window.alert("No details available for input: '" + place.name + "'");
                                                                        return;
                                                                    }

                                                                    // If the place has a geometry, then present it on a map.
                                                                    if (place.geometry.viewport) {
                                                                        map.fitBounds(place.geometry.viewport);
                                                                    } else {
                                                                        map.setCenter(place.geometry.location);
                                                                        map.setZoom(17);  // Why 17? Because it looks good.
                                                                    }
                                                                    marker.setPosition(place.geometry.location);
                                                                    marker.setVisible(true);

                                                                    var address = '';
                                                                    if (place.address_components) {
                                                                        address = [
                                                                            (place.address_components[0] && place.address_components[0].short_name || ''),
                                                                            (place.address_components[1] && place.address_components[1].short_name || ''),
                                                                            (place.address_components[2] && place.address_components[2].short_name || '')
                                                                        ].join(' ');
                                                                    }

                                                                    /*infowindowContent.children['place-icon'].src = place.icon;
                                                                    infowindowContent.children['place-name'].textContent = place.name;
                                                                    infowindowContent.children['place-address'].textContent = address;
                                                                    infowindow.open(map, marker);*/
                                                                });

                                                                // Sets a listener on a radio button to change the filter type on Places
                                                                // Autocomplete.
                                                                /*function setupClickListener(id, types) {
                                                                    var radioButton = document.getElementById(id);
                                                                    radioButton.addEventListener('click', function() {
                                                                        autocomplete.setTypes(types);
                                                                    });
                                                                }

                                                                setupClickListener('changetype-all', []);
                                                                setupClickListener('changetype-address', ['address']);
                                                                setupClickListener('changetype-establishment', ['establishment']);
                                                                setupClickListener('changetype-geocode', ['geocode']);

                                                                document.getElementById('use-strict-bounds')
                                                                    .addEventListener('click', function() {
                                                                        console.log('Checkbox clicked! New state=' + this.checked);
                                                                        autocomplete.setOptions({strictBounds: this.checked});
                                                                    });*/
                                                            }
                                                        </script>
                                                        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsg3oFV_HSQJqndKVAINg6NPbt6vgfBWo&amp;libraries=places&amp;callback=initMap" async="" defer=""></script>
                                                        <div id="map"  style="height:250px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--end: Form Wizard Step 1-->

                                    <!--begin: Form Wizard Step 2-->
                                    <div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
                                        <div class="kt-form__section kt-form__section--first">
                                            <div class="kt-wizard-v1__form">
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Type:</label>
                                                            <div class="col-lg-6">
                                                                <div class="erp-select2">
                                                                    <select class="form-control kt-select2 erp-form-control-sm" id="business_type" name="business_type">
                                                                        <option value="0">Select</option>
                                                                        @php $type_id = isset($type_id)?$type_id:'' @endphp
                                                                        @foreach($data['type'] as $type)
                                                                            <option value="{{$type->business_type_id}}" {{$type->business_type_id == $type_id?'selected':''}}>{{$type->business_type_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Default Currency:</label>
                                                            <div class="col-lg-6">
                                                                <div class="erp-select2">
                                                                    <select class="form-control kt-select2 erp-form-control-sm" id="branch_currency" name="branch_currency">
                                                                        <option value="0">Select</option>
                                                                        @php $currency_id = isset($currency_id)?$currency_id:'' @endphp
                                                                        @foreach($data['currency'] as $currency)
                                                                            <option value="{{$currency->currency_id}}" {{$currency->currency_id == $currency_id?'selected':''}}>{{$currency->currency_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Nature:</label>
                                                            <div class="col-lg-6">
                                                                <div class="erp-select2">
                                                                    <select class="form-control kt-select2 erp-form-control-sm" id="business_nature" name="business_nature">
                                                                        <option value="0">Select</option>
                                                                        @php $nature_id = isset($nature_id)?$nature_id:'' @endphp
                                                                        @foreach($data['nature'] as $nature)
                                                                            <option value="{{$nature->business_nature_id}}" {{$nature->business_nature_id == $nature_id?'selected':''}}>{{$nature->business_nature_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Tax Certificate No:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_tax_certificate_no" value="{{isset($tax_certificate)?$tax_certificate:''}}" class="form-control erp-form-control-sm short_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Central Rate:</label>
                                                            <div class="col-lg-6">
                                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                    <label>
                                                                        <input type="checkbox" name="branch_size" {{$size==1?"checked":""}}>
                                                                        <span></span>
                                                                    </label>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Maximum Employment Size:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_maximum_employment_size" value="{{isset($maximum_employment_size)?$maximum_employment_size:''}}" class="form-control erp-form-control-sm medium_no validNumber">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Local Employment Size:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_local_employment_size" value="{{isset($local_employment_size)?$local_employment_size:''}}" class="form-control erp-form-control-sm medium_no validNumber">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Foreign Employment Size:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_foreign_employment_size" value="{{isset($foreign_employment_size)?$foreign_employment_size:''}}" class="form-control erp-form-control-sm medium_no validNumber">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Omanization Rate:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_omanization_rate" value="{{isset($omanization_rate)?$omanization_rate:''}}" class="form-control erp-form-control-sm medium_no validNumber">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">CR NO:</label>
                                                            <div class="col-lg-6">
                                                                <input type="text" name="branch_cr_no" value="{{isset($cr_no)?$cr_no:''}}" class="form-control erp-form-control-sm short_text">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Branch Account Code:</label>
                                                            <div class="col-lg-6">
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="branch_account_code" name="branch_account_code">
                                                                        <option value="0">Select</option>
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            @php $branch_account_code = isset($branch_account_code)?$branch_account_code:'' @endphp
                                                                            <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id == $branch_account_code ?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6 erp-col-form-label">Status:</label>
                                                            <div class="col-lg-6">
                                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                    <label>
                                                                        @if($case == 'edit')
                                                                            @php $entry_status = isset($status)?$status:""; @endphp
                                                                            <input type="checkbox" name="branch_active_status" {{$entry_status==1?"checked":""}}>
                                                                        @else
                                                                            <input type="checkbox" name="branch_active_status" checked>
                                                                        @endif
                                                                        <span></span>
                                                                    </label>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--end: Form Wizard Step 2-->

                                    <!--begin: Form Wizard Step 3-->
                                    <div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
                                        <div class="kt-form__section kt-form__section--first">
                                            <div class="kt-wizard-v1__form">
                                                <div class="form-group-block row">
                                                    <label class="col-lg-6 erp-col-form-label">Enter Email for the Confirmation of Activation of the Branch:</label>
                                                    <div class="col-lg-6">
                                                        <input type="email" name="branch_confirmation_email" value="{{isset($confirmation_email)?$confirmation_email:''}}" class="form-control erp-form-control-sm small_text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--end: Form Wizard Step 3-->

                                    <!--begin: Form Actions -->
                                    <div class="kt-form__actions">
                                        <button class="btn btn-secondary btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-prev">
                                            Previous
                                        </button>
                                        <button class="btn btn-success btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-submit">
                                            Submit
                                        </button>
                                        <button class="btn btn-brand btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-next">
                                            Next Step
                                        </button>
                                    </div>

                                <!--end: Form Actions -->
                            </form>

                            <!--end: Form Wizard Form-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/branch.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')

@endsection
