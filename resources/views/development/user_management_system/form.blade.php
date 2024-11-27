@extends('layouts.template')
@section('title', 'User Management System')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $disabled = true;
            $id = 0;
            $id2 = 0;
        }
        if($case == 'edit'){
            $disabled = false;
            $id = $data['id'];
            $id2 = $data['id2'];
        }

        $type_id = isset($data['type_id']) ? $data['type_id'] : "";

        if($type_id == "edit"){
            $disabled = false;
            $id = $data['id'];
            $id2 = $data['id2'];
        }

    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="user_management_system_form" class="erp_form_validation kt-form" method="post" action="{{ action('Development\UserManagementSystemController@store') }}">
     @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <label class="col-lg-2 erp-col-form-label">User:</label>
                            <div class="col-lg-4">
                                <div class="erp-select2">
                                    <select class="form-control erp-form-control-sm kt-select2" id="user_id" name="user_id">
                                        <option value="0">Select</option>
                                        @foreach($data['users'] as $user)
                                            <option value="{{$user->id}}" {{ $id == $user->id?'selected':''}} >{{ucwords(strtolower($user->name))}} - {{strtolower($user->email)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2"></div>
                            <div class="col-lg-4">
                                <div class="erp-select2">
                                    <select class="form-control erp-form-control-sm kt-select2" id="user_id2" name="user_id2">
                                        <option value="0">Select</option>
                                        @foreach($data['users'] as $user)
                                            <option value="{{$user->id}}" {{ $id2 == $user->id?'selected':''}}>{{ucwords(strtolower($user->name))}} - {{strtolower($user->email)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-2 erp-col-form-label">
                                Check/Uncheck All:
                            </label>
                            <div class="col-lg-4">
                                <div class="erp-select2">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            <input type="checkbox" id="check_all" name="check_all" {{ $disabled == true ?"disabled":""  }}>
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-2 erp-col-form-label">
                                Show Dashboard:
                            </label>
                            <div class="col-lg-4">
                                <div class="erp-select2">
                                    @if(isset($data['current']))
                                        @php $dashboard = in_array(1, $data['current']); @endphp
                                    @endif
                                    @php $haveDash = isset($dashboard)? $dashboard :false; @endphp
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            <input type="checkbox" id="show_dashboard" {{ $haveDash == true ?"checked":""  }} value="1" name="permissions[]" {{ $disabled == true ?"disabled":""  }}>
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($data['custom_modules'] as $custom_modules)
                            @php
                                if($id == 0){
                                    $checked = '';
                                    $disabled = '';
                                }else{
                                    $checked =  ($custom_modules['checked'] == true) ?"checked":"";
                                    $disabled = '';
                                }
                            @endphp
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-9 erp-col-form-label">{{$custom_modules['title']}}:</label>
                                    <div class="col-lg-3">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                            <input type="checkbox" value="{{$custom_modules['id']}}" name="permissions[]" {{ $checked.' '.$disabled}}>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row" id="all_permissions">
                            @foreach($data['modules'] as $menu)
                                @if($menu->menu_visibility == 1)
                                <div class="col-lg-6">
                                    <div class="kt-portlet permission_block">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    {{$menu->menu_name}}
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">
                                            <table class="table table-bordered permission_table">
                                                <thead>
                                                <tr>
                                                    <th>Menu Name</th>
                                                    @if($menu->menu_id == 10)
                                                        <th>
                                                            <i class="fa fa-eye"></i>
                                                        </th>
                                                    @else
                                                        @foreach($data['permission_head'] as $head)
                                                            <th>
                                                                <i class="fa {{$head->icon}}"></i>
                                                                {{--<label class="kt-checkbox kt-checkbox--bold kt-checkbox--primary">
                                                                    <input type="checkbox" id="check" name="check" {{ $disabled == true ?"disabled":""  }}>
                                                                    <span></span>
                                                                </label>--}}
                                                            </th>
                                                        @endforeach
                                                    @endif
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if($menu->menu_id == 10)
                                                    @foreach($menu->children as $menu_dtl)
                                                        @if($menu_dtl->menu_dtl_visibility == 1)
                                                        <tr>
                                                            <td>{{$menu_dtl->menu_dtl_name}}</td>
                                                            <td>
                                                                @php
                                                                    $d_name = ''; $d_id = ''; $have = false;
                                                                @endphp
                                                                @if(count($menu_dtl->permissions) != 0)
                                                                    @foreach($menu_dtl->permissions as $key=>$permission)
                                                                        @if($data['permission_head'][$key]['name'] == 'view')
                                                                            @php
                                                                                $d_name = $data['permission_head'][$key]['name'];
                                                                                $d_id = $permission['id'];
                                                                            @endphp
                                                                            @break;
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                                @if(isset($data['current']))
                                                                    @php $haveId = in_array($d_id, $data['current']); @endphp
                                                                @endif
                                                                @php $have = isset($haveId)? $haveId :false; @endphp
                                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--{{ $have == true ?"success":"primary"  }}">
                                                                    <input type="checkbox" value="{{$d_id}}" name="permissions[]" {{ $have == true ?"checked":""  }} {{ $disabled == true ?"disabled":""  }}>
                                                                    <span></span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach($menu->children as $menu_dtl)
                                                        @if($menu_dtl->menu_dtl_visibility == 1)
                                                        <tr>
                                                            <td>
                                                                {{$menu_dtl->menu_dtl_name}}
                                                            </td>
                                                            @if(count($data['permission_head']) == count($menu_dtl->permissions))
                                                                @foreach($data['permission_head'] as $perm_head)
                                                                    @foreach($menu_dtl->permissions as $perm)
                                                                        @if($perm_head['name'] == $perm['display_name'])
                                                                            @php $per = $perm; @endphp
                                                                        @endif
                                                                    @endforeach
                                                                    @if(isset($data['current']))
                                                                        @php $haveId = in_array($per['id'], $data['current']); @endphp
                                                                    @endif
                                                                    @php $have = isset($haveId)? $haveId :false; @endphp
                                                                    <td>
                                                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--{{ $have == true ?"success":"primary"  }}">
                                                                            <input type="checkbox" value="{{$per['id']}}" name="permissions[]" {{ $have == true ?"checked":""  }} {{ $disabled == true ?"disabled":""  }}>
                                                                            <span></span>
                                                                        </label>
                                                                    </td>
                                                                @endforeach
                                                            @else
                                                                @for($i=0; count($data['permission_head']) > $i; $i++)
                                                                    <td>
                                                                        @foreach($menu_dtl->permissions as $permission)
                                                                            @if($data['permission_head'][$i]['name'] == $permission['display_name'])
                                                                                @php
                                                                                    $d_name = $data['permission_head'][$i]['name'];
                                                                                    $d_id = $permission['id'];
                                                                                @endphp
                                                                                @break;
                                                                            @else
                                                                                @php $d_name = ""; @endphp
                                                                            @endif
                                                                        @endforeach
                                                                        @if(isset($d_name) && !empty($d_name))
                                                                            @if(isset($data['current']))
                                                                                @php $haveId = in_array($d_id, $data['current']); @endphp
                                                                            @endif
                                                                            @php $have = isset($haveId)? $haveId :false; @endphp
                                                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--{{ $have == true ?"success":"primary"  }}">
                                                                                <input type="checkbox" value="{{$d_id}}" name="permissions[]" {{ $have == true ?"checked":""  }} {{ $disabled == true ?"disabled":""  }}>
                                                                                <span></span>
                                                                            </label>
                                                                            @php $d_name = ""; @endphp
                                                                        @endif
                                                                    </td>
                                                                @endfor
                                                            @endif
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
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
    <script src="{{ asset('js/pages/js/user-management-system.js') }}" type="text/javascript"></script>
    <script>
        $('#user_id2').on('change', function() {
            
            var val = $(this).val();
            var val2 = $('#user_id').val();
            if(val2 == 0)
            {
                if(val == 0){
                    window.location.href = '/user-management/form/';
                }else{
                    var url = '/user-management/form/'+val2+'&'+val+'&edit';
                    window.location.href = url;
                }
            }
        });
        $('#user_id').on('change', function() 
        {
            var val = $(this).val();
            var val2 = $('#user_id2').val();
            if(val2 == 0)
            {
                if(val2 == 0){
                    val = val;
                }

                if(val == 0){
                    window.location.href = '/user-management/form/';
                }else{
                    var url = '/user-management/form/'+val+'&'+val2+'&edit';
                    window.location.href = url;
                }
            }
        });
        $('#check_all').on('click', function() {
            if($(this).is(":checked") == true) {
                var checkAll = true
                $('#show_dashboard').prop('checked',true)
            }else{
                var checkAll = false
                $('#show_dashboard').prop('checked',false)
            }
            $('#all_permissions').find('input').each(function(){
                if(checkAll) {
                    $(this).prop('checked',true)
                }else{
                    $(this).prop('checked',false)
                }
            });
        });


    </script>
@endsection
