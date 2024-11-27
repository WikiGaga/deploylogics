@extends('layouts.template')
@section('title', 'WhatsApp Words')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->word_id;
            $name = $data['current']->word_name;
            $group_id = $data['current']->cnt_grp_id;
            $channel_id = $data['current']->channel_id; 
            $status = trim($data['current']->is_active);
            $details = isset($data['current']->dtl) ? $data['current']->dtl : [];
        }
    @endphp
    @permission($data['permission'])
    <form id="contact_group_form" class="master_form kt-form" method="post" action="{{ action('WhatsApp\WAWordController@store', isset($id)?$id:"") }}">
        <input type="hidden" id="form_type" value="wa_word">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                 <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Word: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                 </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Group: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="group" name="group">
                                                <option value="0">Select</option>
                                                @foreach($data['groups'] as $group)
                                                    @php $group_id_var = isset($group_id)?$group_id:"" @endphp
                                                    <option value="{{$group->grp_id}}" {{ $group_id_var == $group->grp_id ? "selected" : "" }}>{{$group->grp_name}}</option>
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
                                    <label class="col-lg-3 erp-col-form-label">Channel: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="channel" name="channel">
                                                <option value="0">Select</option>
                                                @foreach($data['channels'] as $channel)
                                                    @php $channel_id_var = isset($channel_id)?$channel_id:"" @endphp
                                                    <option value="{{$channel->channel_id}}" {{ $channel_id_var == $channel->channel_id ? "selected" : "" }}>{{$channel->channel_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Status:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $entry_status = isset($status)?$status:""; @endphp
                                                    <input type="checkbox" name="is_active" {{$entry_status=="Y"?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="is_active" checked>
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block mt-4">
                            <div class="erp_form___block">
                                <div class="table-scroll form_input__block">
                                    <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <thead class="erp_form__grid_header">
                                        <tr id="erp_form_grid_header_row">
                                            <th scope="col" style="width: 50px;">
                                                <div class="erp_form__grid_th_title">Sr.</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Message Description
                                                </div>
                                                <div class="erp_form__grid_th_input">
                                                    <select class="pd_description tb_moveIndex form-control erp-form-control-sm" id="description">
                                                        <option value="0">Select</option>
                                                        @foreach($data['messages'] as $msg)
                                                            <option value="{{ $msg->msg_id }}">{{ $msg->remarks }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Status</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select class="pd_status tb_moveIndex form-control erp-form-control-sm" id="status">
                                                        <option value="Y">Active</option>
                                                        <option value="N">Inactive</option>
                                                    </select>
                                                </div>
                                            </th>
                                            <th scope="col" style="width: 50px;">
                                                <div class="erp_form__grid_th_title">Action</div>
                                                <div class="erp_form__grid_th_btn">
                                                    <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                        <i class="la la-plus"></i>
                                                    </button>
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="erp_form__grid_body">
                                            @if(isset($details))
                                                @foreach($details as $dtl)
                                                <tr class="new-row">
                                                    <td class="handle">
                                                        <i class="fa fa-arrows-alt-v handle"></i>
                                                        <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}" class="form-control erp-form-control-sm handle" readonly="">
                                                    </td>
                                                    <td>
                                                        <div class="erp-select2">
                                                            <select class="pd_description form-control erp-form-control-sm" aria-invalid="false" data-id="description" name="pd[{{ $loop->iteration }}][description]">
                                                            <option value="0">Select</option>
                                                            @foreach($data['messages'] as $msg)
                                                                @php $dtl_msg_id = isset($dtl['msg_id']) ? $dtl['msg_id'] : 0; @endphp 
                                                                <option value="{{ $msg->msg_id }}" @if($dtl_msg_id == $msg->msg_id) selected @endif>{{ $msg->remarks }}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="erp-select2">
                                                            <select class="pd_status form-control erp-form-control-sm" data-id="status" name="pd[{{ $loop->iteration }}][status]">
                                                                <option value="Y" @if($dtl["is_active"] == "Y") selected @endif>Active</option>
                                                                <option value="N" @if($dtl["is_active"] == "N") selected @endif>Inactive</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-danger gridBtn delData">
                                                                <i class="la la-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'description',
                'fieldClass':'pd_description',
                'type':'select',
                'message':'Select Message Description',
                'require':true,
                'readonly':true
            },
            {
                'id':'status',
                'fieldClass':'pd_status',
                'type':'select',
                'message':'Select Status',
                'require':true,
                'readonly':true
            },
        ];
        var arr_hidden_field = [];
    </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/wa-group.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
@endsection
