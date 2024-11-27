@extends('layouts.template')
@section('title', 'Rent Location')

@section('pageCSS')
@endsection

@section('content')

    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){
            $id = $data['current']->rent_location_id;
            $name = trim($data['current']->rent_location_name);
            $refName = $data['current']->rent_location_ref_no;
            $mlname = $data['current']->rent_location_mother_language;
            $parentLocation = isset($data['current']->parent->rent_location_parent_id)?$data['current']->parent->rent_location_parent_id:'';
            $locationStatus = $data['current']->rent_location_entry_status;
            $code = (isset($data['code_string']->rent_location_name_code_string)?$data['code_string']->rent_location_name_code_string:'').'-'.$data['current']->rent_location_code;
        }
    @endphp
    @permission($data['permission'])
    <form id="product_group_form" class="kt-form" method="post" action="{{ action('Rent\RentLocationController@store',isset($id)?$id:'') }}">
    @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Code:</label>
                        <div class="col-lg-6">
                            <input type="text" id="rent_location_code" name="rent_location_code" value="{{isset($code)?$code:''}}" class="form-control erp-form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Parent Location:</label>
                        <div class="col-lg-6">
                            <div class="kt-input-icon kt-input-icon--right">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="rent_location_parent_id" name="rent_location_parent_id">
                                        <option value="0">Select</option>
                                        @foreach($data['parent'] as $parent)
                                            @php $parentLocation = isset($parentLocation)?$parentLocation:""; @endphp
                                            <option value="{{$parent->rent_location_id}}" {{ $parent->rent_location_id == $parentLocation?'selected':'' }}>{{$parent->rent_location_name_string}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Name:<span class="required">* </span></label>
                        <div class="col-lg-6">
                            <input type="text" name="rent_location_name" value="{{isset($name)?$name:''}}"  maxlength="100" class="form-control erp-form-control-sm checkHasValue">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Refrence No:</label>
                        <div class="col-lg-6">
                            <input type="text" name="rent_location_ref_no" value="{{isset($refName)?$refName:''}}"  maxlength="100" class="form-control erp-form-control-sm validNo">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Mother Language Name:</label>
                        <div class="col-lg-6">
                             <input type="text" dir="auto" maxlength="100" id="rent_location_mother_language_name" name="rent_location_mother_language_name" value="{{isset($mlname)?$mlname:''}}" class="form-control erp-form-control-sm checkHasValue">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Location Status:</label>
                                <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $locationStatus = isset($locationStatus)?$locationStatus:""; @endphp
                                            <input type="checkbox" name="rent_location_status" {{$locationStatus == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" checked="checked" name="rent_location_status">
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
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/product-group.js') }}" type="text/javascript"></script>
    <script>

        $("#rent_location_parent_id").change(function(){
            var pg_id = $("#rent_location_parent_id").val();
            if(pg_id)
            {
                $.ajax({
                    type:'GET',
                    url:'/rent-location/max-code/'+ pg_id,
                    success: function(response,  data)
                    {
                        if(data)
                        {
                            console.log(response);
                            var code = response['parent']['rent_location_name_code_string']+"-"+response['code'];
                            $('#rent_location_code').empty();
                            $('#rent_location_code').val(code);
                        }
                    }
                });
            }
        });
    </script>
@endsection
