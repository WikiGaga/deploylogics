@extends('layouts.layout')
@section('title', 'Employee')
@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $today =  date('d-m-Y');
        if($case == 'new'){
            $code =  $data['employee_code'];
            $menu_id = $data['menu_id'];
        }
        if($case == 'edit'){
            $current = $data['current'];
            $id = $current->employee_id;
            $menu_id = $data['menu_id'];
            $code = $current->employee_code;
            $name = $current->employee_name;
            $arabic_name = $current->employee_arabic_name;
            $fh_name = $current->employee_fh_name;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$current->employee_date))));
            $status = $current->employee_entry_status;
            $image = $current->employee_img;
        }

        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="employee_form" class="kt-form" method="post" action="{{ action('PayrDepartment\EmployeeController@store',isset($id)?$id:'') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="form_id" value='{{isset($id)?$id:""}}'>
        <input type="hidden" id="form_type" value="{{$form_type}}">
        <input type="hidden" id="menu_id" value="{{$menu_id}}">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="erp-page--title">
                                {{isset($code)?$code:""}}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Employee Name:<span class="required">* </span></label>
                                <div class="col-lg-6">
                                    <input type="text" maxlength="100" name="employee_name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Employee Arabic Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" maxlength="100" name="employee_arabic_name" value="{{isset($arabic_name)?$arabic_name:""}}" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Father/Husband Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" maxlength="100" name="employee_fh_name" value="{{isset($fh_name)?$fh_name:""}}" class="form-control erp-form-control-sm">
                                </div>
                            </div>
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Status:</label>
                                <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $entry_status = isset($status)?$status:""; @endphp
                                                <input type="checkbox" name="employee_entry_status" {{$entry_status==1?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="employee_entry_status" checked>
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Image:</label>
                                <div class="col-lg-6">
                                    @php
                                        $image_url = isset($image)?$image:"";
                                    @endphp
                                    <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                        @if($image_url)
                                            <div class="kt-avatar__holder" style="background-image: url({{$image_url}})"></div>
                                        @else
                                            <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                        @endif
                                        <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change image">
                                            <i class="fa fa-pen"></i>
                                            <input type="file" name="employee_img" accept="image/png, image/jpg, image/jpeg">
                                        </label>
                                        <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel image">
                                            <i class="fa fa-times"></i>
                                        </span>
                                    </div>
                                    <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                </div>
                            </div>
                        </div>
                    </div>{{-- name detail --}}
                    <ul class="employee-tab-nav nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#general_information" role="tab">General Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#contact_detail" role="tab">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#employment" role="tab">Employment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#experience" role="tab">Experience</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#educational_detail" role="tab">Educational</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#bank_detail" role="tab">Bank Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#insurance" role="tab">Insurance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#termination" role="tab">Termination</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#salary_details" role="tab">Salary</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link duty_leave" data-toggle="tab" href="#duty_leave" role="tab">Duty & Leave</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="general_information" role="tabpanel">
                            @include('PayrDepartment.employee.tab_general_info')
                        </div>{{-- general info--}}
                        <div class="tab-pane" id="contact_detail" role="tabpanel">
                            @include('PayrDepartment.employee.tab_contact_detail')
                        </div>{{-- Contact detail--}}
                        <div class="tab-pane" id="employment" role="tabpanel">
                            @include('PayrDepartment.employee.tab_employment')
                        </div>{{-- Employment--}}
                        <div class="tab-pane" id="experience" role="tabpanel">
                            @include('PayrDepartment.employee.tab_experience')
                        </div>{{-- Experience --}}
                        <div class="tab-pane" id="bank_detail" role="tabpanel">
                            @include('PayrDepartment.employee.tab_bank_detail')
                        </div>{{-- Bank Details --}}
                        <div class="tab-pane" id="termination" role="tabpanel">
                            @include('PayrDepartment.employee.tab_termination')
                        </div>{{-- Termination --}}
                        <div class="tab-pane" id="salary_details" role="tabpanel">
                        </div>{{-- Salary Details--}}
                        <div class="tab-pane" id="educational_detail" role="tabpanel">
                            @include('PayrDepartment.employee.tab_educational')
                        </div>{{-- Educational Detail--}}
                        <div class="tab-pane" id="duty_leave" role="tabpanel">
                            <div id="leave_policies"></div>
                        </div>{{-- Duty & Leave--}}
                        <div class="tab-pane" id="insurance" role="tabpanel">
                            @include('PayrDepartment.employee.tab_insurance')
                        </div>{{-- Insurance--}}

                    </div>
                </div>
            </div>
        </div>
    </form>
    @endpermission
@endsection

@section('pageJS')
    <script src="{{ asset('assets/js/pages/crud/file-upload/ktavatar.js') }}" type="text/javascript"></script>
@endsection
@section('customJS')
    <script src="{{ asset('assets/js/pages/crud/file-upload/dropzonejs.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/setting/employee.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/setting/employee-repeater.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var hiddenFieldsFormName = 'employeeForm';
        var formcase = '{{$case}}';
    </script>
    <script src="{{ asset('js/pages/js/erp-form-fields-hide.js') }}" type="text/javascript"></script>
    <script>
        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }

        $('#kt_datepicker_3, #kt_datepicker_3_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows,
            endDate: '+0d',
        });
        $('.kt_datepicker, .kt_datepicker_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows,
        });
        $(document).on('change','.document_files',function(event){
            var thix = $(this);
            var fi = event.target;
            thix.parents('.document_files_block').find('.document_files_list').html('');
            if (fi.files.length > 0) {
                for (var i = 0; i <= fi.files.length - 1; i++) {
                    var fsize = fi.files.item(i).size;
                    var name = '<b>File Name:</b> ' + fi.files.item(i).name + '<br/>';
                    var size = '<b>File Size:</b> ' + Math.round((fsize / 1024)) + '<br/>';
                    var type = '<b>File Type:</b> ' + fi.files.item(i).type + '<br/>';
                    thix.parents('.document_files_block').find('.document_files_list').append(name);
                }
            }
        });
        $(document).on('change','.religion,.designation,.grade',function(e){
            e.preventDefault();
            var formData = {
                religion: $('select[name="religion"]').val(),
                designation: $('select[name="designation"]').val(),
                grade: $('select[name="grade"]').val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '/employee/getLeavePolicy',
                dataType	: 'json',
                data        : formData,
                success: function(response,data) {
                    var policy = '';
                    for(var i=0;i < response.length; i++){
                        policy += '<div class="col-lg-6"><div class="row">' +
                            '<label class="col-lg-6 erp-col-form-label">'+response[i]['name']+'</label>' +
                            '<div class="col-lg-6"><span class="mt-1">\n' +
                            '<label class="kt-checkbox kt-checkbox--primary">\n' +
                                '<input type="checkbox" value="'+response[i]['id']+'" name="policy[]">\n' +
                                '<span></span>\n' +
                            '</label>\n' +
                            '</span></div></div></div>';

                    }
                    var data = '<div class="row">'+policy+'</div>'
                    $('#leave_policies').html(data);
                }
            });
        });
