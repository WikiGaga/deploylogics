@extends('layouts.log_layout')
@section('title', 'Log Product')

@section('pageCSS')
    <style>
        body {
            color: #686868 !important;
        }
        .tags_style{
            background: #ff9800;
            padding: 4px;
            border-radius: 3px;
            color: #fff;
            margin-right: 5px;
        }
        .log_table th{
            background: #f9f9f9 !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            font-family: Verdana !important;
        }
        .log_table td{
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            font-family: Verdana !important;
        }
        .log_table tr:nth-child(odd) {
            background: #f3f3f3;
        }
    </style>
@endsection
@if($data['current'])
@permission($data['permission'])
@section('content')
    @php
        $current = $data['current'];
      //  dd($current->toArray());
    @endphp
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile" style=" margin-bottom: 5px;">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-file"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Product<small class="text-capitalize">log</small>
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-12">
                        <span class="kt-margin-r-20">
                            <b>User:</b> {{$current->user->name}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Branch:</b> {{$current->branch->branch_name}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Date & Time:</b> {{date('d-m-Y H:i:s a', strtotime($current->created_at))}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Action Type:</b> {{$current->action_type}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Document Form:</b> {{$current->document_name}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Activity Form:</b> {{$current->activity_form_type}}
                        </span>
                    </div>
                    <div class="col-lg-12">
                        @php
                            $browser_dtl = unserialize($current->browser_dtl);
                        @endphp
                        <span class="kt-margin-r-20">
                            <b>IP Address:</b> {{$current->ip_address}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Browser Name:</b> {{$browser_dtl['name'].' , '.$browser_dtl['platform']}}
                        </span>
                        <span class="kt-margin-r-20">
                            <b>Remarks:</b> {{$current->remarks}}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            @php
                $form_data = unserialize($current->form_data);
                //dd($form_data);
            @endphp
            <div class="kt-portlet__body">
                <div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <span><b>Product Code: </b> {{$form_data->product_code}}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span><b>Product Name: </b> {{$form_data->product_name}}</span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Product Short Name: </b> {{$form_data->product_short_name}}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span><b>Arabic Name: </b> {{$form_data->product_arabic_name}}</span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Arabic Short Name: </b> {{$form_data->product_arabic_short_name}}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span><b>Status: </b> {{($form_data->product_entry_status == 1)?"Yes":"No"}}</span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Can Sale: </b> {{($form_data->product_can_sale == 1)?"Yes":"No"}}</span>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#general_information" role="tab">General Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#barcodes" role="tab">Barcodes</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="general_information" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                @php
                                    $group_item_name_string = "";
                                    $group_item = \App\Models\ViewPurcGroupItem::where('group_item_id',$form_data->group_item_id)->first();
                                    if($group_item != null){
                                        $group_item_name_string = $group_item->group_item_name_string;
                                    }
                                @endphp
                                <span><b>Product Group: <span class="required">*</span> </b> {{$group_item_name_string}}</span>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $product_type_name = "";
                                      $ProductType =   \App\Models\TblPurcProductType::where('product_type_id',$form_data->product_type_id)->first();
                                      if($ProductType != null){
                                          $product_type_name = $ProductType->product_type_name;
                                      }
                                @endphp
                                <span><b>Product Type Group: </b> {{$product_type_name}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                @php
                                    $product_type_name = "";
                                    $product_type = \App\Models\TblPurcProductType::where('product_type_id',$form_data->product_type_id)->first();
                                    if($product_type != null){
                                        $product_type_name = $product_type->product_type_name;
                                    }
                                @endphp
                                <span><b>Product Type: </b> {{$product_type_name}}</span>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $supplier_name = "";
                                      $supplier =   \App\Models\TblPurcSupplier::where('supplier_id',$form_data->supplier_id)->first();
                                      if($supplier != null){
                                          $supplier_name = $supplier->supplier_name;
                                      }
                                @endphp
                                <span><b>Supplier: </b> {{$supplier_name}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                @php
                                    $manufacturer_name = "";
                                    $manufacturer = \App\Models\TblPurcManufacturer::where('manufacturer_id',$form_data->product_manufacturer_id)->first();
                                    if($manufacturer != null){
                                        $manufacturer_name = $manufacturer->manufacturer_name;
                                    }
                                @endphp
                                <span><b>Manufacturer: </b> {{$manufacturer_name}}</span>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $country_name = "";
                                      $country =   \App\Models\TblDefiCountry::where('country_id',$form_data->country_id)->first();
                                      if($country != null){
                                          $country_name = $country->country_name;
                                      }
                                @endphp
                                <span><b>Country: </b> {{$country_name}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                @php
                                    $brand_name = "";
                                    $brand = \App\Models\TblPurcBrand::where('brand_id',$form_data->product_brand_id)->first();
                                    if($brand != null){
                                        $brand_name = $brand->brand_name;
                                    }
                                @endphp
                                <span><b>Brand Name: </b> {{$brand_name}}</span>
                            </div>
                            <div class="col-md-6">
                                @php $col = []; @endphp
                                @foreach($form_data->specification_tags as $specification_tags)
                                    @php array_push($col,$specification_tags['tag_id']); @endphp
                                @endforeach
                                @php
                                    $specifics =   \App\Models\TblDefiTags::whereIn('tags_id',$col)->get();
                                @endphp
                                <span><b>Specification Tags: </b>
                                    @foreach($specifics as $specific)
                                        <span class="tags_style">{{$specific->tags_name}}</span>
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                @php $col = []; @endphp
                                @foreach($form_data->item_tags as $item_tags)
                                    @php array_push($col,$item_tags['tag_id']); @endphp
                                @endforeach
                                @php
                                    $items =   \App\Models\TblDefiTags::whereIn('tags_id',$col)->get();
                                @endphp
                                <span><b>Item Tags: </b>
                                    @foreach($items as $item)
                                        <span class="tags_style">{{$item->tags_name}}</span>
                                    @endforeach
                                </span>
                            </div>
                            <div class="col-md-6">
                                <span><b>Demand Active Status: </b> {{($form_data->product_demand_active_status == 1)?"Yes":"No"}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span><b>Warranty Status: </b> {{($form_data->product_warranty_status == 1)?"Yes":"No"}}</span>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $warrenty_period_name = "";
                                      $warrentyPeriod =   \App\Models\TblPurcWarrentyPeriod::where('warrenty_period_id',$form_data->product_warranty_period_id)->first();
                                      if($warrentyPeriod != null){
                                          $warrenty_period_name = $warrentyPeriod->warrenty_period_name;
                                      }
                                @endphp
                                <span><b>Warranty Period: </b> {{ $form_data->product_warranty_period_mode.' '.$warrenty_period_name}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span><b>Perishable: </b> {{($form_data->product_perishable == 1)?"Yes":"No"}}</span>
                            </div>

                            <div class="col-md-6">
                                <span><b>Tracing Days: </b> {{ $form_data->product_tracing_days}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span><b>Batch No Required: </b> {{($form_data->product_batch_req == 1)?"Yes":"No"}}</span>
                            </div>

                            <div class="col-md-6">
                                <span><b>Expiry Return Allow: </b> {{($form_data->product_expiry_return_allow == 1)?"Yes":"No"}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span><b>Damage Return Allow: </b> {{($form_data->product_damage_return_allow == 1)?"Yes":"No"}}</span>
                            </div>

                            <div class="col-md-6">
                                <span><b>Expiry Required: </b> {{($form_data->product_expiry_required == 1)?"Yes":"No"}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span><b>Expiry Base On: </b> {{$form_data->product_expiry_base}}</span>
                            </div>

                            <div class="col-md-6">
                                <span><b>Shelf Life Minimum %: </b> {{$form_data->product_shelf_life_minimum}}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <span><b>Notes: </b> {{$form_data->product_remarks}}</span>
                            </div>
                        </div>
                        <h6 style="color: #673ab7">Product Life:</h6>
                        <div class="form-group-block">
                            <div class="erp_form___block">
                                <div class="table-scroll form_input__block">
                                    <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <tr>
                                            <th width="10%">Sr.No</th>
                                            <th width="40%">Country</th>
                                            <th width="25%">Period Type</th>
                                            <th width="25%">Period</th>
                                        </tr>
                                        @foreach($form_data->product_life as $product_life)
                                            @php
                                                $country_name = "";
                                                  $country =   \App\Models\TblDefiCountry::where('country_id',$product_life['country_id'])->first();
                                                  if($country != null){
                                                      $country_name = $country->country_name;
                                                  }
                                            @endphp
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$country_name}}</td>
                                                <td>{{$product_life['product_life_period_type']}}</td>
                                                <td>{{$product_life['product_life_period']}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <h6 style="color: #673ab7">Product Purchase FOC:</h6>
                        <div class="form-group-block">
                            <div class="erp_form___block">
                                <div class="table-scroll form_input__block">
                                    <table class="log_table table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <tr>
                                            <th width="10%">Sr.No</th>
                                            <th width="40%">Supplier</th>
                                            <th width="25%">QTy</th>
                                            <th width="25%">FOC Qty</th>
                                        </tr>
                                        @foreach($form_data->product_foc as $product_foc)
                                            @php
                                                $supplier_name = "";
                                                  $supplier =   \App\Models\TblPurcSupplier::where('supplier_id',$product_foc['supplier_id'])->first();
                                                  if($supplier != null){
                                                      $supplier_name = $supplier->supplier_name;
                                                  }
                                            @endphp
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$supplier_name}}</td>
                                                <td>{{$product_foc['product_foc_purc_qty']}}</td>
                                                <td>{{$product_foc['product_foc_foc_qty']}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="barcodes" role="tabpanel">
                        @foreach($form_data->product_barcode as $pb)
                            <div>
                                <div class="row mb-3" style="    color: #673ab7;background: #e5e5e5; padding: 10px; margin: 0;">
                                    <div class="col-md-3">
                                        <div>
                                            <span><b>Barcode: </b> {{ $pb['product_barcode_barcode']}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div>
                                            <span><b>Weight Apply: </b> {{($pb['product_barcode_weight_apply'] == 1)?"Yes":"No"}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs col-lg-12" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active uom_packing" data-toggle="tab" href="#uom_packing{{ $pb['product_barcode_id'] }}" role="tab">UOM & Packing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rate" data-toggle="tab" href="#rate{{ $pb['product_barcode_id'] }}" role="tab">Rate</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link inventory_shelf_stock" data-toggle="tab" href="#inventory_shelf_stock{{ $pb['product_barcode_id'] }}" role="tab">Inventory & Shelf Stock</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link tax" data-toggle="tab" href="#tax{{ $pb['product_barcode_id'] }}" role="tab">Tax</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link purchase_foc" data-toggle="tab" href="#purchase_foc{{ $pb['product_barcode_id'] }}" role="tab">Purchase & FOC</a>
                                </li>
                            </ul>
                            <div class="tab-content col-lg-12">
                                <div class="tab-pane active uom_packing_content" id="uom_packing{{ $pb['product_barcode_id'] }}">
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <tr>
                                                        <th width="20%">UOM <span class="required">*</span></th>
                                                        <th width="20%">Packing <span class="required">*</span></th>
                                                        <th width="20%">Color</th>
                                                        <th width="20%">Size</th>
                                                        <th width="20%">Variant</th>
                                                    </tr>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{$pb['uom']['uom_name']}}</td>
                                                            <td>{{$pb['product_barcode_packing']}}</td>
                                                            <td>
                                                                @php $col = []; @endphp
                                                                @foreach($pb['color'] as $color)
                                                                    @php array_push($col,$color['color_id']); @endphp
                                                                @endforeach
                                                                @php
                                                                    $colors =   \App\Models\TblDefiColor::whereIn('color_id',$col)->get();
                                                                @endphp
                                                                @foreach($colors as $color_name)
                                                                    <span class="tags_style">{{$color_name->color_name}}</span>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                @php $col = []; @endphp
                                                                @foreach($pb['size'] as $size_id)
                                                                    @php array_push($col,$size_id['size_id']); @endphp
                                                                @endforeach
                                                                @php
                                                                    $sizes =   \App\Models\TblDefiSize::whereIn('size_id',$col)->get();
                                                                @endphp
                                                                @foreach($sizes as $size_name)
                                                                    <span class="tags_style">{{$size_name->size_name}}</span>
                                                                @endforeach
                                                            </td>
                                                            <td> </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane rate" id="rate{{ $pb['product_barcode_id'] }}">
                                    <h6 style="color: #673ab7">Sale Rate:</h6>
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                @php
                                                    $rate_category_width = 60/(int)count($data['rate_category']);
                                                @endphp
                                                <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <tr>
                                                        <th width="40%">Branch Name </th>
                                                        @foreach($data['rate_category'] as $key=>$rate_category)
                                                            <th width="{{$rate_category_width}}%" class="text-center">{{$rate_category->rate_category_name}}</th>
                                                        @endforeach
                                                    </tr>
                                                    <tbody>
                                                    @foreach($data['branch'] as $key=>$branch)
                                                    <tr>
                                                        <td>{{$branch->branch_name}}</td>
                                                        @foreach($data['rate_category'] as $key=>$rate_category)
                                                            @php $sale_rate_rate = '';@endphp
                                                            @foreach($pb['sale_rate'] as $saleRate)
                                                                @if($saleRate['branch_id'] == $branch->branch_id && $saleRate['product_category_id'] == $rate_category->rate_category_id)
                                                                    @php $sale_rate_rate = $saleRate['product_barcode_sale_rate_rate'] @endphp
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                            <td class="text-center">{{$sale_rate_rate != "" ? number_format($sale_rate_rate,3):""}} </td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <h6 style="color: #673ab7">Purchase Rate:</h6>
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <tr>
                                                        <th width="40%">Branch Name </th>
                                                        <th width="20%" class="text-center">Purchase Rate </th>
                                                        <th width="20%" class="text-center">Cost Rate </th>
                                                        <th width="20%" class="text-center">Avg Rate </th>
                                                    </tr>
                                                    <tbody>
                                                    @foreach($data['branch'] as $key=>$branch)
                                                        @foreach($pb['purc_rate'] as $purc_rate)
                                                            @if($purc_rate['branch_id'] == $branch->branch_id)
                                                                @php
                                                                    if($purc_rate['product_barcode_purchase_rate'] != null && $purc_rate['product_barcode_purchase_rate'] != ""){
                                                                        $purchase_rate = $purc_rate['product_barcode_purchase_rate'];
                                                                    }
                                                                    if($purc_rate['product_barcode_cost_rate'] != null && $purc_rate['product_barcode_cost_rate'] != ""){
                                                                        $cost_rate = $purc_rate['product_barcode_cost_rate'];
                                                                    }
                                                                    if($purc_rate['product_barcode_avg_rate'] != null && $purc_rate['product_barcode_avg_rate'] != ""){
                                                                        $avg_rate = $purc_rate['product_barcode_avg_rate'];
                                                                    }
                                                                @endphp
                                                                @break
                                                            @endif
                                                            @php
                                                                $purchase_rate = "";
                                                                $cost_rate = "";
                                                                $avg_rate = "";
                                                            @endphp
                                                        @endforeach
                                                        <tr>
                                                            <td>{{$branch->branch_name}}</td>
                                                            <td class="text-center">{{isset($purchase_rate) && $purchase_rate != "" ? number_format($purchase_rate,3) :""}}</td>
                                                            <td class="text-center">{{isset($cost_rate) && $cost_rate != "" ? number_format($cost_rate,3) :""}}</td>
                                                            <td class="text-center">{{isset($avg_rate) && $avg_rate != "" ? number_format($avg_rate,3) :""}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane inventory_shelf_stock" id="inventory_shelf_stock{{ $pb['product_barcode_id'] }}">
                                    <h6 style="color: #673ab7">Stock Limits:</h6>
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">

                                                    <tr>
                                                        <th width="20%" rowspan="2" class="text-middle">Branch Name</th>
                                                        <th width="15%" rowspan="2" class="text-center">Negative Stock Allow</th>
                                                        <th width="15%" rowspan="2" class="text-center">Re Order Qty Level</th>
                                                        <th width="20%" colspan="2" class="text-center">Stock Limit</th>
                                                        <th width="15%" rowspan="2" class="text-center">Stock Limit Apply</th>
                                                        <th width="15%" rowspan="2" class="text-center">Active Status</th>
                                                    </tr>
                                                    <tr>
                                                        <th width="10%" class="text-center">Max</th>
                                                        <th width="10%" class="text-center">Min</th>
                                                    </tr>
                                                    <tbody>
                                                    @foreach($data['branch'] as $key=>$branch)
                                                        @foreach($pb['barcode_dtl'] as $b_dtl)
                                                            @if($b_dtl['branch_id'] == $branch->branch_id)
                                                                @php
                                                                    $product_barcode_stock_limit_neg_stock =  isset($b_dtl['product_barcode_stock_limit_neg_stock'])?$b_dtl['product_barcode_stock_limit_neg_stock']:0;
                                                                    $product_barcode_stock_limit_reorder_qty =  $b_dtl['product_barcode_stock_limit_reorder_qty'];
                                                                    $product_barcode_shelf_stock_max_qty =  $b_dtl['product_barcode_shelf_stock_max_qty'];
                                                                    $product_barcode_shelf_stock_min_qty =  $b_dtl['product_barcode_shelf_stock_min_qty'];
                                                                    $product_barcode_stock_limit_limit_apply =  isset($b_dtl['product_barcode_stock_limit_limit_apply'])?$b_dtl['product_barcode_stock_limit_limit_apply']:0;
                                                                    $product_barcode_stock_limit_status =  isset($b_dtl['product_barcode_stock_limit_status'])?$b_dtl['product_barcode_stock_limit_status']:0;
                                                                @endphp
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        @php
                                                            $stock_limit_neg_stock =  isset($product_barcode_stock_limit_neg_stock)?$product_barcode_stock_limit_neg_stock:0;
                                                            $stock_limit_limit_apply =  isset($product_barcode_stock_limit_limit_apply)?$product_barcode_stock_limit_limit_apply:0;
                                                            $stock_limit_status =  isset($product_barcode_stock_limit_status)?$product_barcode_stock_limit_status:0;
                                                        @endphp
                                                        <tr>
                                                            <td>{{$branch->branch_name}}</td>
                                                            <td class="text-center">{{$stock_limit_neg_stock==1?"Yes":"No"}}</td>
                                                            <td class="text-center">{{isset($product_barcode_stock_limit_reorder_qty)?$product_barcode_stock_limit_reorder_qty:""}}</td>
                                                            <td class="text-center">{{isset($product_barcode_shelf_stock_max_qty)?$product_barcode_shelf_stock_max_qty:""}}</td>
                                                            <td class="text-center">{{isset($product_barcode_shelf_stock_min_qty)?$product_barcode_shelf_stock_min_qty:""}}</td>
                                                            <td class="text-center">{{$stock_limit_limit_apply==1?"Yes":"No"}}</td>
                                                            <td class="text-center">{{$stock_limit_status==1?"Yes":"No"}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div><h6 style="color: #673ab7">Shelf Stock Limits:</h6>
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <tr>
                                                        <th width="25%">Branch Name</th>
                                                        <th width="20%">Stock Location</th>
                                                        <th width="19%">Saleman</th>
                                                        <th width="18%" class="text-center">Max Qty</th>
                                                        <th width="18%" class="text-center">Min Qty</th>
                                                    </tr>
                                                    <tbody>
                                                    @foreach($data['branch'] as $key=>$branch)
                                                        @foreach($pb['barcode_dtl'] as $b_dtl)
                                                            @if($b_dtl['branch_id'] == $branch->branch_id)
                                                                @php
                                                                    $product_barcode_shelf_stock_location =  isset($b_dtl['product_barcode_shelf_stock_location'])?$b_dtl['product_barcode_shelf_stock_location']:'';
                                                                    $user_id =  isset($b_dtl['product_barcode_shelf_stock_sales_man'])?$b_dtl['user']['name']:"";
                                                                    $product_barcode_stock_limit_max_qty =  $b_dtl['product_barcode_stock_limit_max_qty'];
                                                                    $product_barcode_stock_limit_min_qty =  $b_dtl['product_barcode_stock_limit_min_qty'];
                                                                @endphp
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        @php
                                                            $display_location = \App\Models\ViewInveDisplayLocation::where('display_location_id',$product_barcode_shelf_stock_location)->first(['display_location_name_string']);
                                                        @endphp
                                                        <tr>
                                                            <td>{{$branch->branch_name}}</td>
                                                            <td>{{$display_location['display_location_name_string']}}</td>
                                                            <td>{{$user_id}}</td>
                                                            <td class="text-center">{{$product_barcode_stock_limit_max_qty}}</td>
                                                            <td class="text-center">{{$product_barcode_stock_limit_min_qty}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane tax" id="tax{{ $pb['product_barcode_id'] }}">
                                    <div class="form-group-block">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <tr>
                                                        <th width="60%">Branch Name </th>
                                                        <th width="20%" class="text-center">Tax Value </th>
                                                        <th width="20%" class="text-center">Apply Tax </th>
                                                    </tr>
                                                    <tbody>
                                                    @foreach($data['branch'] as $key=>$branch)
                                                        @foreach($pb['barcode_dtl'] as $b_dtl)
                                                            @php
                                                                $product_barcode_tax_value =  "";
                                                                $product_barcode_tax_apply =  "";
                                                            @endphp
                                                            @if($b_dtl['branch_id'] == $branch->branch_id)
                                                                @php
                                                                    $product_barcode_tax_value =  $b_dtl['product_barcode_tax_value'];
                                                                    $product_barcode_tax_apply =  isset($b_dtl['product_barcode_tax_apply'])?$b_dtl['product_barcode_tax_apply']:0;
                                                                @endphp
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        @php
                                                            $tax_apply =  isset($product_barcode_tax_apply)?$product_barcode_tax_apply:0;
                                                        @endphp
                                                        <tr>
                                                            <td>{{$branch->branch_name}}</td>
                                                            <td class="text-center">{{isset($product_barcode_tax_value)?$product_barcode_tax_value:""}}</td>
                                                            <td class="text-center">{{$tax_apply==1?"Yes":"No"}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane purchase_foc" id="purchase_foc{{ $pb['product_barcode_id'] }}">
                                    FOC
                                </div>
                            </div>
                            <hr style="border-top: 2px solid #607d8b;">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script>
        $(document).ready(function(){
            $("#product_control_group").change(function(){
                var group =  $(this).val();
                if(group) {
                    $.ajax(
                        {
                            type:'GET',
                            url:'/product/form-itemtype-data/'+ group,
                            success: function(response, data)
                            {
                                $('#product_item_type').empty();
                                if(response.product_type_group_id != undefined){
                                    $('#product_item_type').append('<option value="'+response.product_type_group_id+'">'+response.product_type_group_name+'</option>');
                                }
                            }
                        });
                }
            });
        });

        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'product_life_country_name',
                'fieldClass':'product_life_country_name field_readonly',
                'message':'Select Country Name',
                'require':true,
                'readonly':true,
                'type':'select',
                'convertType':'input',
                'getVal':'text',
                'defaultValue':true,
            },
            {
                'id':'period_type',
                'fieldClass':'period_type field_readonly',
                'message':'Select Period Type',
                'require':true,
                'readonly':true,
                'type':'select',
                'convertType':'input',
                'getVal':'text',
                'defaultValue':true,
            },
            {
                'id':'period',
                'fieldClass':'period large_no validNumber validOnlyFloatNumber',
                'message':'Enter period',
                'require':true
            },
        ]
        var arr_hidden_field = ['country'];
        $('.erp_form__grid_header #product_life_country_name').on('change', function() {
            $('.erp_form__grid_header>tr>th:first-child>div>input#country').val($(this).val());
        })
    </script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/table-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/data-repeated.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/product-edit.js') }}" type="text/javascript"></script>

    <script>
        $('.validNumber').keypress(validateNumber);
        $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
        // .create_print_shelf_barcode
        $(document).on('click','.create_print_barcode',function(){
            var data_id = $(this).attr('data-id');
            var product_name = $('#product_name').val();
            var product_arabic_name = $('#product_arabic_name').val();
            var barcode = $(this).parents('.barcode').find('.barcode_repeat_b').val();
            if(data_id != 1){
                if(data_id != 2){
                    toastr.error("Something wrong...");
                    return false;
                }
            }
            if(data_id == 1 || data_id == 2){
                if(product_name.length == 0){
                    toastr.error("Required field product name");
                    return false;
                }
                if(barcode.length == 0){
                    toastr.error("Required field barcode");
                    return false;
                }
            }
            if(data_id == 2){
                if(product_arabic_name.length == 0){
                    toastr.error("Required field product arabic name");
                    return false;
                }
            }
            var formData = {
                data_id : data_id,
                product_name : product_name,
                product_arabic_name : product_arabic_name,
                barcode : barcode,
                rate : $(this).parents('.barcode').find('.label_print_price').val(),
                qty : $(this).parents('.barcode').find('.label_print_total').val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '{{action('Purchase\ProductController@BarcodeTagPrintGenerate')}}',
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    console.log(response)
                    if(response['status'] == 'success'){
                        toastr.success("Barcode label ready..");
                        window.open(response['data']['url'], "newbarcode");
                    }else{

                    }
                }
            });
        });

        //------tax function-----
        $(document).on('click','.tax_status',function(){
            var val = $(this).is(":checked");
            if(val == true){
                $(this).parents('tr').find('.tax_value').attr('required',true);
            }else{
                $(this).parents('tr').find('.tax_value').attr('required',false);
            }
        });


    </script>
@endsection
@endpermission
@endif
