@extends('layouts.template')
@section('title', 'POS Setting')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->pos_setting_id;
            $select_user = $data['current']->user_id;
            $branch_id = $data['current']->branch_id;
            $chart_id = $data['current']->chart_id;
            $hold_apply = $data['current']->hold_apply;
            $delete_apply = $data['current']->delete_apply;
            $cancel_bill = $data['current']->cancel_bill;
            $photo_apply = $data['current']->photo_apply;
            $return_apply = $data['current']->return_apply;
            $return_blank = $data['current']->return_apply_blank;
            $save_apply = $data['current']->save_apply;
            $less_qty_apply = $data['current']->less_qty_apply;
            $customer_create_apply = $data['current']->customer_create_apply;
            $holdprint_apply = $data['current']->holdprint_apply;
            $inv_discount_apply = $data['current']->inv_discount_apply;
            $forward_apply = $data['current']->forward_apply;
            $redeem_loyalty_points = $data['current']->redeem_loyalty_points;
            $last_print_apply = $data['current']->last_print_apply;
            $list_print_apply = $data['current']->list_print_apply;

        }
    @endphp
    @permission($data['permission'])
    <form id="pos_setting_form" class="master_form kt-form" method="post" action="{{ action('Setting\POSSettingController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">User: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="user_id" name="user_id">
                                        <option value="0">Select</option>
                                        @php $select_user = isset($select_user)?$select_user:""; @endphp
                                        @foreach($data['users'] as $users)
                                            <option value="{{$users->id}}" {{$users->id == $select_user?"selected":""}}>{{$users->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Branch: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="branch_id" name="branch_id">
                                        <option value="0">Select</option>
                                        @php $branch_id = isset($branch_id)?$branch_id:""; @endphp
                                        @foreach($data['branch'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == $branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
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
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Hold Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $hold_apply = isset($hold_apply)?$hold_apply:""; @endphp
                                                <input type="checkbox" name="hold_apply" {{$hold_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="hold_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Delete Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $delete_apply = isset($delete_apply)?$delete_apply:""; @endphp
                                                <input type="checkbox" name="delete_apply" {{$delete_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="delete_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Cancel Bill:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $cancel_bill = isset($cancel_bill)?$cancel_bill:""; @endphp
                                                    <input type="checkbox" name="cancel_bill" {{$cancel_bill == 'YES'?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="cancel_bill">
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Photo Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $photo_apply = isset($photo_apply)?$photo_apply:""; @endphp
                                                <input type="checkbox" name="photo_apply" {{$photo_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="photo_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Return Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $return_apply = isset($return_apply)?$return_apply:""; @endphp
                                                <input type="checkbox" name="return_apply" {{$return_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="return_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Return Apply Blank:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $return_blank = isset($return_blank)?$return_blank:""; @endphp
                                                <input type="checkbox" name="return_blanck" {{$return_blank == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="return_blanck">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Save Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $save_apply = isset($save_apply)?$save_apply:""; @endphp
                                                <input type="checkbox" name="save_apply" {{$save_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="save_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Less Qty Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $less_qty_apply = isset($less_qty_apply)?$less_qty_apply:""; @endphp
                                                <input type="checkbox" name="less_qty_apply" {{$less_qty_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="less_qty_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Customer Create Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $customer_create_apply = isset($customer_create_apply)?$customer_create_apply:""; @endphp
                                                <input type="checkbox" name="customer_create_apply" {{$customer_create_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="customer_create_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Hold Print Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $holdprint_apply = isset($holdprint_apply)?$holdprint_apply:""; @endphp
                                                <input type="checkbox" name="holdprint_apply" {{$holdprint_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="holdprint_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Inv. Discount Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $inv_discount_apply = isset($inv_discount_apply)?$inv_discount_apply:""; @endphp
                                                <input type="checkbox" name="inv_discount_apply" {{$inv_discount_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="inv_discount_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Forward Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $forward_apply = isset($forward_apply)?$forward_apply:""; @endphp
                                                <input type="checkbox" name="forward_apply" {{$forward_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="forward_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Redeem Loyalty Points:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $redeem_loyalty_points = isset($redeem_loyalty_points)?$redeem_loyalty_points:""; @endphp
                                                <input type="checkbox" name="redeem_loyalty_points" {{$redeem_loyalty_points == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="redeem_loyalty_points">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Last Print Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $last_print_apply = isset($last_print_apply)?$last_print_apply:""; @endphp
                                                <input type="checkbox" name="last_print_apply" {{$last_print_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="last_print_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">List Print Apply:</label>
                                    <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $list_print_apply = isset($list_print_apply)?$list_print_apply:""; @endphp
                                                <input type="checkbox" name="list_print_apply" {{$list_print_apply == 'YES'?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="list_print_apply">
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                    </div>
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

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/pos-setting.js') }}" type="text/javascript"></script>
@endsection
