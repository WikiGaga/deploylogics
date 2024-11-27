@extends('layouts.template')
@section('title', 'Menu Maker')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['code'];
        }
        if($case == 'edit'){
            $id = $data['current']->menu_dtl_id;
            $code = $data['current']->menu_dtl_id;
            $menu_id = $data['current']->menu_id;
            $name = $data['current']->menu_dtl_name;
            $link = $data['current']->menu_dtl_link;
            $table = $data['current']->menu_dtl_table_name;
            $alignment = $data['current']->menu_dtl_sorting;
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="menu_form" class="erp_form_validation kt-form" method="post" action="{{ action('Development\MenuMakerController@store',isset($id)?$id:'') }}">
     @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-lg-3  erp-col-form-label">Code:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="menu_dtl_id" value="{{ isset($code)?$code:'' }}" class="form-control erp-form-control-sm" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3  erp-col-form-label">Menu:</label>
                                    <div class="col-lg-9">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" name="menu_id">
                                                <option value="0">Select</option>
                                                @foreach($data['menu'] as $menu)
                                                    @php  $menu_id = isset($menu_id)?$menu_id:''@endphp
                                                    <option value="{{$menu->menu_id}}" {{$menu->menu_id==$menu_id?'selected':''}}>{{$menu->menu_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3  erp-col-form-label">Name:<span class="required" aria-required="true"> * </span></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="menu_dtl_name" value="{{isset($name)?$name:''}}" maxlength="100" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3  erp-col-form-label">Link:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="menu_dtl_link" value="{{ isset($link)?$link:'' }}" maxlength="100" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3  erp-col-form-label">Table Name:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="menu_dtl_table_name" value="{{ isset($table)?$table:'' }}" maxlength="100" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3  erp-col-form-label">Alignment:</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="menu_dtl_sorting" value="{{ isset($alignment)?$alignment:'' }}" maxlength="5" class="form-control erp-form-control-sm validNumber text-left">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">
                                                Visibility and Action
                                            </h3>
                                        </div>
                                        <div class="kt-portlet__head-toolbar">
                                            <div class="kt-checkbox-list">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                                    Check All: <input type="checkbox" id="check_all_menu_permissions"><span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body" id="menu_permissions">
                                        <div class="form-group row">
                                            @if(isset($menu_id) && $menu_id == 10)
                                                @foreach($data['views_action'] as $views_action)
                                                    @if($views_action->id == 1)
                                                        @if(isset($data['permissions']))
                                                            @php $haveId = in_array($views_action->name, $data['permissions']); @endphp
                                                        @endif
                                                        @php $have = isset($haveId)? $haveId :false; @endphp
                                                        <div class="col-lg-6">
                                                            <div class="row">
                                                                <label class="col-lg-6  erp-col-form-label">{{$views_action->display_name}}:</label>
                                                                <div class="col-lg-6">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                                        <input type="checkbox" value="{{$views_action->name}}" {{ $have == true ?"checked":""  }} name="views_action[]">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach($data['views_action'] as $views_action)
                                                    @if(isset($data['permissions']))
                                                        @php $haveId = in_array($views_action->name, $data['permissions']); @endphp
                                                    @endif
                                                    @php $have = isset($haveId)? $haveId :false; @endphp
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6  erp-col-form-label">{{$views_action->display_name}}:</label>
                                                            <div class="col-lg-6">
                                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                                    <input type="checkbox" value="{{$views_action->name}}" {{ $have == true ?"checked":""  }} name="views_action[]">
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if(isset($id) && $id == 35)
                                                    @if(isset($data['permissions']))
                                                        @php $haveId2 = in_array('change_password', $data['permissions']); @endphp
                                                    @endif
                                                    @php $have2 = isset($haveId2)? $haveId2 :false; @endphp
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-6  erp-col-form-label">Change Password:</label>
                                                            <div class="col-lg-6">
                                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                                    <input type="checkbox" value="change_password" {{ $have2 == true ?"checked":""  }} name="views_action[]">
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
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
    <script src="{{ asset('js/pages/js/menu-create.js') }}" type="text/javascript"></script>
    <script>
        $('#check_all_menu_permissions').on('click', function() {
            if($(this).is(":checked") == true) {
                var checkAll = true
            }else{
                var checkAll = false
            }
            $('#menu_permissions').find('input').each(function(){
                if(checkAll) {
                    $(this).prop('checked',true)
                }else{
                    $(this).prop('checked',false)
                }
            });
        });
    </script>
@endsection
