@extends('layouts.template')
@section('title', 'Badges')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){
            $id = $data['current']->dash_widget_id;
            $widget_name = $data['current']->dash_widget_name;
            $badgeDtls = isset($data['current']->badgeDtl)?$data['current']->badgeDtl:[];
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="dashboard_badges_form" class="kt-form" method="post" action="{{action('Dashboard\DashboardStudioBadges@store',isset($id)?$id:'')}}" enctype="multipart/form-data">
     @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <label class="col-lg-2 erp-col-form-label">Widget Name:<span class="required">* </span></label>
                            <div class="col-lg-4">
                                <input type="text" name="dash_widget_name" maxlength="100" value="{{isset($widget_name)?$widget_name:''}}" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                        <hr>
                        <div id="widget_badges">
                            <div data-repeater-list="badges">
                                @php $totalBadges =  0; @endphp
                                @if($case == 'edit')
                                    @if(isset($badgeDtls))
                                        @php
                                        $totalBadges =  count($badgeDtls);
                                        @endphp
                                        @foreach($badgeDtls as $key=>$Dtls)
                                            <div data-repeater-item class="widget_badges">
                                                <div class="form-group-block row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Badge Name:<span class="required">* </span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" name="dash_widget_badge_name" maxlength="100" value="{{isset($Dtls->dash_widget_badge_name)?$Dtls->dash_widget_badge_name:''}}" class="form-control erp-form-control-sm badge_name" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Badge Case:<span class="required">* </span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" name="dash_widget_case_name" maxlength="100" value="{{isset($Dtls->dash_widget_case_name)?$Dtls->dash_widget_case_name:''}}" class="form-control erp-form-control-sm badge_case_name">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Badge Text Color:</label>
                                                            <div class="col-lg-8">
                                                                <input type="text" name="dash_widget_badge_color" maxlength="100" value="{{isset($Dtls->dash_widget_badge_color)?$Dtls->dash_widget_badge_color:''}}" class="form-control erp-form-control-sm">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Badge Detail:</label>
                                                            <div class="col-lg-8">
                                                                <input type="text" name="dash_widget_badge_detail" maxlength="100" value="{{isset($Dtls->dash_widget_badge_detail)?$Dtls->dash_widget_badge_detail:''}}" class="form-control erp-form-control-sm">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Background Color:</label>
                                                            <div class="col-lg-8">
                                                                <input type="color" name="dash_widget_badge_bg_color" maxlength="100" class="form-control erp-form-control-sm badge_bg_color" value="{{isset($Dtls->dash_widget_badge_bg_color)?$Dtls->dash_widget_badge_bg_color:'#ffffff'}}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Background Image:</label>
                                                            <div class="col-lg-8 abc">
                                                                <select class="form-control kt-selectpicker badge_svg {{"badge_".$key}}" name="dash_widget_badge_svg" id="dash_widget_badge_svg">
                                                                        <option value='{{isset($Dtls->dash_widget_badge_svg)?$Dtls->dash_widget_badge_svg:''}}' data-content='{{isset($Dtls->dash_widget_badge_svg)?$Dtls->dash_widget_badge_svg:''}} {{isset($Dtls->dash_widget_badge_svg_name)?$Dtls->dash_widget_badge_svg_name:''}}' selected="selected">{{isset($Dtls->dash_widget_badge_svg_name)?$Dtls->dash_widget_badge_svg_name:''}}</option>
                                                                    @foreach($data['svg'] as $svg)
                                                                        <option value='{{$svg['svg_icon']}}' data-content='{{$svg['svg_icon'] }} {{$svg['svg_name']}}'>{{$svg['svg_name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Background Image Color:</label>
                                                            <div class="col-lg-8">
                                                                <input type="color" maxlength="100" name="dash_widget_badge_svg_color" value="{{isset($Dtls->dash_widget_badge_svg_color)?$Dtls->dash_widget_badge_svg_color:''}}"  class="form-control erp-form-control-sm bg_img_color" value="#000000">
                                                                <input type="hidden" maxlength="100" name="dash_widget_badge_svg_name" value="{{$Dtls->dash_widget_badge_svg_name}}"  class="form-control erp-form-control-sm bg_img_name {{"bg_img_name_".$key}}">
                                                                <input type="hidden" maxlength="100" value="{{isset($Dtls->dash_widget_badge_svg_color)?$Dtls->dash_widget_badge_svg_color:''}}" class="form-control erp-form-control-sm bg_img_color_hidden">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <div class="col-lg-8">
                                                                <label class="erp-col-form-label">Badge Bg Image:<span class="required">* </span></label>
                                                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                                                    @php $picture = isset($Dtls->dash_widget_badge_bg_img)?'/images/'.$Dtls->dash_widget_badge_bg_img:""; @endphp
                                                                    @if(!empty($picture))
                                                                        <div class="kt-avatar__holder" style="background-image: url({{$picture}})"></div>
                                                                    @else
                                                                        <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                                                    @endif
                                                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change image">
                                                                        <i class="fa fa-pen"></i>
                                                                        <input type="file" name="dash_widget_badge_bg_img" accept="image/png, image/jpg, image/jpeg">
                                                                    </label>
                                                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel image">
                                                                        <i class="fa fa-times"></i>
                                                                    </span>
                                                                </div>
                                                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                                            </div>
                                                            <div class="col-lg-4 text-right">
                                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                                                    <i class="la la-minus-circle"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group-block row">
                                                    <label class="col-lg-2 erp-col-form-label">Badge Query:<span class="required">* </span></label>
                                                    <div class="col-lg-10">
                                                        <textarea type="text" rows="2" maxlength="250" name="dash_widget_badge_query" class="form-control erp-form-control-sm">{{isset($Dtls->dash_widget_badge_query)?$Dtls->dash_widget_badge_query:''}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                            </div>
                                        @endforeach
                                    @endif
                                @else
                                    <div data-repeater-item class="widget_badges">
                                        <div class="form-group-block row">
                                            <div class="col-lg-6">
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Badge Name:<span class="required">* </span></label>
                                                    <div class="col-lg-8">
                                                        <input type="text" name="dash_widget_badge_name" maxlength="100" class="form-control erp-form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Badge Case:<span class="required">* </span></label>
                                                    <div class="col-lg-8">
                                                        <input type="text" name="dash_widget_case_name" maxlength="100" class="form-control erp-form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Badge Text Color:</label>
                                                    <div class="col-lg-8">
                                                        <input type="text" name="dash_widget_badge_color" maxlength="100" class="form-control erp-form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Badge Detail:</label>
                                                    <div class="col-lg-8">
                                                        <input type="text" name="dash_widget_badge_detail" maxlength="100" class="form-control erp-form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Background Color:</label>
                                                    <div class="col-lg-8">
                                                        <input type="color" name="dash_widget_badge_bg_color" maxlength="100" class="form-control erp-form-control-sm" value="#ffffff">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Background Image:</label>
                                                    <div class="col-lg-8 abc">
                                                        <select class="form-control kt-selectpicker badge_svg" name="dash_widget_badge_svg" id="dash_widget_badge_svg">
                                                            @foreach($data['svg'] as $svg)
                                                                <option value='{{$svg['svg_icon']}}' data-content='{{$svg['svg_icon'] }} {{$svg['svg_name']}}'>{{$svg['svg_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-lg-4 erp-col-form-label">Background Image Color:</label>
                                                    <div class="col-lg-8">
                                                        <input type="color" maxlength="100" name="dash_widget_badge_svg_color" class="form-control erp-form-control-sm bg_img_color" value="#000000">
                                                        <input type="hidden" maxlength="100" name="dash_widget_badge_svg_name" class="form-control erp-form-control-sm bg_img_name">
                                                        <input type="hidden" maxlength="100" class="form-control erp-form-control-sm bg_img_color_hidden">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <label class="erp-col-form-label">Badge Bg Image:<span class="required">* </span></label>
                                                        <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                                            <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                                            <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change image">
                                                                <i class="fa fa-pen"></i>
                                                                <input type="file" name="dash_widget_badge_bg_img" accept="image/png, image/jpg, image/jpeg">
                                                            </label>
                                                            <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel image">
                                                                <i class="fa fa-times"></i>
                                                            </span>
                                                        </div>
                                                        <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                                    </div>
                                                    <div class="col-lg-4 text-right">
                                                        <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                                            <i class="la la-minus-circle"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group-block row">
                                            <label class="col-lg-2 erp-col-form-label">Badge Query:<span class="required">* </span></label>
                                            <div class="col-lg-10">
                                                <textarea type="text" rows="2" maxlength="250" name="dash_widget_badge_query" class="form-control erp-form-control-sm"></textarea>
                                            </div>
                                        </div>
                                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                    </div>
                                @endif
                            </div>
                            <div class="row text-right">
                                <div class="col-lg-12">
                                    <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                        Add
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/dashboard-badge.js') }}" type="text/javascript"></script>
    <script>
    var casetype = '{{$case}}';

    // Class definition
        var KTFormRepeater = function() {
            var widget_badges = function() {
                $('#widget_badges').repeater({
                    initEmpty: false,
                    show: function () {

                        $('.kt-selectpicker').selectpicker();
                        var totalBadges = "{{$totalBadges}}";
                        if(totalBadges == 0){
                            var a = $(this).find('.kt-selectpicker>option').attr('data-content');
                            $(this).find('.abc>.dropdown>button>.filter-option>.filter-option-inner>.filter-option-inner-inner').html(a);
                            $(this).find('select').prop('selectedIndex',0);
                            $(this).find(".bg_img_color_hidden").val($(this).find(".bg_img_color").val());
                        }else{
                            var a = $(this).find('.kt-selectpicker>option:nth-child(2)').attr('data-content');
                            $(this).find('.abc>.dropdown>button>.filter-option>.filter-option-inner>.filter-option-inner-inner').html(a);
                            $(this).find('select').prop('selectedIndex',1);
                            $(this).find('select').children('option:first').remove();
                            $(this).find(".badge_name").removeAttr("readonly");
                            $(this).find(".badge_bg_color").val('#ffffff');
                            $(this).find(".bg_img_color").val('#000000');
                            $(this).find(".bg_img_color_hidden").val('#000000');
                        }
                        formRepeaterValidation();
                        $(this).slideDown();
                    },
                    ready: function(setIndexes){

                        $('.kt-selectpicker').selectpicker();
                        var totalBadges = "{{$totalBadges}}";
                        if(totalBadges != 0){
                            for(var i=0; i < totalBadges; i++){
                                var a = $('.badge_'+i+'>option').attr('data-content');
                                $('.badge_'+i).parent().parent().find('.abc>.dropdown>button>.filter-option>.filter-option-inner>.filter-option-inner-inner').html(a);
                                
                                a = a.split("</svg>");
                                $('.bg_img_name_'+i).parent().parent().find('.bg_img_name').val(a[1]);
                            
                            }
                        }else{
                            var a = $('.kt-selectpicker>option').attr('data-content');
						    $('.abc>.dropdown>button>.filter-option>.filter-option-inner>.filter-option-inner-inner').html(a);
                        }
                        formRepeaterValidation();
                    },
                    hide: function (deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                });
            }
            return {
                // public functions
                init: function() {
                    widget_badges();
                }
            };
        }();
        jQuery(document).ready(function() {
            KTFormRepeater.init();
            if(casetype == 'new'){
                ColorChange();
            }   
        });

        function ColorChange(){
            var defSvgName = $(".badge_svg option:selected").attr('data-content');
                defSvgName = defSvgName.split("</svg>");
            $(".bg_img_name").val(defSvgName[1]);
            $(".bg_img_color_hidden").val($(".bg_img_color").val());
            
            $('.badge_svg').change(function() {
                var SvgName = $(this).find('select option:selected').attr('data-content');
                SvgName = SvgName.split("</svg>");
                $(this).parents('.row').find('.bg_img_name').val(SvgName[1]);
            });
        }


        $(document).on('click','.abc>.dropdown>button', function(){
            var i = 0;
            $(this).parents('.row').find('.kt-selectpicker>option').each(function(){
                var value = $(this).attr('data-content');
                $(this).parents('.row').find('.abc>.dropdown>.show>.show>ul>li:eq('+i+')>a>span').html(value);
                i++;
            })
        });
        $(document).on('click','.abc>.dropdown>.show>.show>ul>li', function(){
            var value = $(this).find('a>span').html();
            $(this).parents('.row').find('.abc>.dropdown>button>.filter-option>.filter-option-inner>.filter-option-inner-inner').html(value);
        });


        $(document).on('change','.bg_img_color', function(){
            var value = $(this).val();
            var oldVal = $(this).parents('.row').find('.bg_img_color_hidden').val();
            var selectedVal = $(this).parents('.row').siblings().find('select option:selected').val();
            var selectedValChange = selectedVal.replaceAll('fill="'+oldVal+'"', 'fill="'+value+'"');
            $(this).parents('.row').siblings().find('select option:selected').val(selectedValChange);
            var g = $(this).parents('.row').siblings().find('.abc>.dropdown>button>.filter-option>.filter-option-inner>.filter-option-inner-inner>svg>g').children()
            $(this).parents('.row').find('.bg_img_color_hidden').val(value);
            for(var i=0;g.length>i;i++ ){
                var d = $(g[i]).attr('fill');
                if(d != undefined){
                    $(g[i]).attr('fill', value)
                }
            }
        });
    </script>
@endsection
