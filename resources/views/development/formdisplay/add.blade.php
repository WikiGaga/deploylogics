@extends('layouts.template')
@section('title', 'Form Display Setting')

@section('pageCSS')
@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @php
                    $page_data = [
                        'page_title'=>'Form Display Setting',
                        'form_type'=> ''
                    ]
                @endphp
                @include('elements.page_header',['page_data'=>$page_data])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <form id="display_form" class="kt-form" method="post" action="{{action('Development\FormDisplayController@update')}}">
                    @csrf
                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">ID:</label>
                            <div class="col-lg-2">
                                <input type="text" name="form_cases_id" class="form-control form-control-sm">
                            </div>
                            <div class="col-lg-4"></div>
                            <label class="col-lg-2 col-form-label">Date:</label>
                            <div class="col-lg-2">
                                <div class="input-group date">
									<input type="text" class="form-control" readonly value="05/20/2017" name="menu_form_display_apply_at" id="kt_datepicker_3" />
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="la la-calendar"></i>
										</span>
									</div>
								</div>
                            </div>
                        </div>{{-- end row--}}
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Select Form:</label>
                                    <div class="col-lg-3" >
                                        <select class="form-control kt-select2 form-control-sm" id="menu_form_display_name" name="menu_flow_criteria_name">
                                             <option value="">Select</option>
                                             @foreach($data['formcases'] as $menue)
                                            <option value="{{ $menue->form_cases_table_name }}">{{ $menue->form_cases_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- end row --}}
                    </div>

                    <div class="form-group row">
                                    <div class="col-lg-12">
                                        <table id="Form_display" class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                                            <thead>
                                            <tr>
                                                <th width="20%">Column Name</th>
                                                <th width="1%">Active</th>
                                                <th width="20%">Heading</th>
                                                <th width="5%">Sort By</th>
                                                <th width="20%">Order By Asc/Desc</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>

                                        </table>
                                    </div>
                                </div>{{-- end row--}}

                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <button type="submit" class="btn btn-success">Save</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>

     <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/development-create.js') }}" type="text/javascript"></script>
<script>
   $(document).ready(function(){
  $("#menu_form_display_name").change(function(){
    var formtble =  $(this).val();
    if(formtble) {
    $.ajax(
        {
            type:'GET',
            url:'/formdisplay/form-display-data/'+ formtble,
            success: function(response,  data)
            {
                console.log(response);
                    var tr = "";
                    for(var i=0;response['column'].length>i;i++){
                        var heading = '';
                        var checked = '';
                        var orderNo  = '';
                        var sortChecked  = '';

                        tr += '<tr>'+
                                    '<td>'+
                                        '<input  type="text" value="'+response['column'][i]+'" class="form-control form-control-sm" id="form_display_field_name_'+[i]+'" readonly>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group  row">'+
                                            '<div class="col-lg-6"> '+
                                                '<span >'+
                                                '<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">';
                                                for(var r=0;response['dbcolumn'].length>r;r++){
                                                    checked = (response["dbcolumn"][r]==response["column"][i].toLowerCase())?"checked":""; 
                                                    sortChecked = (response["orderby"][r]==response["column"][i].toLowerCase())?"checked":"";
                                                    if(checked =='checked'){
                                                        orderNo = r + 1;
                                                        heading = response["dbheading"][r];
                                                        break;
                                                    }
                                                }
                                                for(var x=0;response['orderby'].length>x;x++){
                                                   
                                                    if(response["orderby"][x] == response["column"][i].toLowerCase() )
                                                    {  console.log(response["column"][i]);  }
                                                    
                                                }
                                        var disabled  =  (heading=="")?"disabled":"";
                                        tr += '<input type="checkbox" class="state" name="formDisplay['+i+'][column]" value="'+response['column'][i]+'" '+checked+'>';
                                        tr +='<span></span>'+
                                                '</label>'+
                                                '</span>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="text"  class="form-control form-control-sm" name="formDisplay['+i+'][heading]" value="'+heading+'" '+disabled+'>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group  row">'+
                                            '<div class="col-lg-6"> '+
                                                '<span >'+
                                                    '<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">'+
                                                    '<input type="checkbox" value="'+response['column'][i]+'" name="formDisplay['+i+'][sort]" '+disabled+sortChecked+'>'+
												    '<span></span>'+
												    '</label>'+
                                                '<span>'+
                                            '</div>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="text"  class="form-control form-control-sm validNo orderBy" value="'+orderNo+'" name="formDisplay['+i+'][orderby]" '+disabled+'>'+
                                    '</td>'+
                                '</tr>';
                    }
                    $('#Form_display>tbody').html(tr);
                    FieldsToggle();
                    $('.validNo').keypress(validateNumber);
                    numberOrderBy();
                   
            }
        });
    }else{
        $('#Form_display>tbody').html("")

    }
  });
});

function FieldsToggle(){
    $('.state').click(function(){
        var val = $(this).is(":checked");
        if(val == true)
        {
            $(this).parents('tr').find('input').attr('disabled',false);
        }else
        {
            $(this).parents('tr').find('input').attr('disabled',true);
            $(this).attr('disabled',false);
        }
        numberOrderBy();
    });
}
function numberOrderBy(){
    var n = 0;
    var v = '';
    $( "#Form_display>tbody>tr" ).each(function( index ) {
        v = $(this).find('td:eq(1) input[type="checkbox"]:checked').length;
        if(v == 1){
            n += 1;
        }
    });
    var OrderByArray = [];
    $(".orderBy").each(function(){
        if($(this).val()>0){
            OrderByArray.push($(this).val());
        }
    });
    $('.orderBy').keyup(function(event){
        n+=1;
        var this_ = $(this);
        var value = $(this).val(); // 1
        console.log(n);
        console.log(OrderByArray);
        console.log(value);
        
        if(value == 0 || value > n){
            this_.val("");
        }
        for(var i=0; OrderByArray.length >i; i++)
        {
            if(value==OrderByArray[i])
                {
                    this_.val("");
                }
        }
        n=n-1;
    });
    
}

</script>

@endsection

