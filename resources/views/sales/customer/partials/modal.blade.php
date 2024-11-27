<!-- Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="modal_create_customer">
            <div class="row form-group-block">
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Name: <span class="required">*</span></label>
                        <div class="col-lg-12">
                            <div class="input-group date">
                                <input type="text" name="customer_name" class="moveIndex form-control erp-form-control-sm" value="" placeholder="Customer Name" id="modal_customer_name" autofocus="" autocomplete="off" aria-invalid="false">
                                <input type="hidden" name="customer_type" value="10321722152050">
                                <input type="hidden" name="customer_entry_status" value="1">
                                <input type="hidden" name="customer_branch_id[]" value="{{ auth()->user()->branch->branch_id }}">
                                <input type="hidden" name="is_modal_entry" value="1">
                            </div>
                        </div>
                    </div>
                </div>         
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Arabic Name: <span class="required">*</span></label>
                        <div class="col-lg-12">
                            <div class="input-group date">
                                <input type="text" name="customer_local_name" class="moveIndex form-control erp-form-control-sm" placeholder="Customer Arabic Name"  value="" id="modal_customer_arabic_name" autocomplete="off" aria-invalid="false">
                            </div>
                        </div>
                    </div>
                </div>         
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Phone No. / Mobile No.: <span class="required">*</span></label>
                        <div class="col-lg-12">
                            <div class="input-group date">
                                <input type="text" name="customer_phone_1" class="moveIndex form-control erp-form-control-sm" placeholder="Customer Mobile No." id="modal_customer_phone" value="" autocomplete="off" aria-invalid="false">
                            </div>
                        </div>
                    </div>
                </div>         
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">City: <span class="required">*</span></label>
                        <div class="col-lg-12">
                            <div class="erp-select2 form-group">
                                <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="modal_customer_city" name="city_id" aria-hidden="true">
                                    <option value="0">Select</option>      
                                    @foreach($data['cities'] as $city)
                                        <option value="{{$city->city_id}}">{{$city->city_name}}</option>
                                    @endforeach                                                              
                                </select>
                            </div>
                        </div>
                    </div>
                </div>         
                <div class="col-lg-12">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Area: <span class="required">*</span></label>
                        <div class="col-lg-12">
                            <div class="erp-select2 form-group">
                                <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="modal_customer_area" name="customer_area_id" aria-hidden="true">
                                    <option value="0">Select</option>                                                                    
                                </select>
                            </div>
                        </div>
                    </div>
                </div>         
                <div class="col-lg-12 d-none">
                    <div class="row">
                        <label class="col-lg-12 erp-col-form-label">Address:</label>
                        <div class="col-lg-12">
                            <div class="input-group date">
                                <input type="text" name="customer_address" class="moveIndex form-control erp-form-control-sm" placeholder="Enter Customer Address" value="" id="modal_customer_address" autocomplete="off" aria-invalid="false">
                            </div>
                        </div>
                    </div>
                </div>         
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modal_add_customer">Save</button>
      </div>
    </div>
  </div>
</div>