<div class="row">
    <div class="col-lg-12 text-center">
        <div class="product-barcode-innertabs--title">Stock Limits</div>
    </div>
</div>{{-- end row--}}
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblSL">
    <thead>
    <tr>
        <th width="20%" rowspan="2" class="text-middle">Branch Name</th>
        <th width="15%" rowspan="2" class="text-middle text-center">Negative Stock Allow</th>
        <th width="15%" rowspan="2" class="text-middle text-center">Re Order Point</th>
        <th width="15%" rowspan="2" class="text-middle text-center">Re Order Qty Level</th>
        <th width="20%" colspan="3" class="text-center">Stock Limit</th>
        <th width="15%" rowspan="2" class="text-middle text-center">Stock Limit Apply</th>
        <th width="15%" rowspan="2" class="text-middle text-center">Active Status</th>
    </tr>
    <tr class="height:25px;">
        <th width="6.66%" class="text-center">Max</th>
        <th width="6.66%" class="text-center">Min</th>
        <th width="6.66%" class="text-center">Consumption Days</th>
    </tr>
    </thead>
    <tbody>
    @php $sr = 0; @endphp
    @foreach($data['branch'] as $key=>$branch)
        @if($case == $edit || $case == $view)
            @foreach($pb['barcode_dtl'] as $b_dtl)
                @if($b_dtl->branch_id == $branch->branch_id)
                    @php
                        $product_barcode_stock_limit_neg_stock =  isset($b_dtl->product_barcode_stock_limit_neg_stock)?$b_dtl->product_barcode_stock_limit_neg_stock:0;
                        $product_barcode_stock_limit_reorder_qty =  $b_dtl->product_barcode_stock_limit_reorder_qty;
                        $product_barcode_shelf_stock_max_qty =  $b_dtl->product_barcode_shelf_stock_max_qty;
                        $product_barcode_shelf_stock_min_qty =  $b_dtl->product_barcode_shelf_stock_min_qty;
                        $product_barcode_consumption_days =  $b_dtl->product_barcode_stock_cons_day;
                        $product_barcode_stock_limit_limit_apply =  isset($b_dtl->product_barcode_stock_limit_limit_apply)?$b_dtl->product_barcode_stock_limit_limit_apply:0;
                        $product_barcode_stock_limit_status =  isset($b_dtl->product_barcode_stock_limit_status)?$b_dtl->product_barcode_stock_limit_status:0;
                        $stock_limit_reorder_point =  isset($b_dtl->product_barcode_stock_limit_reorder_point)?$b_dtl->product_barcode_stock_limit_reorder_point:0;
                    @endphp
                    @break
                @endif
            @endforeach
        @endif
        @php
            $stock_limit_neg_stock =  isset($product_barcode_stock_limit_neg_stock)?$product_barcode_stock_limit_neg_stock:0;
            $stock_limit_limit_apply =  isset($product_barcode_stock_limit_limit_apply)?$product_barcode_stock_limit_limit_apply:0;
            $stock_limit_status =  isset($product_barcode_stock_limit_status)?$product_barcode_stock_limit_status:0;
        @endphp
        <tr>
            <td><input type="hidden" class="branch_SL" name="branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
            <td class="text-center">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" {{$stock_limit_neg_stock==1?"checked":""}} name="stock_limit_neg_stock_{{$sr}}">
                            <span></span>
                        </label>
                    </span>
            </td>
            <td>
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($stock_limit_reorder_point)?$stock_limit_reorder_point:""}}" name="stock_limit_reorder_point_{{$sr}}">
            </td>
            <td>
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_stock_limit_reorder_qty)?$product_barcode_stock_limit_reorder_qty:""}}" name="stock_qty_level_{{$sr}}">
            </td>
            <td>
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_shelf_stock_max_qty)?$product_barcode_shelf_stock_max_qty:""}}" name="stock_max_limit_{{$sr}}">
            </td>
            <td>
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_shelf_stock_min_qty)?$product_barcode_shelf_stock_min_qty:""}}" name="stock_min_limit_{{$sr}}">
            </td>
            <td>
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_consumption_days)?$product_barcode_consumption_days:""}}" name="stock_consumption_days_{{$sr}}">
            </td>
            <td class="text-center">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" {{$stock_limit_limit_apply==1?"checked":""}} name="stock_limit_apply_status_{{$sr}}">
                            <span></span>
                        </label>
                    </span>
            </td>
            <td class="text-center">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" {{$stock_limit_status==1?"checked":""}} name="stock_status_{{$sr}}">
                            <span></span>
                        </label>
                    </span>
            </td>
        </tr>
        @php $sr++; @endphp
    @endforeach
    </tbody>
