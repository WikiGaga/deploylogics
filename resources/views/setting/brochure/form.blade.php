@extends('layouts.layout')
@section('title', 'Brochure')

@section('pageCSS')
<style>

</style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            //$code = $data['document_code'];
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $id = $data['current']->brochure_id;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->brochure_date))));
            $brochure_name = $data['current']->brochure_name;
            $header_heading = $data['current']->header_heading;
            $start_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->start_date))));;
            $end_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->end_date))));;
            $background_type = $data['current']->background_type;
            $branch_logo = $data['current']->branch_logo;
            $bg_color = $data['current']->background_color;
            $background_image = $data['current']->background_image;
            $brochures_dtl = $data['current']->brochures_dtl;
            $selectedBranches = explode("," , $data['current']->branches);
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="brochure_form" class="kt-form" method="post" action="{{ action('Setting\BrochureController@store',isset($id)?$id:"") }}">
    <input type="hidden" value='{{$form_type}}' id="form_type">
    <input type="hidden" value='{{isset($id)?$id:""}}' id="form_id">
    @csrf
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{--isset($code)?$code:""--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="brochure_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
                                    <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Brochure Name:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" value="{{isset($brochure_name)?$brochure_name:""}}" id="brochure_name" name="brochure_name" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Header Heading:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" value="{{isset($header_heading)?$header_heading:""}}" id="header_heading" name="header_heading" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Start Date: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="start_date" class="form-control erp-form-control-sm moveIndex c-date-p" value="{{isset($start_date)?$start_date: date('d-m-Y') }}" id="kt_datepicker_3" autofocus/>
                                    <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">End Date: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="end_date" class="form-control erp-form-control-sm moveIndex c-date-p" value="{{isset($end_date)?$end_date: date('d-m-Y') }}" id="kt_datepicker_3" autofocus/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Background Type: <span class="required">*</span></label>
                            <div class="col-lg-6">
                            <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 bg_type" id="bg_type" name="bg_type" data-minimum-results-for-search="Infinity">
                                        @if($case == 'new')
                                            <option value="0" selected>Select</option>
                                        @else 
                                            <option value="0">Select</option>
                                        @endif
                                        <option value="1" @if(isset($background_type) && $background_type == '1') selected @endif>Color</option>
                                        <option value="2" @if(isset($background_type) && $background_type == '2') selected @endif>Image</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-12">
                        <div class="row">
                            <label class="col-lg-2 erp-col-form-label">Branches: <span class="required">*</span></label>
                            <div class="col-lg-10">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 bro_branches" multiple id="bro_branches" name="bro_branches[]">
                                        <option value="0">Select</option>
                                        @foreach($data['branches'] as $bran)

                                            <option value="{{ $bran->branch_id }}" @if(in_array( $bran->branch_id , $selectedBranches ?? [])) selected @endif>{{ $bran->branch_short_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block my-3">
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Branch Logo: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                @php
                                    $branch_profile = isset($branch_logo)?'/uploads/'.$branch_logo:"";
                                @endphp
                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                    @if($branch_profile)
                                        <div class="kt-avatar__holder" style="background-image: url({{$branch_profile}})"></div>
                                    @else
                                        <div class="kt-avatar__holder" style="background-image: url(/assets/media/project-logos/7.png)"></div>
                                    @endif
                                    <label class="kt-avatar__upload" for="upload__branch_logo" data-toggle="kt-tooltip" title="Upload Branch Logo" data-original-title="Change Branch Logo">
                                        <i class="fa fa-pen"></i>
                                    </label>
                                    <input type="file" id="upload__branch_logo" class="d-none" value="{{ $branch_logo ?? '' }}" name="branch_profile" accept="image/png, image/jpg, image/jpeg">
                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="Cancel Branch Logo" data-original-title="Cancel Branch Logo">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 bg_color_container @if(isset($background_type) && $background_type == '1') d-block @else d-none @endif">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Background Color:<span class="required"></span></label>
                            <div class="col-lg-6">
                                <input type="color" value="{{isset($bg_color)?$bg_color:""}}" id="bg_color" name="bg_color" class="bg_color form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 bg_image_container @if(isset($background_type) && $background_type == '2') d-block @else d-none @endif">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Background Image:</label>
                            <div class="col-lg-8">
                                @php
                                    $background_image = isset($background_image)?'/uploads/'.$background_image:"";
                                @endphp
                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_2">
                                    @if($background_image)
                                        <div class="kt-avatar__holder" style="background-image: url({{$background_image}})"></div>
                                    @else
                                        <div class="kt-avatar__holder" style="background-image: url(/assets/media/project-logos/7.png)"></div>
                                    @endif
                                    <label class="kt-avatar__upload" for="upload__backgound" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                        <i class="fa fa-pen"></i>
                                    </label>
                                    <input type="file" id="upload__backgound" class="d-none" value="{{ $background_image ?? '' }}" name="background_image" accept="image/png, image/jpg, image/jpeg">
                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="data_entry_header">
                            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                    <i class="flaticon-more" style="color: #666666;"></i>
                                </button>
                                @php
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Notes','Qty',
                                                'Rate','Amount','Disc%','Disc Amt','Bg Color','Product Image',
                                                ];
                                @endphp
                                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                    @foreach($headings as $key=>$heading)
                                        <li >
                                            <label>
                                                <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="kt-user-page-setting" style="display: inline-block">
                                <button type="button" style="width: 30px;height: 30px;" title="Setting Save" data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon" id="pageUserSettingSave">
                                    <i class="la la-floppy-o"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block">
                    <div class="erp_form___block">
                        <div class="table-scroll form_input__block">
                            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                            <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                            <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                            <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">
                                            Barcode
                                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                <i class="la la-barcode"></i>
                                            </button>
                                        </div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Product Name</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">UOM</div>
                                        <div class="erp_form__grid_th_input">
                                            <select id="pd_uom" class="pd_uom form-control erp-form-control-sm">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th cope="col">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th cope="col">
                                        <div class="erp_form__grid_th_title">Notes</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="remarks"  type="text" class="form-control erp-form-control-sm tb_moveIndex">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" type="text" class="tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="amount" type="text" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Disc %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="dis_perc" type="text" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Disc Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="dis_amount" type="text" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">VAT %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="vat_perc" type="text" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">VAT Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="vat_amount" type="text" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Gross Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="gross_amount" readonly type="text" class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Action</div>
                                        <div class="erp_form__grid_th_btn">
                                            <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                <i class="la la-plus"></i>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="erp_form__grid_body">
                                @if(isset($brochures_dtl))
                                    @foreach($brochures_dtl as $dtl)
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][brochure_dtl_id]" data-id="brochure_dtl_id" value="{{$dtl->brochure_dtl_id}}" class="brochure_dtl_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->barcode->product->product_id)?$dtl->barcode->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->barcode->uom->uom_id)?$dtl->barcode->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->barcode->product->product_name)?$dtl->barcode->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][pd_uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->barcode->uom->uom_name)?$dtl->barcode->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][pd_packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="remarks" name="pd[{{$loop->iteration}}][remarks]" value="{{isset($dtl->brochure_dtl_remarks)?$dtl->brochure_dtl_remarks:""}}" class="form-control erp-form-control-sm tb_moveIndex"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity"  value="{{$dtl->brochure_dtl_qty}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>

                                            <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate"  value="{{number_format($dtl->brochure_dtl_rate,3)}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount"  value="{{number_format($dtl->brochure_dtl_amount,3)}}" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc"  value="{{number_format($dtl->brochure_dtl_disc_percent,2)}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount"  value="{{number_format($dtl->brochure_dtl_disc_amount,3)}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc" value="{{number_format($dtl->brochure_dtl_vat_perc,3)}}" title="{{number_format($dtl->brochure_dtl_vat_perc,3)}}" class="tblGridCal_vat_perc tblGridCal_parent_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_amount]" data-id="vat_amount" value="{{number_format($dtl->brochure_dtl_vat_amount,3)}}" title="{{number_format($dtl->brochure_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount tblGridCal_parent_vat_amount form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->brochure_dtl_gross_amount,3)}}" class="tblGridCal_gross_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                                <tbody class="erp_form__grid_body_total">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_grid_qty">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td class="total_grid_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td>
                                    </td>
                                    <td></td>
                                    <td class="total_grid_vat_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td ></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/setting/brochure.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    
    <script>
        var formcase = '{{$case}}';
        var data_po_selected = "";
    </script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
                'message':'Enter Barcode',
                'require':true,
                'readonly':true
                //  'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'pd_uom',
                'fieldClass':'pd_uom field_readonly',
                'type':'select'
            },
            {
                'id':'pd_packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'remarks',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc tb_moveIndex validNumber'
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount tb_moveIndex validNumber'
            },
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc tb_moveIndex validNumber'
            },
            {
                'id':'vat_amount',
                'fieldClass':'tblGridCal_vat_amount tb_moveIndex validNumber'
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            }
        ];
        var  arr_hidden_field = ['product_id','product_barcode_id','uom_id'];

        $('select.bg_type').on('change' , function(e){
            var thix = $(this).val();
            if(thix == '1'){
                $('.bg_color_container').removeClass('d-none');
                $('.bg_image_container').removeClass('d-block').addClass('d-none');
            }else if(thix == '2'){
                $('.bg_color_container').removeClass('d-block').addClass('d-none');
                $('.bg_image_container').removeClass('d-none');
            }else{
                $('.bg_color_container').removeClass('d-block').addClass('d-none');
                $('.bg_image_container').removeClass('d-block').addClass('d-none');
            }
        });
    </script>
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
