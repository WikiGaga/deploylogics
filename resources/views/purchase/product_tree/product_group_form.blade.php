@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    if($case == 'new'){
        $close = 'close_new';
    }else{
        $close = 'close_edit';
    }
@endphp
<button type="button" class="close {{$close}}" data-dismiss="modal" aria-label="Close" style="z-index: 99;top: 20px;right: 20px;position: absolute;">
    <span aria-hidden="true">&times;</span>
</button>
<div class="modal-body" style="padding: 2px;">
    @php
  //  dd($data);
        if($case == 'new'){
            $code = $data['code'];
            $parent = $data['parent'];
            $parentGroup = $parent->group_item_id;
        }
        if($case == 'edit'){
            $parent = $data['parent'];
            $id = $data['current']->group_item_id;
            $name = $data['current']->group_item_name;
            $mlname = $data['current']->group_item_mother_language_name;
            $parentGroup = isset($data['current']->parent->group_parent_item_id)?$data['current']->parent->group_parent_item_id:'';
            $productType = isset($data["current"]->product_type_group_id)?$data["current"]->product_type_group_id:'2';
            $brandCode = $data['current']->group_item_brand_validation;
            $saleStatus = $data['current']->group_item_sales_status;
            $expiryDetail = $data['current']->group_item_expiry;
            $stockDetail = $data['current']->group_item_stock_type;
            $code = (isset($data['code_string']->group_item_name_code_string)?$data['code_string']->group_item_name_code_string:'').'-'.$data['current']->group_item_code ;
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="product_group_form" class="kt-form" method="post" action="{{ action('Purchase\GroupitemController@store',isset($id)?$id:'') }}" autocomplete="off">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" style="padding: 0 !important;">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Code:</label>
                        <div class="col-lg-6">
                            <input type="text" id="group_item_code" name="group_item_code" value="{{isset($code)?$code:''}}" class="form-control erp-form-control-sm" readonly style="background: #f1f1f1">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Parent Group:</label>
                        <div class="col-lg-6">
                            <div class="kt-input-icon kt-input-icon--right">
                                <input type="hidden" id="parent_group_id" name="parent_group_id" value="{{$parentGroup}}">
                                <input type="text" value="{{$parent->group_item_name_string}}" class="form-control erp-form-control-sm" readonly style="background: #f1f1f1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Name:<span class="required">* </span></label>
                        <div class="col-lg-6">
                            <input type="text" name="group_item_name" value="{{isset($name)?$name:''}}"  maxlength="100" class="form-control erp-form-control-sm checkHasValue">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Mother Language Name:</label>
                        <div class="col-lg-6">
                            <input type="text" dir="auto" maxlength="100" id="group_item_mother_language_name" name="group_item_mother_language_name" value="{{isset($mlname)?$mlname:''}}" class="form-control erp-form-control-sm checkHasValue">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Product Type Group:<span class="required">* </span></label>
                        <div class="col-lg-6">
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="product_type_group_id">
                                    <option value="0">Select</option>
                                    @if($case == 'edit')
                                        @foreach($data['producttypegroup'] as $typegroup)
                                            @php $productType = isset($productType)?$productType:""; @endphp
                                            <option value="{{$typegroup->product_type_group_id}}" {{ $typegroup->product_type_group_id == $productType?'selected':'' }}>{{$typegroup->product_type_group_name}}</option>
                                        @endforeach
                                    @else
                                        @foreach($data['producttypegroup'] as $typegroup)
                                            <option value="{{$typegroup->product_type_group_id}}" {{ $typegroup->product_type_group_name == 'Inventory'?'selected':'' }}>{{$typegroup->product_type_group_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Apply Brand Code:</label>
                                <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $brandCode = isset($brandCode)?$brandCode:""; @endphp
                                            <input type="checkbox" name="group_item_brand_validation" {{$brandCode == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" checked="checked" name="group_item_brand_validation">
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Sale Status:</label>
                                <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $saleStatus = isset($saleStatus)?$saleStatus:""; @endphp
                                            <input type="checkbox" name="group_item_sales_status" {{$saleStatus == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" checked="checked" name="group_item_sales_status">
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
                                <label class="col-lg-6 erp-col-form-label">Has Expiry Detail:</label>
                                <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $expiryDetail = isset($expiryDetail)?$expiryDetail:""; @endphp
                                            <input type="checkbox" name="group_item_expiry" {{$expiryDetail == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" checked="checked" name="group_item_expiry">
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Has Stock Type Detail:</label>
                                <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $stockDetail = isset($stockDetail)?$stockDetail:""; @endphp
                                            <input type="checkbox" name="group_item_stock_type" {{$stockDetail == 1?"checked":""}}>
                                        @else
                                            <input type="checkbox" checked="checked" name="group_item_stock_type">
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
    <script src="{{ asset('js/pages/js/purchase/product-tree-product-group-create.js') }}" type="text/javascript"></script>
    <script>
        $('#kt_select2_1, #kt_select2_1_validate').select2({
            placeholder: "Select Branches",
            tags: true
        });
    </script>
    @endpermission
</div>
