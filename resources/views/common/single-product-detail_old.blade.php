
<style>
    /***********************************
    Single Product Name
 */
    #single_product_detail>.card-body>.prod>.prod-img>img{
        height: 108px;
        object-fit: contain;
    }
    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless {
        line-height: 1.5;
        font-size: 13px !important;
        font-weight: 400;
        color: #3F4254;
        box-sizing: border-box;
        position: relative;
        min-width: 0;
        border-radius: 0.42rem;
        border: 0;
        display: flex;
        height: 100%;
        background-color: transparent;
    }

    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless>.card-body{
        padding: 2rem 2.25rem;
    }

    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless>.card-body>h3{
        font-size: 20px !important;
    }

    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless>.card-body>.barcode_name{
        line-height: 1.5;
        color: #181C32 !important;
        font-size: 16px !important;
    }
    button.modal_close {
        position: absolute;
        top: 10px;
        width: 20px;
        height: 20px;
        right: 10px;
        z-index: 999999;
    }
    .product_detail_table {
        margin-top: 10px;
    }

    .product_detail_table>thead {
        color: #fff;
        background: #ffbb38;
    }

    .product_detail_table>thead>tr>th {
        padding: 2px 5px;
        font-weight: 400;
    }

    .product_detail_table>tbody>tr>td {
        padding: 3px 5px;
    }
    .product_detail_table>tbody>tr:nth-child(even)>td {
        background: #fffbf4;
    }

    .product_detail_table>tbody>tr>td{
        font-size: 12px;
        font-weight: 400;
    }
    /**
        End Single Product Name
    **********************************/
