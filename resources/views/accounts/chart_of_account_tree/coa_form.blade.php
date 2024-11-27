<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="z-index: 99;top: 20px;right: 20px;position: absolute;">
    <span aria-hidden="true">&times;</span>
</button>
<div class="modal-body" style="padding: 2px;">
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    $data['parent_acc'] = \App\Models\TblAccCoa::select('chart_account_id','chart_code','chart_name')->where('chart_account_id', $data['parent_id'])->where(\App\Library\Utilities::currentBC())->first();
    $parent_account_id = $data['parent_acc']->chart_account_id;
    $parent_chart_code = $data['parent_acc']->chart_code;
    $parent_chart_name = $data['parent_acc']->chart_name;
    if($case == 'new'){
        $chart_level = $data['level'];
        $chart_code = $columns =  collect(\Illuminate\Support\Facades\DB::select('SELECT get_account_code(?,?) AS code from dual', [$chart_level,$parent_chart_code]))->first()->code;

    }
    if($case == 'edit'){
        $id = $data['current']->chart_account_id;
        $chart_level = $data['current']->chart_level;
        $parent_account_code = $data['current']->parent_account_code;
        $parent_account_id = $data['current']->parent_account_id;
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
<form id="coa_form" class="kt-form" method="post" action="{{ action('Accounts\CoaController@store',isset($id)?$id:'') }}" autocomplete="off">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" style="padding: 0 !important;">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Level:</label>
                        <div class="col-lg-6">
                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--bold kt-radio--brand moveIndex" autofocus>
                                    <input type="radio" name="chart_level" value="{{$chart_level}}" checked> Level {{$chart_level}}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Select Account:<span class="required" aria-required="true"> * </span></label>
                        <div class="col-lg-6">
                            <input type="hidden" name="parent_account_id" id="parent_account_id" value="{{ $parent_account_id }}">
                            @if($case == 'edit' && $chart_level == 4)
                                <input type="hidden" id="chart_account_id" value="{{ $id }}">
                                <div class="erp-select2 form-group">
                                    <select name="parent_account_code" id="parent_account_code" class="form-control erp-form-control-sm kt-select2">
                                        @foreach($data['thirdLevelAccounts'] as $thirdLeve)
                                            <option @if($parent_chart_code == $thirdLeve->chart_code) selected @endif value="{{ $thirdLeve->chart_code }}">[{{ $thirdLeve->chart_code }}] {{ $thirdLeve->chart_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="erp-select2 form-group">
                                    <input type="hidden" name="parent_account_code" value="{{isset($parent_chart_code)?$parent_chart_code:''}}">
                                    <input type="text" value="{{"[".$parent_chart_code."] ".$parent_chart_name}}"  class="form-control erp-form-control-sm" style="background: #f1f1f1" readonly>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Account Code:</label>
                        <div class="col-lg-6">
                            <input type="text" name="chart_code" id="chart_code_id" value="{{isset($chart_code)?$chart_code:''}}" class="form-control erp-form-control-sm" style="background: #f1f1f1" readonly>
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
                                <select class="form-control kt-select2 erp-form-control-sm moveIndex tag-select2" multiple  id="chart_branch_id" name="chart_branch_id[]">
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
                                            <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
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
                        <label class="col-lg-3 erp-col-form-label">Can Sale :</label>
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
    </div>
</form>
<!--end::Form-->
<script src="{{ asset('js/pages/js/accounts/coa-tree-create.js') }}" type="text/javascript"></script>

<script>
    $('.tag-select2, #tag-select2_validate,.kt-select2').select2({
        placeholder: "Select Branches",
        tags: true
    });
    $("#tax_type_id").change(function() {
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
    $('select#parent_account_code').on('change' , function(e){
        var thix = $(this);
        $.ajax({
            url: '/coa/coa-max-code/4/'+thix.val(),
            method: 'GET',
            cache: false,
            data : { chart_account_id : $('#chart_account_id').val() },
            beforeSend: function(){
                $('body').addClass('pointerEventsNone');
            },
            success:function(response){
                $('body').removeClass('pointerEventsNone');
                toastr.success('New Code Generated');
                $('#chart_code_id').val(response.new_code);
                $('#parent_account_id').val(response.parent_account_id.parent_account_id);
            },
            error:function(response){
                $('body').removeClass('pointerEventsNone');
                toastr.error('Something went wrong!');
            }
        });
    });
</script>
@endpermission
</div>
