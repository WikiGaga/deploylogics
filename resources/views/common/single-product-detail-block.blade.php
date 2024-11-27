@if(isset($data['error']))
    <div class="prod_block" style="min-height:200px;">
        <div style="text-align: center; line-height: 150px; font-size: 29px; font-weight: 400; color: #9e9e9e;">{{$data['error']}}</div>
    </div>
@else
    @if(isset($data['product']))
        <div class="prod_block" style="min-height:200px;">
            <div id="single_product_detail" class="card">
                <div class="card-body" style="background:radial-gradient(94.09% 94.09% at 50% 50%, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0) 100%), #FFA800;">
                    <div class="row prod">
                        <div class="prod-head col-lg-7">
                            <div class="card card-custom card-stretch card-transparent card-shadowless">
                                <div class="card-body d-flex flex-column justify-content-center pr-0">
                                    <h3 class="font-size-h4 font-size-h1-sm font-size-h4-lg font-size-h1-xl mb-0">
                                        <a href="javascript:;" class="text-black font-weight-bolder">{{$data['product'][0]['product_name']}}</a>
                                    </h3>
                                    <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark">{{$data['product'][0]['product_arabic_name']}}</div>
                                    <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark"><span style="color:#fd7e14;font-weight:bold;">Barcode:</span> <strong>{{$data['product'][0]['product_barcode_barcode']}}</strong></div>
                                    <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark"><span style="color:#fd7e14; font-weight:bold;">Sale Rate:</span> <strong>{{$data['pur_rate']['sale_rate']}}</strong></div>
                                    <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark"><span style="color:#fd7e14; font-weight:bold;">Packing:</span> <strong>{{@number_format($data['product'][0]['product_barcode_packing'],0)}}</strong></div>
                                    <div class="barcode_name font-size-lg font-size-h4-sm font-size-h6-lg font-size-h4-xl text-dark"><span style="color:#fd7e14; font-weight:bold;">Amount:</span> <strong>{{ @number_format($data['product'][0]['product_barcode_packing'] * $data['pur_rate']['sale_rate'],0)}}</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="prod-img col-lg-5">
                            @if(isset($data['product'][0]['product_image_url']) && $data['product'][0]['product_image_url'] != null)
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
                    <!--<th width="100px">Barcode</th>
                    <th width="50px">UOM</th>
                    <th width="50px">Packing</th>-->
                    <th width="85px"></th>
                    <th width="100px" class="text-center">{{auth()->user()->branch->branch_short_name}}</th>
                    @foreach($data['all_branches'] as $branch)
                        <th width="100px" class="text-center">{{$branch->branch_short_name}}</th>
                    @endforeach
                    <th width="50px">Total</th>
                </tr>
                </thead>
                @foreach($data['product'] as $barcode)
                    <tbody>
                    <tr>
                        <!--<td rowspan="4">{{$barcode['product_barcode_barcode']}}</td>
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
                    </tr>-->
                    <tr>
                        <td>Stock</td>
                        @php
                            $now = new \DateTime("now");
                            $today_format = $now->format("d-m-Y");
                            $date = date('Y-m-d', strtotime($today_format));
                            $totstock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS total_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id,"",$date]))->first()->total_stock;
                            $totstock = (float)$totstock;
                            // (float)$barcode['product_barcode_packing'];
                         @endphp
                        <td class="text-right">{{number_format($totstock,0)}}</td>
                        @foreach($data['all_branches'] as $branch)
                            @php
                                $now = new \DateTime("now");
                                $today_format = $now->format("d-m-Y");
                                $date = date('Y-m-d', strtotime($today_format));
                                $def_total_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS total_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id,"",$date]))->first()->total_stock;
                                //$def_total_stock = (float)$def_total_stock / (float)$barcode['product_barcode_packing'];
                                $totstock += (float)$def_total_stock;
                            @endphp
                            <td class="text-right">{{number_format($def_total_stock,0)}}</td>
                        @endforeach
                        <td class="text-right">{{number_format($totstock,0)}}</td>
                    </tr>
                    {{--<tr> 2-dec-21 - Will fix later
                        <td>Shelf Stock</td>
                        @php
                            $now = new \DateTime("now");
                            $today_format = $now->format("d-m-Y");
                            $date = date('Y-m-d', strtotime($today_format));
                            $showroom_id = 2;
                            $def_shelf_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS shelf_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id,$showroom_id,$date]))->first()->shelf_stock;
                        @endphp
                        <td class="text-right">{{number_format($def_shelf_stock,3)}}</td>
                        @foreach($data['all_branches'] as $branch)
                            @php
                                $now = new \DateTime("now");
                                $today_format = $now->format("d-m-Y");
                                $date = date('Y-m-d', strtotime($today_format));
                                $showroom_id = 2;
                                $def_shelf_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS shelf_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id,$showroom_id,$date]))->first()->shelf_stock;
                            @endphp
                            <td class="text-right">{{number_format($def_shelf_stock,3)}}</td>
                        @endforeach
                    </tr>--}}
                    </tbody>
                @endforeach
            </table>
        </div>
    @else
        <div class="prod_block" style="min-height:200px;">
            <div style="text-align: center; line-height: 150px; font-size: 29px; font-weight: 400; color: #9e9e9e;">Found new barcode data..</div>
        </div>
        {{--<div class="prod_block" style="height:200px;">
            <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style="top: 72px;"> <span style="text-align: center;">Found new barcode data..</span></div>
        </div>--}}
    @endif
@endif
