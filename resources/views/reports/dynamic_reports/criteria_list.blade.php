<div class="kt-invoice__brand" style="width: {{($data['form_file_type'] == 'pdf')?"70%":""}};">
    <h1 class="kt-invoice__title">{{strtoupper($report_tb_data['report_title'])}}</h1>
    @if(isset($data['from_date'])  && isset($data['to_date']) && $data['from_date'] != "" && $data['to_date'] != "")
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Date:</span>
            <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
        </h6>
    @endif
    @if(isset($data['date']) && $data['date'] != "")
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Date:</span>
            <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))}}</span>
        </h6>
    @endif
    @if(count($data['branch_ids']) != 0)
        @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Branch:</span>
            @foreach($branch_lists as $branch_list)
                <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['product']) && $data['product'] != "")
        @php $product = \Illuminate\Support\Facades\DB::table('tbl_purc_product')->where('product_id',$data['product'])->first('product_name'); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Product Name:</span>
            <span style="color: #5578eb;">{{$product->product_name}}</span>
        </h6>
    @endif
    @if(isset($data['products']) && count($data['products']) != 0)
        @php $product_lists = \Illuminate\Support\Facades\DB::table('tbl_purc_product')->whereIn('product_id',$data['products'])->get('product_name'); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Product Name:</span>
            @foreach($product_lists as $product_list)
                <span style="color: #5578eb;">{{$product_list->product_name}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['rate_type']) &&  $data['rate_type'] != "" && $data['rate_type'] != 0){
    <h6 class="kt-invoice__criteria">
        <span style="color: #e27d00;">Rate Type:</span>
        @if($data['rate_type'] == 1)
            <span style="color: #5578eb;">Sale Rate</span>
        @endif
        @if($data['rate_type'] == 2)
            <span style="color: #5578eb;">Cost Rate</span>
        @endif
    </h6>
    @endif
    @if(isset($data['store_ids']) && count($data['store_ids']) != 0 && $data['store_ids'] != "" && $data['store_ids'] != null)
        @php $stores = \Illuminate\Support\Facades\DB::table('tbl_defi_store')->whereIn('store_id',$data['store_ids'])->get('store_name'); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Stores:</span>
            @foreach($stores as $store)
                <span style="color: #5578eb;">{{$store->store_name}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['sales_type']) &&  $data['sales_type'] != ""){
    <h6 class="kt-invoice__criteria">
        <span style="color: #e27d00;">Sales Type:</span>
        <span style="color: #5578eb;">{{$data['sales_type']}}</span>
    </h6>
    @endif
    @if(isset($data['sales_types']) &&  count($data['sales_types']) != 0 && $data['sales_types'] != "" && $data['sales_types'] != null)
        @php $sales_types = \Illuminate\Support\Facades\DB::select('select distinct sales_type from vw_sale_sales_invoice'); @endphp
        {{--{{dd($sales_types)}}--}}
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Sales Type:</span>
            @foreach($data['sales_types'] as $sales_type)
                <span style="color: #5578eb;">{{strtoupper($sales_type)}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['product_group']) &&  count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
        @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->get('group_item_name_string'); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Product Group:</span>
            @foreach($product_groups as $product_group)
                <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['chart_account']) &&  $data['chart_account'] != ""){
    @php $chart_account = \Illuminate\Support\Facades\DB::table('tbl_acco_chart_account')->where('chart_account_id',$data['chart_account'])->first(); @endphp
    <h6 class="kt-invoice__criteria">
        <span style="color: #e27d00;">Chart Account:</span>
        <span style="color: #5578eb;">{{$chart_account->chart_code}} - {{$chart_account->chart_name}}<</span>
    </h6>
    @endif
    @if(isset($data['chart_account_multiple']) &&  count($data['chart_account_multiple']) != 0 && $data['chart_account_multiple'] != "" && $data['chart_account_multiple'] != null)
        @php $chart_accounts = \Illuminate\Support\Facades\DB::table('tbl_acco_chart_account')->whereIn('chart_account_id',$data['chart_account_multiple'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Chart Account:</span>
            @foreach($chart_accounts as $chart_account)
                <span style="color: #5578eb;">{{$chart_account->chart_code}} - {{$chart_account->chart_name}}</span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['customer_group']) &&  count($data['customer_group']) != 0 && $data['customer_group'] != "" && $data['customer_group'] != null)
        @php $customer_groups = \Illuminate\Support\Facades\DB::table('tbl_sale_customer_type')->whereIn('customer_type_id',$data['customer_group'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Customer Group:</span>
            @foreach($customer_groups as $customer_group)
                <span style="color: #5578eb;">{{$customer_group->customer_type_name}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['customers']) &&  count($data['customers']) != 0 && $data['customers'] != "" && $data['customers'] != null)
        @php $customers = \Illuminate\Support\Facades\DB::table('tbl_sale_customer')->whereIn('customer_id',$data['customers'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Customer:</span>
            @foreach($customers as $customer)
                <span style="color: #5578eb;">{{$customer->customer_name}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['payment_types']) &&  count($data['payment_types']) != 0 && $data['payment_types'] != "" && $data['payment_types'] != null)
        @php $payment_types = \Illuminate\Support\Facades\DB::table('tbl_defi_payment_type')->whereIn('payment_type_id',$data['payment_types'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Payment Type:</span>
            @foreach($payment_types as $payment_type)
                <span style="color: #5578eb;">{{$payment_type->payment_type_name}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['users']) &&  count($data['users']) != 0 && $data['users'] != "" && $data['users'] != null)
        @php $users = \Illuminate\Support\Facades\DB::table('users')->whereIn('id',$data['users'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">User:</span>
            @foreach($users as $user)
                <span style="color: #5578eb;">{{$user->name}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['supplier_group']) &&  count($data['supplier_group']) != 0 && $data['supplier_group'] != "" && $data['supplier_group'] != null)
        @php $supplier_groups = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier_type')->whereIn('supplier_type_id',$data['supplier_group'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Supplier Group:</span>
            @foreach($supplier_groups as $supplier_group)
                <span style="color: #5578eb;">{{$supplier_group->supplier_type_name}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['suppliers']) &&  count($data['suppliers']) != 0 && $data['suppliers'] != "" && $data['suppliers'] != null)
        @php $suppliers = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->whereIn('supplier_id',$data['suppliers'])->get(); @endphp
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Supplier:</span>
            @foreach($suppliers as $supplier)
                <span style="color: #5578eb;">{{$supplier->supplier_name}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
    @if(isset($data['voucher_types']) &&  count($data['voucher_types']) != 0 && $data['voucher_types'] != "" && $data['voucher_types'] != null)
        <h6 class="kt-invoice__criteria">
            <span style="color: #e27d00;">Supplier:</span>
            @foreach($data['voucher_types'] as $voucher_types)
                <span style="color: #5578eb;">{{$voucher_types}} </span><span style="color: #fd397a;">, </span>
            @endforeach
        </h6>
    @endif
</div>
