@extends('layouts.template')
@section('title', 'Stock Location')

@section('pageCSS')
@endsection

@section('content')

    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){
            $id = $data['current']->stock_location_id;
            $name = $data['current']->stock_location_name;
            $parentGroup = $data['current']->stock_location_parent_group_id;
            $code = (isset($parentGroup)?$parentGroup.'-':'').$data['current']->stock_location_code ;
        }
    @endphp
    @permission($data['permission'])
    <form id="stock_location_form" class="kt-form" method="post" action="{{ action('Purchase\StockLocationController@store',isset($id)?$id:'') }}">
    @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Code:</label>
                        <div class="col-lg-6">
                            <input type="text" id="stock_location_code" name="stock_location_code" value="{{isset($code)?$code:''}}" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Name:<span class="required">* </span></label>
                        <div class="col-lg-6">
                            <input type="text" name="stock_location_name" value="{{isset($name)?$name:''}}"  maxlength="100" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Select Parent Group:</label>
                        <div class="col-lg-6">
                            <div class="erp-select2 form-group">
                                <select class="form-control kt-select2 form-control-sm" id="parent_group_id" name="parent_group_id">
                                    <option value="0">Select</option>
                                    @foreach($data['display_location'] as $display_location)
                                        <option value="{{$display_location->display_location_id}}">{{$display_location->display_location_name_string}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  row">
                            <label class="col-lg-3 col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        <input type="checkbox" checked="checked" name="stock_location_entry_status">
                                        <span></span>
                                    </label>
                                </span>
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
    <script src="{{ asset('js/pages/js/stock-location-form.js') }}" type="text/javascript"></script>
    <script>

        $("#parent_group_id").change(function(){
            var pg_id = $("#parent_group_id").val();

            if(pg_id)
            {

                 $.ajax({
                            type:'GET',
                            url:'/stock-location/max-code/'+ pg_id,
                            success: function(response,  data)
                            {
                                if(data)
                                {
                                    var code = pg_id+"-"+response['code'];
                                    $('#stock_location_code').empty();
                                    $('#stock_location_code').val(code);
                                }
                            }
                        });
            }
        });

    </script>
@endsection
