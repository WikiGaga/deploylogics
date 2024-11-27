@extends('layouts.layout')
@section('title', 'Product Merged')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->country_id;
            $name = $data['current']->country_name;
            $status = $data['current']->country_entry_status;
        }
    @endphp

    @permission($data['permission'])
    <form id="product_merged_form" class="product_merged_form kt-form" data-url="{{ action('Purchase\ProductMergedController@store')}}">
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
                            <label class="col-lg-4 erp-col-form-label">Product: <span class="required">*</span></label>
                            <div class="col-lg-4">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" id="f_barcode" name="f_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productMergedFromHelp')}}" class="open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter Here">
                                        <input type="hidden" id="f_product_id" name="f_product_id" class="form-control erp-form-control-sm">
                                        <input type="hidden" id="f_product_barcode_id" name="f_product_barcode_id" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <input id="f_product_name" name="f_product_name" type="text" class="form-control erp-form-control-sm readonly" readonly>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-4 erp-col-form-label">Merged To: <span class="required">*</span></label>
                            <div class="col-lg-4">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data2">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" id="m_barcode" name="m_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productMergedToHelp')}}" class="open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter Here">
                                        <input type="hidden" id="m_product_barcode_id" name="m_product_barcode_id" class="form-control erp-form-control-sm">
                                        <input type="hidden" id="m_product_id" name="m_product_id" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <input id="m_product_name" name="m_product_name" type="text" class="form-control erp-form-control-sm readonly" readonly>
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

        var xhrGetData = true;
        $(document).on('click','#btn-update-entry',function(e){
            e.preventDefault();
            var thix = $(this);
            var val = thix.val();
            var form = thix.parents('form');
            var f_product_id = form.find('#f_product_id').val();
            var f_product_barcode_id = form.find('#f_product_barcode_id').val();
            var m_product_id = form.find('#m_product_id').val();
            var validate = true;
            if(valueEmpty(f_product_id) || valueEmpty(f_product_barcode_id) || valueEmpty(m_product_id)){
                toastr.error("Select any product");
                validate = false;
                return true;
            }
            if(f_product_id == m_product_id){
                toastr.error("Both Product must be separate");
                validate = false;
                return true;
            }

            if(validate && xhrGetData){
                var disabledElement = $('form');
                var formData = {
                    f_product_id : f_product_id,
                    f_product_barcode_id : f_product_barcode_id,
                    m_product_id : m_product_id,
                };
                var url = $('#product_merged_form').attr('data-url');

                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, merged it!'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: url,
                            dataType	: 'json',
                            data        : formData,
                            beforeSend: function( xhr ) {
                                xhrGetData = false;
                                disabledElement.addClass('pointerEventsNone');
                            },
                            success: function(response,data) {
                                console.log(response);
                                if(response.status == 'success'){
                                    toastr.success(response.message);
                                    location.reload();
                                }else{
                                    toastr.error(response.message);
                                }
                                xhrGetData = true;
                                disabledElement.removeClass('pointerEventsNone');
                            },
                            error: function(response,status) {
                                toastr.error(response.responseJSON.message);
                                xhrGetData = true;
                                disabledElement.removeClass('pointerEventsNone');
                            }
                        });
                    }
                });
            }
        })

    </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

    <script>
        $(document).on('click','#btn-minus-selected-data',function(){
            $('#f_product_name').val("");
        })
        $(document).on('click','#btn-minus-selected-data2',function(){
            $('#m_product_name').val("");
        })
    </script>
@endsection

