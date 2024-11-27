@extends('layouts.layout')
@section('title', 'Multi Branch Stock Transfer')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code  = $data['document_code'];
            $date =  date('d-m-Y');
            $id = "";
        }
        if($case == 'edit'){
            $current = $data['current'];
            $id = $current->mb_stock_transfer_id;
            $code  = $current->mb_stock_transfer_code;
            $date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$current->mb_stock_transfer_entry_date))));
            $purchasing_id = $current->purchasing_id;
            $purchasing_code = $current->purchasing->purchasing_code;
            $remarks = $current->mb_stock_transfer_remarks;
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="mbst_form" class="kt-form" method="post" action="{{ action('Inventory\MBStockTransferController@store', isset($id)?$id:"") }}">
        @csrf
        <input type="hidden" value='{{$form_type}}' id="form_type">
        <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$code}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="document_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$date}}" id="kt_datepicker_3"  />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Purchasing Code:</label>
                                <div class="col-lg-8">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                            </div>
                                            <input type="text" value="{{isset($purchasing_code)?$purchasing_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','stockPurchasingHelp')}}" id="purchasing_code" name="purchasing_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                            <input type="hidden" id="purchasing_id" name="purchasing_id" value="{{isset($purchasing_id)?$purchasing_id:''}}"/>
                                            <div class="input-group-append">
                                                <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                    <i class="la la-search"></i>
                                                </span>
                                                @if($case == 'new')
                                                <span class="input-group-text group-input-btn" id="getStockPurchasingData">
                                                    GO
                                                </span>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">

                        </div>
                    </div>
                    <div class="form-group-block row kt-margin-t-15">
                        <div class="col-lg-12">
                            <div id="purchasing_products_details" class="purchasing_products_details"></div>
                        </div>
                    </div>
                    <div class="form-group-block row kt-margin-t-20">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="3" id="remarks" name="remarks" maxlength="255" class="form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
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
    <script src="{{ asset('js/pages/js/inventory/mb_stock_transfer.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
@endsection

@section('customJS')
    <script>
        @if($case == 'new')
        $('#getStockPurchasingData').click(function(){
            var thix = $(this);
            var val = $('form .erp_form___block').find('input#purchasing_id').val();
            var url = "{{action('Inventory\MBStockTransferController@getStockPurchasing')}}";
            var formData = {
                purchasing_id : val,
            };
            getPurchsingData(val,url,formData);
        });
        @endif
        @if($case == 'edit')
            var val = {{$id}};
            var url = "{{action('Inventory\MBStockTransferController@getStockPurchasingDtl')}}";
            var formData = {
                id : {{$id}},
            };
            getPurchsingData(val,url,formData);
        @endif
        function getPurchsingData(val,url,formData){
            if(val){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#purchasing_products_details').load(url,formData,function(responseTxt, statusTxt, xhr){
                    if(statusTxt == "success")
                        toastr.success('Data loaded successfully');
                    if(statusTxt == "error")
                        toastr.error("Error: " + xhr.status + ": " + xhr.statusText);
                });
            }else{
                toastr.error("Select first Purchasing Code");
            }
        }
        $(document).on('keyup','input.branch_qty',function(){
            var thix = $(this);
            var tr = thix.parents('tr');
            var sendQty = 0;
            tr.find('td').each(function(){
                if($(this).find('input.branch_qty').val()){
                    sendQty += parseFloat($(this).find('input.branch_qty').val())
                }
            });
            var totalQty = tr.find('.totalQty').text()
            var diffQty = parseFloat(sendQty) - parseFloat(totalQty);
            tr.find('.diffQty').text(parseFloat(diffQty).toFixed(3));
            if(totalQty < sendQty){
                tr.find('.diffQty').css({color: '#f00'})
            }else{
                tr.find('.diffQty').css({color: ''})
            }
        })
    </script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
