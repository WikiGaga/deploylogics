@extends('layouts.template')
@section('title', 'Bank Distribution Entry Edit')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $date =  date('d-m-Y');
            $user_id = Auth::user()->id;
            $menu_id = $data['menu_id'];
        }
        if($case == 'edit'){
            $id = $data['current']->bd_id;
            $code = $data['current']->bd_code;
            $menu_id = $data['menu_id'];
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bd_date))));
            $shift = $data['current']->bd_shift;
            $user_id = $data['current']->saleman_id;
            $payment_handover_received = $data['current']->bd_payment_handover_received;
            $payment_way_type = $data['current']->bd_payment_way_type;
            $reference_no = $data['current']->bd_reference_no;
            $notes = $data['current']->bd_notes;
            $dtls = isset($data['current']->distribution_dtl)? $data['current']->distribution_dtl :[];
       //   dd($dtls->toarray());

            $dtl_data = [];
            foreach($dtls as $dtl){
                $dtl_data[$dtl['sr_no']][] = $dtl;
            }


        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="bank_distribution_form" class="kt-form" method="post" action="{{ action('Sales\BankDistributionController@store', isset($id)?$id:'') }}">
    <input type="hidden" value='{{$form_type}}' id="form_type">
    <input type="hidden" value='{{isset($id)?$id:""}}' id="form_id">
    <input type="hidden" value='{{isset($menu_id)?$menu_id:""}}' id="menu_id">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{isset($code)?$code:""}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="day_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label text-center">User:</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="saleman_id" name="saleman_id">
                                            @php $select_user = isset($user_id)?$user_id:""; @endphp
                                            @foreach($data['users'] as $users)
                                                <option value="{{$users->id}}" {{$users->id == $select_user?"selected":""}}>{{$users->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label text-center">Shift:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="moveIndex form-control erp-form-control-sm kt-select2" name="day_shift">
                                            <option value="0">Select</option>
                                            @php $shift = isset($shift)?$shift:'' @endphp
                                            <option value="First Shift" {{$shift == 'First Shift'?'selected':''}}>First Shift</option>
                                            <option value="Second Shift" {{$shift == 'Second Shift'?'selected':''}}>Second Shift</option>
                                            <option value="Third Shift" {{$shift == 'Third Shift'?'selected':''}}>Third Shift</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Payment Handover User:</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" name="payment_handover_received">
                                            <option value="0">Select</option>
                                            @php $payment_handover_received = isset($payment_handover_received)?$payment_handover_received:""; @endphp
                                            @foreach($data['payment_person'] as $payment_person)
                                                <option value="{{$payment_person->id}}" {{$payment_person->id == $payment_handover_received?"selected":""}}>{{$payment_person->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Payment Way Type:</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" name="payment_way_type">
                                            <option value="0">Select</option>
                                            @php $payment_way_type = isset($payment_way_type)?$payment_way_type:""; @endphp
                                            @foreach($data['payment_type'] as $payment_type)
                                                <option value="{{$payment_type->payment_type_id}}" {{$payment_type->payment_type_id == $payment_way_type?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label text-center">Reference No:</label>
                                <div class="col-lg-6">
                                    <input type="text" name="reference_no" value="{{isset($reference_no)?$reference_no:''}}" class="moveIndex form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="3" name="notes" maxlength="255" class="form-control erp-form-control-sm moveIndex">{{isset($notes)?$notes:''}}</textarea>
                        </div>
                    </div>

                    <div class="tab-pane" id="distribution" role="tabpanel" style="margin-top:20px;">
                        <div id="kt_repeater_distribution">
                            <div class="form-group row">
                                <div data-repeater-list="distribution_dtl" class="col-lg-12">
                                    @foreach($dtl_data  as $key=>$dtls)
                                        <div data-repeater-item class="kt-margin-b-10 barcode">
                                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                            <div class="row form-group-block">
                                                <label class="col-lg-2 erp-col-form-label">Select Bank:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" name="bank_id">
                                                            <option value="0">Select</option>
                                                                @foreach($data['acc_code'] as $parent)
                                                                @php $account_id = isset($dtls[0]->bank_id)?$dtls[0]->bank_id:'' @endphp
                                                                    <option value="{{$parent->chart_account_id}}" {{ $parent->chart_account_id ==$account_id?'selected':'' }}>{{$parent->chart_code}}-{{$parent->chart_name}}</option>
                                                                @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                                                        <i class="la la-remove"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="denomination_table" id="denomination_table">
                                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                                                            <thead>
                                                                <tr>
                                                                    <th width="40%">Denomination</th>
                                                                    <th width="30%">Qty</th>
                                                                    <th width="30%">Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="repeated_data">
                                                                @php $sr = 0; $totAmt = 0;@endphp
                                                                    @foreach($data['denomination'] as $key=>$denomination)
                                                                        @foreach($dtls as $dtl)
                                                                            @if($dtl->denomination_id ==$denomination->denomination_id )
                                                                                <tr>
                                                                                    <td>
                                                                                        <input type="hidden" name="denomination_id_{{$sr}}" value="{{$denomination->denomination_id}}"><b class="denomination">{{$denomination->denomination_name}}</b>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" class="form-control erp-form-control-sm qty validNumber" maxlength="15" name="day_qty_{{$sr}}" value="{{$dtl->bd_dtl_qty}}">
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" class="form-control erp-form-control-sm amt validNumber" maxlength="15" name="day_value_{{$sr}}" value="{{number_format($dtl->bd_dtl_amount,3)}}" readonly>
                                                                                    </td>
                                                                                </tr>
                                                                                @php $totAmt = $totAmt + $dtl->bd_dtl_amount; @endphp
                                                                            @endif
                                                                        @endforeach
                                                                    @php $sr++; @endphp
                                                                @endforeach
                                                            <tr>
                                                                <td colspan="2"><b class="denomination">Total</b></td>
                                                                <td id="total_Amt" class="text-right font-weight-bold total_Amt">{{number_format($totAmt,3)}}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <div data-repeater-create="" class="btn btn btn-primary">
                                        <span id="new">
                                            <i class="la la-plus"></i>
                                            <span>Add</span>
                                        </span>
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
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/bank-distribution.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>

        $(document).on('keyup', '.qty', function (e) {
            var qty = $(this).val();
            var denomination = $(this).parents('tr').find('.denomination').html();
            qty = (qty == '' || qty == undefined)? 0 : qty;
            denomination = (denomination == '' || denomination == undefined)? 0 : denomination;
            var amount = qty * denomination;
            amount= amount.toFixed(3);
            $(this).parents('tr').find('.amt').val(amount);
            totalAmount($(this));
        });
        function totalAmount(tr){
            var t = 0;
            var v = 0;

            tr.parents('table').find('.repeated_data>tr').each(function( index ) {
                v = $(this).find('td>.amt').val();
                v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
                t += parseFloat(v);
            });

            t = t.toFixed(3);
            tr.parents('table').find('.total_Amt').html(t);
        }
        $(document).on('click','#upload_documents',function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                form_id : $('#form_id').val(),
                form_type : $('#form_type').val(),
                menu_id : $('#menu_id').val(),
                form_code : $('.erp-page--title').text().trim(),
            }
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
        })
    </script>
@endsection
