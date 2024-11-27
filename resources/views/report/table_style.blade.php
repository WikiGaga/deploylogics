<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Table Style
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div><b>Table Header</b></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="erp-col-form-label">Background Color:</label>
                                <div class="input-group">
                                    <input type="color" name="table_header_bg_color" class="form-control erp-form-control-sm" value="#0033ff">
                                    <div class="input-group-append">
                                        <span class="input-group-text erp-form-control-sm">
                                            <label class="kt-checkbox kt-checkbox--single">
                                                <input name="table_header_bg_color" type="checkbox" checked>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Font Size:</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="table_header_font_size" name="table_header_font_size">
                                        {{-- @foreach($data['item'] as $tag)
                                             <option value="{{$tag->tags_id}}">{{$tag->tags_name}}</option>
                                         @endforeach--}}
                                        <option value="0">Select</option>
                                        <option value="8">8px</option>
                                        <option value="9">9px</option>
                                        <option value="10">10px</option>
                                        <option value="11">11px</option>
                                        <option value="12">12px</option>
                                        <option value="14">14px</option>
                                        <option value="16">16px</option>
                                        <option value="18">18px</option>
                                        <option value="20">20px</option>
                                        <option value="24">24px</option>
                                        <option value="28">28px</option>
                                        <option value="30">30px</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Color:</label>
                                <div class="input-group">
                                    <input type="color" class="form-control erp-form-control-sm" name="table_header_color" value="#ff0000">
                                    <div class="input-group-append">
                                        <span class="input-group-text erp-form-control-sm">
                                            <label class="kt-checkbox kt-checkbox--single">
                                                <input name="table_header_color" type="checkbox" checked>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div><b>Table Rows Background Color</b></div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Odd Rows:</label>
                                <div class="input-group">
                                    <input type="color" class="form-control erp-form-control-sm" name="table_row_odd_bg_color" value="#e5e5e5">
                                    <div class="input-group-append">
                                        <span class="input-group-text erp-form-control-sm">
                                            <label class="kt-checkbox kt-checkbox--single">
                                                <input name="table_row_odd_bg_color" type="checkbox" checked>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Even Rows:</label>
                                <div class="input-group">
                                    <input type="color" class="form-control erp-form-control-sm" name="table_row_even_bg_color" value="#000000">
                                    <div class="input-group-append">
                                        <span class="input-group-text erp-form-control-sm">
                                            <label class="kt-checkbox kt-checkbox--single">
                                                <input name="table_row_even_bg_color" type="checkbox" checked>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div><b>Table Body</b></div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Font Size:</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="table_body_font_size" name="table_body_font_size">
                                        {{-- @foreach($data['item'] as $tag)
                                             <option value="{{$tag->tags_id}}">{{$tag->tags_name}}</option>
                                         @endforeach--}}
                                        <option value="0">Select</option>
                                        <option value="8">8px</option>
                                        <option value="9">9px</option>
                                        <option value="10">10px</option>
                                        <option value="11">11px</option>
                                        <option value="12">12px</option>
                                        <option value="14">14px</option>
                                        <option value="16">16px</option>
                                        <option value="18">18px</option>
                                        <option value="20">20px</option>
                                        <option value="24">24px</option>
                                        <option value="28">28px</option>
                                        <option value="30">30px</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Color:</label>
                                <div class="input-group">
                                    <input type="color" class="form-control erp-form-control-sm" name="table_body_color" value="#8cff00">
                                    <div class="input-group-append">
                                        <span class="input-group-text erp-form-control-sm">
                                            <label class="kt-checkbox kt-checkbox--single">
                                                <input name="table_body_color" type="checkbox" checked>
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
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div><b>Column Align</b></div>
                        <div id="AddColumnAlignList">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