//city current
            $(document).on('change','.country',function(e){
            var thix = $(this);
            var countryCurrent = $(this).val();
            if(countryCurrent) {
                $.ajax({
                    type:'GET',
                    url:'/employee/getCurrentCity'+'/'+countryCurrent,
                    success: function(response, data){
                        console.log(response);
                        if(response)
                        {
                           var c = thix.parents('.country_city_block').find('.city')
                           thix.parents('.country_city_block').find('.city').empty();
                           thix.parents('.country_city_block').find('.city').append('<option value="0">Select</option>');
                            $.each(response,function(key,value){
                                c.append('<option value="'+value.city_id+'">'+value.city_name+'</option>');
                            });
                        }
                    }
                });
            }
        });
       //city permanent
        // $("#country").change(function(e){
        //     e.preventDefault();
        //     var country = $("select[name='country']").val();
        //     if(country) {
        //         $.ajax({
        //             type:'GET',
        //             url:'/employee/getCity'+'/'+country,
        //             success: function(response, data){
        //                 if(data)
        //                 {
        //                     $('#city').empty();
        //                     $('#city').append('<option>Select</option>');
        //                     $.each(response,function(key,value){

        //                         $('#city').append('<option value="'+value.city_id+'">'+value.city_name+'</option>');

        //                     });
        //                 }
        //             }

        //         });
        //     }
        // });

        $(document).on('click','#upload_documents',function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                form_id : $('#form_id').val(),
                form_type : $('#form_type').val(),
                menu_id : $('#menu_id').val(),
                form_code : $('.erp-page--title').text().trim(),
            }
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
        });

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_employee.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
