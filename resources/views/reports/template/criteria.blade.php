<div class="kt-invoice__brand">
    <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
    <h6 class="kt-invoice__criteria">
        <span style="color: #e27d00;">Date:</span>
        <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
    </h6>
    @if(isset($data['all_branches']) && count($data['all_branches']) != 0)
        @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['all_branches'])->get('branch_name'); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Branch:</span>
            @foreach($branch_lists as $branch_list)
                <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if($data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
        @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
            @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->get('group_item_name_string'); @endphp
            <h6 class="kt-invoice__criteria">
                <span style="color: #e27d00;">Product Group:</span>
                @foreach($product_groups as $product_group)
                    <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
                @endforeach
            </h6>
        @endif
    @endif
    @if($data['key'] == 'stock_receiving')
        @if(isset($data['product']) && !empty($data['product']))
            <h6 class="kt-invoice__criteria">
                <span style="color: #e27d00;">Product:</span>
                <span style="color: #5578eb;">{{$data['product']->product_name}}</span>
            </h6>
        @endif
        @if(isset($data['supplier_ids']) && !empty($data['supplier_ids']))
            @php 
                $supplierDtl = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->where('supplier_id',$data['supplier_ids'])->first();
            @endphp
            <h6 class="kt-invoice__criteria">
                <span style="color: #e27d00;">Supplier:</span>
                <span style="color: #5578eb;">{{" ".$supplierDtl->supplier_name." "}}</span>
            </h6>
        @endif
    @endif
</div>
