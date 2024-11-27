@extends('layouts.template')
@section('title', ucfirst($data['case_name']))

@section('pageCSS')
    <link href="/assets/css/pages/support-center/home-1.css" rel="stylesheet" type="text/css" />
@endsection
<style>
    .erp-card{
        height:185px;
        box-shadow:rgba(82, 63, 105, 0.05) 0px 0px 30px 0px;
        display:flex;
        flex-direction:column;
        position:relative;
        margin-bottom: 20px;
    }
    .erp-card-search {
        height: 185px;
        width: 70% !important;
        padding-left: 65px;
        padding-right: 0px;
        padding-top: 20px;
    }
    .erp-card-search>h1 {
        font-size: 26px;
        font-weight: 600 !important;
    }
    .erp-card-search>.font-size-h4.mb-8 {
        font-size: 17.55px;
        font-weight: 400;
        margin-bottom: 26px;
        overflow-wrap: break-word;
    }
    .erp-bg-cover {
        width: 30% !important;
        background-position-x: 100%;
        background-position-y: -12px;
        background-size: 195px;
        background-repeat: no-repeat;
    }
    .erp-card-search-input-group {
        height: 45px;
        align-items: center;
        background-color: rgb(255, 255, 255);
        border-radius: 5.46px;
        color: rgb(63, 66, 84);
        display: flex;
        font-size: 13px;
        font-weight: 400;
        padding: 6.5px 19.5px;
    }
</style>
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $date =  date('d-m-Y');
        }
    @endphp
    <div class="col-lg-12">
        <div class="erp-card">
            <div class="erp-card-body rounded kt-padding-0 d-flex bg-light">
                <div class="erp-card-search">
                    <h1 class="font-weight-bolder text-dark mb-0">{{ucfirst($data['case_name'])}} Reports</h1>
                    <div class="font-size-h4 mb-8">Get Amazing Reports</div>
                    <!--begin::Form-->
                    <div class="erp-card-search-input-group">
                        <span class="svg-icon svg-icon-lg svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                                </g>
                            </svg>
                        </span>
                    <input type="text" id="reportSearch" class="erp-card-search-input form-control border-0" placeholder="Search....">
                    </div>
                    <!--end::Form-->
                </div>
                <div class="erp-bg-cover" style="background-image: url(/assets/media/custom/copy.svg);"></div>
            </div>
        </div>
        <div class="row form-group-block report_list">
            @if(count($data['list']) == 0)
                <div class="col-lg-12 report_item">
                    <div class="alert alert-secondary" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                        <div class="alert-text">
                            Data list not found.....
                        </div>
                    </div>
                </div>
            @else
                @foreach($data['list'] as $list)
                    @php
                         $view = $list->menu_dtl_id.'-view';
                    @endphp
                    @permission($view)
                    <div class="col-lg-4">
                        <a href="/reports/report-create/{{$list->report_static_dynamic}}/{{$list->report_case}}" class="kt-portlet erp-wave kt-iconbox wave-primary ">
                            <div class="kt-iconbox__body">
                                <div class="kt-iconbox__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
                                            <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="kt-iconbox__desc">
                                    <div class="kt-iconbox__content">
                                        {{$list->report_title}}
                                    </div>
                                    <div class="kt-iconbox_content_desc">
                                        Report Description.....
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endpermission
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('pageJS')

@endsection

@section('customJS')
    <script>
        $(document).on('mouseover','.kt-iconbox', function(){
            $(this).addClass('kt-iconbox--animate-slow erp-iconbox-primary').removeClass('wave-primary')
        }).on('mouseleave','.kt-iconbox', function(){
            $(this).removeClass('kt-iconbox--animate-slow erp-iconbox-primary').addClass('wave-primary')
        });
        $("#reportSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".report_list>.col-lg-4").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>
@endsection

