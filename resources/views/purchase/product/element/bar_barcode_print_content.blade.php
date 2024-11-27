
<div class="row">
    <label class="col-lg-3 erp-col-form-label">Barcode Print:</label>
    <div class="col-lg-9">
        <div class="form-group-block  input-group-sm">
            <div class="input-group input-group-sm">
                <div class="input-group-prepend"><span class="input-group-text"><i class="la la-money"></i></span></div>
                <input type="text" value="{{number_format($current_bra_sale_rate,3)}}" class="form-control medium_no label_print_price validNumber validOnlyFloatNumber" >
                <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-stopwatch"></i>
                                                </span>
                </div>
                <input type="number" value="1" class="form-control label_print_total validNumber ">
                <div class="input-group-append" >
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" style="border: 1px solid #e9ebf1;padding: 6.75px;">
                            <i class="la la-barcode"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-item create_print_barcode" data-id="1">Barcode Label</div>
                            <div class="dropdown-item create_print_barcode" data-id="2">Shelf Label</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
