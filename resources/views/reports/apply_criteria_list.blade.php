<div class="modal-header" style="background: #33b5e5;">
    <h5 class="modal-title" style="color: #fff;">{{$data['title']}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;"></button>
</div>
<div class="modal-body">ِ
    <div class="kt-container" style="width:100%;">
        @if(isset($data['code_val']) && $data['code_val'] != "")
            <div class="row form-group-block">
                <div class="col-lg-3">
                    <span class="erp-col-form-label">Code :</span>
                </div>
                <div class="col-lg-6">
                    <span class="erp-col-form-label" style="color: #5d78ff;">{{$data['code_val']}}</span>
                </div>
            </div>
        @endif
        <div class="row form-group-block">
            <div class="col-lg-3">
                <input type="hidden" name="product_id" value="{{isset($data['product_id'])?$data['product_id']:''}}">
                <input type="hidden" name="account_id" value="{{isset($data['account_id'])?$data['account_id']:''}}">
                <span class="erp-col-form-label">Name :</span>
            </div>
            <div class="col-lg-6">
                <span class="erp-col-form-label" style="color: #5d78ff;">{{$data['name_val']}}</span>
            </div>
        </div>
        @php
            $all_document_types = ['POS','RPOS','SI','SR','GRN','PR','SP','IST','DI','STR','OS','SA','EI','ST',];
            sort($all_document_types);
        @endphp
        <div class="row form-group-block">
            <div class="col-lg-3">
                <label class="erp-col-form-label">All Document Type:</label>
            </div>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control erp-form-control-sm kt_select2_options all_document_type" multiple name="all_document_type[]">
                        @foreach($all_document_types as $document_types)
                            <option value="{{$document_types}}" >{{strtoupper($document_types)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $stores = App\Models\TblDefiStore::where(App\Library\Utilities::currentBCB())->get();
        @endphp
        <div class="row form-group-block">
            <div class="col-lg-3">
                <label class="erp-col-form-label">Store:</label>
            </div>
            @php
                $defaultStore = App\Library\Utilities::getDefaultStoreOfBranch();
                $defaultStore = isset($defaultStore->store_id) ? $defaultStore->store_id : 0;
            @endphp
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control erp-form-control-sm kt_select2_options store" multiple name="store[]">
                        @foreach($stores as $store)
                            <option value="{{$store->store_id}}" >{{$store->store_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row form-group-block">
            <div class="col-lg-3">
                <label class="erp-col-form-label">Select Date Range:</label>
            </div>
            <div class="col-lg-6">
                <div class="erp-selectDateRange">
                    <div class="input-daterange input-group um_datepicker_5">
                        @php
                            $date = \Carbon\Carbon::today()->subDays(30);
                            $newDate = date("d-m-Y", strtotime($date));
                        @endphp
                        <input type="text" class="form-control erp-form-control-sm" value="{{ $newDate }}" name="date_from" autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text erp-form-control-sm">To</span>
                        </div>
                        <input type="text" class="form-control erp-form-control-sm" value="{{date('d-m-Y')}}" name="date_to" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
            <div class="row form-group-block">
                <div class="col-lg-3">
                    <label class="erp-col-form-label">Month Wise:</label>
                </div>
                <div class="col-lg-6">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                            <input type="checkbox" name="month_wise" id="month_wise">
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" id="{{$data['btn_id']}}" class="btn btn-primary" style="background: #33b5e5;">Generate</button>
</div>
<script>
    var arrows = {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    }
    $('.um_datepicker_5').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format:'dd-mm-yyyy',
        templates: arrows,
        todayBtn:true
    });
    $('.kt_select2_options, #kt_select2_options_validate').select2({
        placeholder: "Select",
        tags: true
    });
</script>
