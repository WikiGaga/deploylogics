@extends('layouts.template')
@section('title', 'Chart of Account')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            if (Session::has('lastData')){
                $chart_level = isset(Session::get('lastData')['chart_level'])?Session::get('lastData')['chart_level']:'';
                $parent_code = isset(Session::get('lastData')['parent_account_code'])?Session::get('lastData')['parent_account_code']:'';
                $chart_code = isset(Session::get('lastData')['maxcode'])?Session::get('lastData')['maxcode']:'';
                $data['level'] = \App\Models\TblAccCoa::select('chart_code','chart_name')->where('chart_level', '=', $chart_level-1)->where(\App\Library\Utilities::currentBC())->get();
            }
        }
        if($case == 'edit'){
            $id = $data['current']->chart_account_id;
            $chart_level = $data['current']->chart_level;
            $parent_account_code = $data['current']->parent_account_code;
            $chart_code = $data['current']->chart_code;
            $chart_name = $data['current']->chart_name;
            $reference_code = $data['current']->chart_reference_code;
            $pos_default = $data['current']->pos_default;
            $can_sale = $data['current']->chart_can_sale;
            $can_purchase = $data['current']->chart_can_purchase;
            $debit_limit = $data['current']->chart_debit_limit;
            $credit_limit = $data['current']->chart_credit_limit;
            $warn = $data['current']->chart_warn;
            $block = $data['current']->chart_block_transaction;
            $sale_expense_account = $data['current']->chart_sale_expense_account;
            $purchase_expense_account = $data['current']->chart_purch_expense_account;
            $chart_branches = isset($data['current']->chart_branches) ? $data['current']->chart_branches : [] ;
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="coa_form" class="master_form kt-form" method="post" action="{{ action('Accounts\CoaController@store',isset($id)?$id:'') }}">
     @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Level:</label>
                        <div class="col-lg-6">
                            <div class="kt-radio-inline">
                                @php $chart_level = isset($chart_level)?$chart_level:'' @endphp
                                <label class="kt-radio kt-radio--bold kt-radio--brand moveIndex" autofocus>
                                    @if($case == 'edit')
                                        <input type="radio" name="chart_level" value="2" disabled {{$chart_level==2?'checked':''}}> Level 2
                                    @else
                                        <input type="radio" name="chart_level" value="2" {{$chart_level==2?'checked':''}}> Level 2
                                    @endif
                                    <span></span>
                                </label>
                                <label class="kt-radio kt-radio--bold kt-radio--brand moveIndex">
                                    @if($case == 'edit')
                                        <input type="radio" name="chart_level" value="3" disabled {{$chart_level==3?'checked':''}}> Level 3
                                    @else
                                        <input type="radio" name="chart_level" value="3" {{$chart_level==3?'checked':''}}> Level 3
                                    @endif
                                    <span></span>
                                </label>
                                <label class="kt-radio kt-radio--bold kt-radio--brand moveIndex">
                                    @if($case == 'edit')
                                        <input type="radio" name="chart_level" value="4" disabled {{$chart_level==4?'checked':''}}> Level 4
                                    @else
                                        <input type="radio" name="chart_level" value="4" {{$chart_level==4?'checked':''}}> Level 4
                                    @endif
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3 text-right">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Create Cust/Sup
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item" href="/supplier/form" target="_blank">Create Supplier</a>
                                        <a class="dropdown-item" href="/customer/form" target="_blank">Create Customer</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Select Account:<span class="required" aria-required="true"> * </span></label>
                        <div class="col-lg-6">
                            <div class="erp-select2 form-group">

                                @if($case == 'edit')
                                    <select class="form-control kt-select2 moveIndex erp-form-control-sm" name="parent_account_code" id="parent_account_code" disabled>
                                        @php $parent_account_code = isset($parent_account_code)?$parent_account_code:""; @endphp
                                        @foreach($data['level'] as $level)
                                            <option value="{{$level->chart_code}}" {{$parent_account_code == $level->chart_code ? "selected":""}}>{{$level->chart_code.'-'.$level->chart_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    @if(isset($parent_code ))
                                        <select class="form-control kt-select2 moveIndex erp-form-control-sm" name="parent_account_code" id="parent_account_code">
                                            @php $parent_code = isset($parent_code)?$parent_code:""; @endphp
                                            @foreach($data['level'] as $level)
                                                <option value="{{$level->chart_code}}" {{$parent_code == $level->chart_code ? "selected":""}}>{{$level->chart_code.'-'.$level->chart_name}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select class="form-control kt-select2 moveIndex erp-form-control-sm" name="parent_account_code" id="parent_account_code">
                                            <option value="0">Select</option>
                                        </select>
                                    @endif
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Account Code:</label>
                        <div class="col-lg-6">
                            <input type="text" name="chart_code" id="chart_code_id" value="{{isset($chart_code)?$chart_code:''}}" class="form-control erp-form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Name:<span class="required" aria-required="true"> * </span></label>
                        <div class="col-lg-6">
                            <input type="text" name="name" value="{{isset($chart_name)?$chart_name:''}}" class="form-control moveIndex erp-form-control-sm medium_text">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Branch:<span class="required" aria-required="true"> * </span></label>
                        <div class="col-lg-6">
                            <div class="erp-select2 form-group">
                                <select class="form-control kt-select2 erp-form-control-sm moveIndex"  id="chart_branch_id" name="chart_branch_id[]">
                                    @if(isset($chart_branches))
                                        @php $col = []; @endphp
                                        @foreach($chart_branches as $branch)
                                            @php array_push($col,$branch->branch_id); @endphp
                                        @endforeach
                                        @foreach($data['branch'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{ (in_array($branch->branch_id, $col)) ? 'selected' : '' }}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    @else
                                        @foreach($data['branch'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Reference Code:</label>
                        <div class="col-lg-6">
                            <input type="text" name="reference_code" value="{{isset($reference_code)?$reference_code:''}}" class="form-control moveIndex erp-form-control-sm small_text">
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">POS Default:</label>
                        <div class="col-lg-6">
                            <span >
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                    @php $pos_default = isset($pos_default)?$pos_default:'' @endphp
                                    <input type="checkbox" name="pos_default" {{$pos_default==1?"checked":""}}>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Can Sale:</label>
                        <div class="col-lg-6">
                                <span >
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                        @php $can_sale = isset($can_sale)?$can_sale:'' @endphp
                                        <input type="checkbox" name="chart_can_sale" value=1 {{$can_sale==1?"checked":""}}>
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Can Purchase:</label>
                        <div class="col-lg-6">
                                <span >
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                        @php $can_purchase = isset($can_purchase)?$can_purchase:'' @endphp
                                        <input type="checkbox" name="chart_can_purchase" value=1 {{$can_purchase==1?"checked":""}}>
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Debit Limit:</label>
                        <div class="col-lg-6">
                            <input type="text" name="chart_debit_limit" maxlength="20" value="{{isset($debit_limit)?$debit_limit:''}}" class="form-control moveIndex erp-form-control-sm validNumber">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Credit Limit:</label>
                        <div class="col-lg-6">
                            <input type="text" name="chart_credit_limit" maxlength="20" value="{{isset($credit_limit)?$credit_limit:''}}" class="form-control moveIndex erp-form-control-sm validNumber">
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Warn Only:</label>
                        <div class="col-lg-6">
                                <span >
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                        @php $warn = isset($warn)?$warn:'' @endphp
                                        <input type="checkbox" name="chart_warn" value=1 {{ $warn==1?"checked":"" }}>
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Block Transaction:</label>
                        <div class="col-lg-6">
                                <span >
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                        @php $block = isset($block)?$block:'' @endphp
                                        <input type="checkbox" name="chart_block_transaction" value=1 {{ $block==1?"checked":"" }}>
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Sale Expense Account:</label>
                        <div class="col-lg-6">
                                <span >
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                        @php $expense_account = isset($sale_expense_account)?$sale_expense_account:'' @endphp
                                        <input type="checkbox" name="chart_sale_expense_account" value=1 {{ $expense_account==1?"checked":"" }}>
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Purchase Expense Account:</label>
                        <div class="col-lg-6">
                                <span >
                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                        @php $expense_account = isset($purchase_expense_account)?$purchase_expense_account:'' @endphp
                                        <input type="checkbox" name="chart_purchase_expense_account" value=1 {{ $expense_account==1?"checked":"" }}>
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
    <script src="{{ asset('js/pages/js/accounts/coa.js') }}" type="text/javascript"></script>

<script>
    $("input[type='radio']").click(function()
        {
            var radioValue = $("input[name='chart_level']:checked").val();
            if(radioValue) {
                $.ajax({
            type:'GET',
            url:'/coa/coa-data/'+ radioValue,
            success: function(response,  data){
                if(data)
                {
                     $('#parent_account_code').empty();
                     $('#chart_code_id').val("");
                     $('#parent_account_code').append('<option>Select</option>');
                    $.each(response,function(key,value){

                         $('#parent_account_code').append('<option value="'+value.chart_code+'">'+value.chart_code+'-'+value.chart_name+'</option>');

                    });

                }
            }
        });
            }

        });
        $("#parent_account_code").change(function()
        {
            var radioValue = $("input[name='chart_level']:checked").val();

            if(radioValue)
                {

                 $.ajax({
                            type:'GET',
                            url:'/coa/coa-max/'+ radioValue+'/'+ $("#parent_account_code").val(),
                            success: function(response,  data)
                            {
                                if(data)
                                {
                                    $('#chart_code_id').empty();
                                    $('#chart_code_id').val(response);
                                }
                            }
                        });
                }
        });

        $("#tax_type_id").change(function()
        {
            var DataValue = $("#tax_type_id").val();

            if(DataValue)
                {

                 $.ajax({
                            type:'GET',
                            url:'/coa/coa-taxtype/'+ DataValue,
                            success: function(response,  data)
                            {
                                console.log(response.tax_type_percent+"-"+data);
                                if(data)
                                {
                                    $('#tax_percent').empty();
                                    $('#tax_percent').val(response.tax_type_percent);
                                }
                            }
                        });
                }
        });




</script>
@endsection


