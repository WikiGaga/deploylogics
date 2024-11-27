@extends('layouts.template')
@section('title', 'Create Cheque Book')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->cheque_book_id;
            $account_id = $data['current']->chart_account_id;
            $book_name = $data['current']->cheque_book_name;
            $serial_from = $data['current']->cheque_book_serial_from;
            $cheque_book_no = $data['current']->cheque_book_no_of_cheque;
            $serial_to = $data['current']->cheque_book_serial_to;
            $status = $data['current']->cheque_book_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="cb_form" class="kt-form" method="post" action="{{ action('Accounts\ChequeBookController@store',isset($id)?$id:'') }}">
    @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Select Bank:</label>
                        <div class="col-lg-5">
                            <div class="erp-select2">
                                <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="bank_id" name="bank_id" autofocus>
                                    <option value="0">Select</option>
                                    @foreach($data['acc_code'] as $parent)
                                        @php $account_id = isset($account_id)?$account_id:'' @endphp
                                        <option value="{{$parent->chart_account_id}}" {{ $parent->chart_account_id ==$account_id?'selected':'' }}>{{$parent->chart_code}}-{{$parent->chart_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Check Book Name:<span class="required">* </span></label>
                                <div class="col-lg-6">
                                    <input type="text" name="cheque_book_name" maxlength="100" id="cheque_book_name" value="{{ isset($book_name)?$book_name:'' }}" class="form-control erp-form-control-sm moveIndex">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">No of Cheques:<span class="required">* </span></label>
                                <div class="col-lg-6">
                                    <input type="text" name="cheque_book_no_of_cheque" maxlength="20"  id="cheque_book_no_of_cheque" value="{{ isset($cheque_book_no)?$cheque_book_no:'' }}" class="form-control erp-form-control-sm validNumber moveIndex">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Cheque Book Serial From:</label>
                                <div class="col-lg-6">
                                    <input type="text" name="cheque_book_serial_from" maxlength="20" id="cheque_book_serial_from" value="{{ isset($serial_from)?$serial_from:'' }}" class="form-control erp-form-control-sm validNumber moveIndex">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Cheque Book Serial to:</label>
                                <div class="col-lg-6">
                                    <input type="text" name="cheque_book_serial_to" maxlength="20"  id="cheque_book_serial_to" value="{{ isset($serial_to)?$serial_to:'' }}"  class="form-control erp-form-control-sm validNumber moveIndex">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Status:</label>
                        <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon moveIndex">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="cheque_book_entry_status" {{$entry_status==1?"checked":""}} >
                                        @else
                                            <input type="checkbox" checked="checked" name="cheque_book_entry_status">
                                        @endif
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
    <script src="{{ asset('js/pages/js/check-book-create.js') }}" type="text/javascript"></script>

<script>

        $("#cheque_book_serial_from").keyup(function()
        {
            var DataValue1 = $("#cheque_book_serial_from").val();
            $("#cheque_book_no_of_cheque").val(0);
            $("#cheque_book_serial_to").val(DataValue1);

        });

        $("#cheque_book_no_of_cheque").keyup(function()
        {
            var DataValue1 = $("#cheque_book_serial_from").val();
            var DataValue3 = $("#cheque_book_no_of_cheque").val();
            var result=parseInt(DataValue1) + parseInt(DataValue3);
            if(result)
                {
                    $('#cheque_book_serial_to').val(result);

                }
        });

        $("#cheque_book_serial_to").keyup(function()
        {
            var DataValue1 = $("#cheque_book_serial_from").val();
            var DataValue3 = $("#cheque_book_serial_to").val();
            var result=parseInt(DataValue3) - parseInt(DataValue1);
            if(result)
                {

                    $('#cheque_book_no_of_cheque').val(result);

                }
        });




</script>
@endsection