</style>
<div class="modal-body" style="padding: 2px;">
    <button type="button" class="modal_close close" onclick="closeModal()" aria-label="Close"><i class="la la-times"></i></button>
    @if(isset($data['error']))
    <div id="single_product_detail" class="card">
        <div class="card-body" style="background:radial-gradient(94.09% 94.09% at 50% 50%, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0) 100%), #FFA800;">
            <span style="color:#fff;font-size: 24px;font-weight: 400;">{{$data['error']}}</span>
        </div>
    </div>
    @else
        @if($data['product'])
            <div id="single_product_detail" class="card">
                <div class="card-body" style="background:radial-gradient(94.09% 94.09% at 50% 50%, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0) 100%), #FFA800;">
                    <div class="row prod">
                            <div class="prod-head col-lg-7">
                                <div class="card card-custom card-stretch card-transparent card-shadowless">
                                    <div class="card-body d-flex flex-column justify-content-center pr-0">
                                        <h3 class="font-size-h4 font-size-h1-sm font-size-h4-lg font-size-h1-xl mb-0">
                                            <a href="#" class="text-white font-weight-bolder">{{$data['product'][0]['product_name']}}</a>
                                        </h3>
                                        <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark">{{$data['product'][0]['product_arabic_name']}}</div>
                                        <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark"><span style="color:#fff ">Barcode:</span> {{$data['product'][0]['product_barcode_barcode']}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="prod-img col-lg-5">
                                @if(isset($data['product'][0]['product_image_url']) && !empty($data['product'][0]['product_image_url']))
                                    <img src="/images/{{$data['product'][0]['product_image_url']}}" class="d-flex flex-row-fluid w-100">
                                @else
                                    <img src="/assets/media/products/Untitled.png" class="d-flex flex-row-fluid w-100">
                                @endif
                            </div>
                        </div>
                </div>
            </div>
            <table class="product_detail_table table table-bordered">
                <thead>
                    <tr>
                        <th width="100px">Barcode</th>
                        <th width="50px">UOM</th>
                        <th width="50px">Packing</th>
                        <th width="85px"></th>
                        <th width="100px" class="text-center">{{auth()->user()->branch->branch_short_name}}</th>
                        @foreach($data['all_branches'] as $branch)
                            <th width="100px" class="text-center">{{$branch->branch_short_name}}</th>
                        @endforeach
                    </tr>
                </thead>
                @foreach($data['product'] as $barcode)
                    <tbody>
                        <tr>
                            <td rowspan="4">{{$barcode['product_barcode_barcode']}}</td>
                            <td rowspan="4">{{$barcode['uom_name']}}</td>
                            <td rowspan="4">{{$barcode['product_barcode_packing']}}</td>
                            <td>Sale Price</td>
                            @php
                                $query = "select product_barcode_sale_rate_rate as rate from vw_purc_product_rate where product_category_id = '2' and branch_id = ".auth()->user()->branch_id." and product_id = ".$barcode['product_id']." and product_barcode_id = ".$barcode['product_barcode_id'] ;
                                $def_sale_price = collect(\DB::select($query))->first();
                            @endphp
                            <td class="text-right">{{empty($def_sale_price->rate)?number_format(0,3):number_format($def_sale_price->rate,3)}}</td>
                            @foreach($data['all_branches'] as $branch)
                                @php
                                    $query = "select product_barcode_sale_rate_rate as rate from vw_purc_product_rate where product_category_id = '2' and branch_id = ".$branch->branch_id." and product_id = ".$barcode['product_id']." and product_barcode_id = ".$barcode['product_barcode_id'] ;
                                    $def_sale_price = collect(\DB::select($query))->first();
                                @endphp
                                <td class="text-right">{{empty($def_sale_price->rate)?number_format(0,3):number_format($def_sale_price->rate,3)}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Purchase Price</td>
                            @php
                                // $def_cost_price = collect(DB::select('SELECT get_stock_avg_rate(?,?,?,?,?) AS cost_price from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id]))->first()->cost_price;
                            @endphp
                            <td class="text-right">{{number_format($barcode['product_barcode_purchase_rate'],3)}}</td>
                            @foreach($data['all_branches'] as $branch)
                                @php
                                    $def_cost_price = collect(DB::select('SELECT get_stock_avg_rate(?,?,?,?,?) AS cost_price from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id]))->first()->cost_price;
                                @endphp
                                <td class="text-right">{{number_format($def_cost_price,3)}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Total Stock</td>
                            @php
                                $now = new \DateTime("now");
                                $today_format = $now->format("d-m-Y");
                                $date = date('Y-m-d', strtotime($today_format));
                                $def_total_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS total_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id,"",$date]))->first()->total_stock;
                                $def_total_stock = number_format($def_total_stock/$barcode['product_barcode_packing'],0);
                            @endphp
                            <td class="text-right">{{$def_total_stock}}</td>
                            @foreach($data['all_branches'] as $branch)
                                @php
                                    $now = new \DateTime("now");
                                    $today_format = $now->format("d-m-Y");
                                    $date = date('Y-m-d', strtotime($today_format));
                                    $def_total_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS total_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id,"",$date]))->first()->total_stock;
                                    $def_total_stock = number_format($def_total_stock/$barcode['product_barcode_packing'],0);
                                @endphp
                                <td class="text-right">{{$def_total_stock}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Shelf Stock</td>
                            @php
                                $now = new \DateTime("now");
                                $today_format = $now->format("d-m-Y");
                                $date = date('Y-m-d', strtotime($today_format));
                                $showroom_id = 2;
                                $def_shelf_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS shelf_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id,$showroom_id,$date]))->first()->shelf_stock;
                            @endphp
                            <td class="text-right">{{$def_shelf_stock}}</td>
                            @foreach($data['all_branches'] as $branch)
                                @php
                                    $now = new \DateTime("now");
                                    $today_format = $now->format("d-m-Y");
                                    $date = date('Y-m-d', strtotime($today_format));
                                    $showroom_id = 2;
                                    $def_shelf_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS shelf_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id,$showroom_id,$date]))->first()->shelf_stock;
                                @endphp
                                <td class="text-right">{{$def_shelf_stock}}</td>
                            @endforeach
                        </tr>
                    </tbody>
                @endforeach
            </table>
        @endif
    @endif
</div>
<script>
    function closeModal(){
        $('#kt_modal_md').find('.modal-content').empty();
        $('#kt_modal_md').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('#kt_modal_md').modal('hide');
    }
</script>
