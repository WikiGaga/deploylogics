@extends('layouts.template')
@section('title', 'POS Terminal')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->terminal_id;
            $name = $data['current']->terminal_name;
            $mac_address = $data['current']->terminal_mac_address;
            $branch_id = $data['current']->branch_id;
            $chart_id = $data['current']->chart_id;
            $fbr_pos_id = $data['current']->fbr_pos_id;
            $pos_register_no = $data['current']->pos_register_no;
            $pos_ntn_no = $data['current']->pos_ntn_no;
            $pos_stn_no = $data['current']->pos_stn_no;
        }
    @endphp
    @permission($data['permission'])
    <form id="pos_terminal_form" class="master_form kt-form" method="post" action="{{ action('Setting\POSTerminalController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Terminal Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm short_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Terminal Mac Address: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="mac_address" value="{{isset($mac_address)?$mac_address:""}}" class="form-control erp-form-control-sm short_text text-left">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Select Branch:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 form-control-sm" id="kt_select2_1" name="branch_id">
                                                <option value="">Select</option>
                                                @php $branch_id = isset($branch_id)?$branch_id:''@endphp
                                                @foreach($data['branches'] as $branch)
                                                    <option value="{{$branch->branch_id}}" {{$branch->branch_id == $branch_id ? 'selected' : ''}}>{{$branch->branch_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Select Account:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 form-control-sm" name="chart_id">
                                                <option value="">Select</option>
                                                @php $chart_id = isset($chart_id)?$chart_id:''@endphp
                                                @foreach($data['acc_code'] as $acc_code)
                                                    <option value="{{$acc_code->chart_account_id}}" {{$acc_code->chart_account_id==$chart_id?'selected':''}}>{{$acc_code->chart_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">FBR POS NO: </label>
                            <div class="col-lg-6">
                                <input type="text" name="fbr_pos_id" value="{{isset($fbr_pos_id)?$fbr_pos_id:""}}" class="form-control erp-form-control-sm short_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">POS Registration No: </label>
                            <div class="col-lg-6">
                                <input type="text" name="pos_register_no" value="{{isset($pos_register_no)?$pos_register_no:""}}" class="form-control erp-form-control-sm short_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">NTN #: </label>
                            <div class="col-lg-6">
                                <input type="text" name="pos_ntn_no" value="{{isset($pos_ntn_no)?$pos_ntn_no:""}}" class="form-control erp-form-control-sm short_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">STN #: </label>
                            <div class="col-lg-6">
                                <input type="text" name="pos_stn_no" value="{{isset($pos_stn_no)?$pos_stn_no:""}}" class="form-control erp-form-control-sm short_text">
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

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/pos-terminal.js') }}" type="text/javascript"></script>
@endsection
