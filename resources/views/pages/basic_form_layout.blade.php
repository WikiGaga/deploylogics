@extends('layouts.layout')
{{--@section('title', 'Page Title')--}}
@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $date =  date('d-m-Y');
            $code =  "DEFAULT-0000001";
        }
        if($case == 'edit'){
            $id = '';
        }
    @endphp
    @permission($data['permission'])
    <form {{--id="_form"--}} class="kt-form" method="post" action="{{--{{ action('Development\ListingStudioController@store',isset($id)?$id:'') }}--}}">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="erp-page--title">
                                {{isset($code)?$code:""}}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row form-group-block">
                                <label class="col-lg-6 col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p kt_date" readonly value="{{isset($date)?$date:""}}" autofocus/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /date row --}}
                        </div>
                        <div class="col-lg-4">
                            <div class="row form-group-block">
                                <label class="col-lg-6 col-form-label">Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" name="name" class="form-control erp-form-control-sm">
                                </div>
                            </div>{{-- /name row --}}
                        </div>
                        <div class="col-lg-4">
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Status:</label>
                                <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            <input type="checkbox" checked="checked" name="entry_status">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>{{-- /status row --}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Select Name:</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" name="n">
                                            <option value="0">Select</option>
                                           {{-- @foreach($data['document_types'] as $document_type)
                                                <option value="{{$document_type->document_id}}" {{$document_type_id == $document_type->document_id?"selected":""}}>{{ucfirst(strtolower($document_type->document_name))}}</option>
                                            @endforeach--}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Select Name:</label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"> <i class="la la-search"></i> </span>
                                        </div>
                                        <input type="text" name="v" class="salary_head_val form-control erp-form-control-sm">
                                        <div class="input-group-append">
                                            <span class="input-group-text"> <i class="la la-eye"></i> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Portal Title
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="kt_repeater_">
                        <div data-repeater-list="repeater_list">
                            <div data-repeater-item class="repeater_list_item">
                                hjkgkjgkjgkj
                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                    <i class="la la-trash-o"></i>
                                </a>
                            </div>
                        </div>
                        <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                            Add
                        </a>
                    </div>
                </div>
            </div>

            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Tabs
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar"> Tool Bar </div>
                </div>
                <div class="kt-portlet__body">
                    <ul class="employee-tab-nav nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_1" role="tab">General Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_2" role="tab">Contact</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" role="tabpanel">
                            Tab Content 1
                        </div>{{-- general info--}}
                        <div class="tab-pane" id="tab_2" role="tabpanel">
                            Tab Content 2
                        </div>{{-- Contact detail--}}
                    </div>
                </div>
            </div>

        </div>
    </form>
    @endpermission
@endsection

@section('pageJS')
@endsection
@section('customJS')
    <script src="{{--{{ asset('js/pages/js/master-form.js') }}--}}" type="text/javascript"></script>
    <script>
        var pageSpinner = '<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span>';
        var xhrGetData = true;
        $(document).on('click','#getData',function(){
            var thix = $(this);
            var val = thix.val();
            var form = thix.parents('form');
            var saleman_id = form.find('.saleman_id option:selected').val();
            var validate = true;
            if(valueEmpty(val)){
                toastr.error("");
                validate = false;
                return true;
            }
            if(validate && xhrGetData){
                var disabledElement = $('body');
                xhrGetData = false;
                var formData = {};
                var url = '';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
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
        })



        //////////////////////////////////////////////////////////
        var KTFormRepeater = function() {
            var kt_repeater_allowance = function() {
                $('#kt_repeater_').repeater({
                    initEmpty: false,
                    isFirstItemUndeletable: true,
                    defaultValues: {
                        // 'text-input': 'foo'
                    },
                    show: function() {
                        $(this).find('.salary_type').select2({
                            placeholder: "Select"
                        });
                        $(this).find('.salary_head').select2({
                            placeholder: "Select"
                        });
                        $(this).slideDown();
                    },
                    ready: function (setIndexes) {
                        $(this).find('.salary_type').select2({
                            placeholder: "Select"
                        });
                        $(this).find('.salary_head').select2({
                            placeholder: "Select"
                        });
                    },

                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                });
            }
            return {
                // public functions
                init: function() {
                    kt_repeater_allowance();
                }
            };
        }();
        jQuery(document).ready(function() {
            KTFormRepeater.init();

        });
    </script>
    <script>

        $(document).on('keyup','#data_bank_reconciliation>thead input',function(){
            var val = $(this).val();
            var index = $(this).parent('th').index();
            var arr = {
                index : index,
                val : val
            }
            funFilterDataRow1(arr);
        })

        $(document).on('click','#header_input_clear_data',function(){
            $('#data_bank_reconciliation>thead input').val("");
        })

        function funFilterDataRow1(arr) {
            var input, filter, table, tr, td, i, txtValue;
            input = arr.val;
            td_index = arr.index;
            filter = input;
            table = document.getElementById("data_bank_reconciliation");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[td_index];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

    </script>
@endsection