</table>


<div class="row">
    <div class="col-lg-12 text-center">
        <div class="product-barcode-innertabs--title">Shelf Stock Limits</div>
    </div>
</div>{{-- end row--}}
@php
    $ssl_width = 80/7;
@endphp
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblSSL">
    <thead>
    <tr>
        <th width="20%">Branch Name</th>
        <th width="{{$ssl_width}}%">Stock Location</th>
        <th width="{{$ssl_width}}%">Salesman</th>
        <th width="{{$ssl_width}}%">Max Qty</th>
        <th width="{{$ssl_width}}%">Min Qty</th>
        <th width="{{$ssl_width}}%">Depth Qty</th>
        <th width="{{$ssl_width}}%">Face Qty</th>
        <th width="{{$ssl_width}}%">Reorder Point</th>
    </tr>
    </thead>
    <tbody>
    @php $sr = 0; @endphp
    @foreach($data['branch'] as $key=>$branch)
        @php
            $user_id = 0;
            $location = 0;
        @endphp
        @if($case == $new)

        @endif
        @if($case == $edit || $case == $view)
            @foreach($pb['barcode_dtl'] as $b_dtl)
                @if($b_dtl->branch_id == $branch->branch_id)
                    @php
                        $product_barcode_shelf_stock_location =  isset($b_dtl->product_barcode_shelf_stock_location)?$b_dtl->product_barcode_shelf_stock_location:'';
                        $user_id =  isset($b_dtl->user->id)?$b_dtl->user->id:"";
                        $product_barcode_stock_limit_max_qty =  $b_dtl->product_barcode_stock_limit_max_qty;
                        $product_barcode_stock_limit_min_qty =  $b_dtl->product_barcode_stock_limit_min_qty;
                        $shelf_stock_dept_qty =  $b_dtl->product_barcode_shelf_stock_dept_qty;
                        $shelf_stock_face_qty =  $b_dtl->product_barcode_shelf_stock_face_qty;
                        $shelf_stock_reorder_point =  $b_dtl->product_barcode_shelf_stock_reorder_point;
                    @endphp
                    @break
                @endif
            @endforeach
        @endif
        <tr>
            <td><input type="hidden" class="branch_SSL" name="stock_branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
            <td>
                <div class="erp-select2 form-group">
                    <select class="form-control kt-select2 erp-form-control-sm shelf_stock_location" name="shelf_stock_location_{{$sr}}">
                        <option value="">Select</option>
                        @php $location = isset($product_barcode_shelf_stock_location)?$product_barcode_shelf_stock_location:'' @endphp
                        @foreach($data['display_location'] as $display_location)
                            <option value="{{$display_location->display_location_id}}" {{ $location == $display_location->display_location_id?'selected':'' }} >{{$display_location->display_location_name_string}}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="erp-select2">
                    <select class="form-control erp-form-control-sm kt-select2 shelf_stock_salesman" name="shelf_stock_salesman_{{$sr}}">
                        <option value="">Select</option>
                        @foreach($data['users'] as $user)
                            @if($user->branch_id == $branch->branch_id)
                                <option value="{{$user->id}}" {{$user_id == $user->id?"selected":"" }}>{{$user->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </td>
            <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_stock_limit_max_qty)?$product_barcode_stock_limit_max_qty:""}}" name="shelf_stock_max_qty_{{$sr}}"></td>
            <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_stock_limit_min_qty)?$product_barcode_stock_limit_min_qty:""}}" name="shelf_stock_min_qty_{{$sr}}"></td>
            <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($shelf_stock_dept_qty)?$shelf_stock_dept_qty:""}}" name="shelf_stock_dept_qty_{{$sr}}"></td>
            <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($shelf_stock_face_qty)?$shelf_stock_face_qty:""}}" name="shelf_stock_face_qty_{{$sr}}"></td>
            <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($shelf_stock_reorder_point)?$shelf_stock_reorder_point:""}}" name="shelf_stock_reorder_point_{{$sr}}"></td>
        </tr>
        @php $sr++; @endphp
    @endforeach
    </tbody>
</table>
