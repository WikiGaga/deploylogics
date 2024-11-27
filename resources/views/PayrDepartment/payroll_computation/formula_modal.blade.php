<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{isset($data['salary_head'])?$data['salary_head']:""}}: <small> Formula Calculate</small></h5>
    <button type="button" class="close" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row ">
        <div class="col-lg-3" style="color: #303031;
    font-size: 15px;
    border: 1px solid #ffb822;">
            <div class="salary_head_list" style="user-select: none;">
                @foreach ($data['tags'] as $tag)
                    <div>[{{$tag->allowance_deduction_tag_name}}]</div>    
                @endforeach
            </div>
           
        </div>
        <div class="col-lg-6">
            <textarea rows="5" id="formula" style="font-size: 16px;" class="form-control erp-form-control-sm">{{isset($data['value_input'])?$data['value_input']:""}}</textarea>
        </div>
        <div class="col-lg-3" style="color: #303031;
    font-size: 15px;
    border: 1px solid #ffb822;">
            <div class="salary_operators" style="user-select: none;">
                <div>+</div>
                <div>-</div>
                <div>*</div>
                <div>/</div>
                <div>%</div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" id="calculate_generate">Generate</button>
</div>
