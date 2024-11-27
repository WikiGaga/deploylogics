<style>
    .modal {pointer-events: none;}
    .modal-body{padding:0px;}
    .modal-backdrop{display: none;}
    .modal-header{padding:0.5rem 1rem;}
    .modal-title{color: #fff;}
    .modal-dialog{max-width:fit-content;}
    .product_detail_table{font-size: 12px;}
    .product_detail_table{margin-bottom: 0;}
    .text-danger {color: red !important;}
    .product_detail_table>thead{color: #000;background: #ffbb38;}
    .toast-modal .modal-header{background: #ffbb38;}
    .product-detail h5{font-size: 16px;}
    .text-black{color: #48465b !important;}
</style>
<div id="myModal" class="modal toast-modal fade" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row-form-group w-100">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-7">                                
                                <h4 class="modal-title">{{ isset($data['product'][0]) ? $data['product'][0]->product_name : "Error!"  }}</h4> 
                            </div>
                            <div class="col-lg-5">
                                @if(isset($data['product']))
                                    <div>
                                        <h5 class="text-white d-inline mr-1">Barcode:</h5><h5 class="d-inline text-black">{{ $data['product'][0]->product_barcode_barcode ?? "" }}</h5>
                                    </div>
                                    <div>
                                        <h5 class="text-white d-inline mr-1">UOM:</h5><h5 class="d-inline text-black">{{ $data['product'][0]->uom_name ?? "" }}</h5> , <h5 class="text-white d-inline mx-1">Packing:</h5><h5 class="d-inline text-black">{{ $data['product'][0]->product_barcode_packing ?? "" }}</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="close toast-dismiss" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
            @if(isset($data['error']))
                <div class="prod_block" style="min-height:200px;">
                    <div style="text-align: center; line-height: 150px; font-size: 29px; font-weight: 400; color: #9e9e9e;">{{$data['error']}}</div>
                </div>
            @else
                @if(isset($data['product']))
                    <div class="prod_block">
                        <table class="product_detail_table table table-bordered table-sm">
                            <thead>
                            <tr>
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
                                    <td title="Sale Price">Sale Price</td>
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
                                    <td title="Purchase Price">Purc Price</td>
                                    <td class="text-right">{{number_format($barcode['product_barcode_purchase_rate'],3)}}</td>
                                    @foreach($data['all_branches'] as $branch)
                                        @php
                                            $def_cost_price = collect(DB::select('SELECT get_stock_avg_rate(?,?,?,?,?) AS cost_price from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id]))->first()->cost_price;
                                        @endphp
                                        <td class="text-right">{{number_format($def_cost_price,3)}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td title="Total Stock">Total Stock</td>
                                    @php
                                        $now = new \DateTime("now");
                                        $today_format = $now->format("d-m-Y");
                                        $date = date('Y-m-d', strtotime($today_format));
                                        $def_total_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS total_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id,"",$date]))->first()->total_stock;
                                        $def_total_stock = (float)$def_total_stock / (float)$barcode['product_barcode_packing'];
                                    @endphp
                                    <td class="text-right">{{number_format($def_total_stock,3)}}</td>
                                    @foreach($data['all_branches'] as $branch)
                                        @php
                                            $now = new \DateTime("now");
                                            $today_format = $now->format("d-m-Y");
                                            $date = date('Y-m-d', strtotime($today_format));
                                            $def_total_stock = collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS total_stock from dual', [$barcode['product_id'], $barcode['product_barcode_id'], auth()->user()->business_id, auth()->user()->company_id, $branch->branch_id,"",$date]))->first()->total_stock;
                                            $def_total_stock = (float)$def_total_stock / (float)$barcode['product_barcode_packing'];
                                        @endphp
                                        <td class="text-right">{{number_format($def_total_stock,3)}}</td>
                                    @endforeach
                                </tr>
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                @else
                    <div class="prod_block" style="min-height:200px;">
                        <div style="text-align: center; line-height: 150px; font-size: 29px; font-weight: 400; color: #9e9e9e;">Found new barcode data..</div>
                    </div>
                @endif
            @endif
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->