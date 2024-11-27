<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->
<head>
    <base href="">
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{--<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    --}}<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @yield('pageCSS')
    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles -->
    <link href="{{ asset('css/report.css') }}" rel="stylesheet" type="text/css" />
    <script src="/js/pages/js/lang/en.js" type="text/javascript"></script>
    <style>
        /*
    right click menu
*/
    #contextRateMenu,
    #contextCopyDataMenu,
    #contextChartMenu,
    #contextMenu {
        position: absolute;
        display: none;
        z-index: 99;
    }
    div#contextChartMenu a,
    div#contextCopyDataMenu a,
    div#contextMenu a {
        color: #6b6a6a;
        font-family: Verdana;
        font-weight: 400;
    }
    div#contextChartMenu a:hover,
    div#contextCopyDataMenu a:hover,
    div#contextMenu a:hover {
        background: #FFA800;
        color: #fff;
    }

    div#contextRateMenu table.right_rate_list,
    div#contextMenu ul {
        box-shadow: 0px -2px 20px 0px rgba(0, 0, 0, 0.35);
        -webkit-box-shadow: 0px -2px 20px 0px rgba(0, 0, 0, 0.35);
        -moz-box-shadow: 0px -2px 20px 0px rgba(0, 0, 0, 0.35);
    }
    </style>

    <script src="{{ asset('/js/generate_pdf.js') }}"></script>
    <style>
        @media print {
            #downloadBtn{
                display: none;
            }
        }
    </style>
</head>

<body>
@if(isset($data['form_file_type']) && $data['form_file_type'] == 'pdf')
    @include('reports.pdfCss')
@endif
@include('elements/popup')

@include('reports.template.download_btn')

<div id="content">
    @yield('content')
</div>

</body>
@yield('pageJS')
<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "dark": "#282a3c",
                "light": "#ffffff",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": [
                    "#c5cbe3",
                    "#a1a8c3",
                    "#3d4465",
                    "#3e4466"
                ],
                "shape": [
                    "#f0f3ff",
                    "#d9dffa",
                    "#afb4d4",
                    "#646c9a"
                ]
            }
        }
    };
    var dataSession = <?php echo json_encode(Session::get('dataSession')); ?>;
    function valueEmpty(val){
        if(val == 0 || val == undefined || val == "" || val == null || val == NaN || val == 'NaN' || !val){
            return true;
        }
        return false;
    }
</script>
<!--begin::Global Theme Bundle(used by all pages) -->
<script src="/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
<script src="/assets/js/scripts.bundle.js" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/shortcuts.js') }}" type="text/javascript"></script>
<!--begin::Page Scripts(used by this page) -->
<script src="{{ asset('js/pages/js/report-user-html-table.js') }}" type="text/javascript"></script>
<!--end::Page Scripts -->

@yield('customJS')

@include('layouts.commonJSFunc')

<!--end::Global Theme Bundle -->

<script>
    var table_width = $(".static_report_table").width();
    if(table_width > 1300){
        table_width = parseInt(table_width) + 200;
        table_width = table_width+'px';
        $("#kt_portlet_table").css({'width':'100%'});
    }
    $(".generate_report").click(function(e){
        var id = $(this).data('id');
        var type = $(this).data('type');
        var path = '';
        
        // accounts
        var accountsTypeList = ['crv','cpv','brv','bpv','jv','obv','lv'];
        if(accountsTypeList.includes(type)) {
            path = '/accounts/'+type+'/form/'+id;
        }

        // purchase
        if(type == 'GRN' || type == 'GRNM'){path = '/grn/form/'+id;}
        if(type == 'PR'){path = '/purchase-return/form/'+id;}
        if(type == 'PO'){path = '/purchase-order/form/'+id;}
        // sale
        if(type == 'SI'){path = '/sales-invoice/form/'+id;}
        if(type == 'SR'){path = '/sale-return/form/'+id;}
        if(type == 'POS'){path = '/pos-sales-invoice/form/'+id;}
        if(type == 'RPOS'){path = '/pos-sales-return/form/'+id;}
        if(type == 'SD'){path = '/sales-delivery/form/'+id;}
        if(type == 'LFS'){path = '/sales-fee/form/'+id;}
        if(type == 'RI'){path = '/rebate-invoice/form/'+id;}
        if(type == 'DRF'){path = '/display-rent-fee/form/'+id;}
        // stock inventory
        if(type == 'OS'){path = '/stock/opening-stock/form/'+id;}
        if(type == 'EI'){path = '/stock/expired-items/form/'+id;}
        if(type == 'ST'){path = '/stock/stock-transfer/form/'+id;}
        if(type == 'STR'){path = '/stock/stock-receiving/form/'+id;}
        if(type == 'SA'){path = '/stock/stock-adjustment/form/'+id;}
        if(type == 'SP'){path = '/stock/sample-items/form/'+id;}
        if(type == 'DI'){path = '/stock/damaged-items/form/'+id;}
        if(type == 'IST'){path = '/stock/internal-stock-transfer/form/'+id;}

        if(path != ''){
            window.open(path, "_blank");
        }
    });
    $(document).on('ready' , function(e){
        $('body').removeClass('pointerEventsNone');
    });
</script>
<!-- end::Body -->
<script src="{{ asset('/js/jquery.table2excel.min.js') }}"></script>

@yield('exportXls')

<script>

    $(document).find('table:first-child').addClass('table2ExcelExport');

    $(document).on('click','.btnExcelExport',function() {
        $(".table2ExcelExport").table2excel({
            // exclude: ".noExport",
            filename: "report.xls",
        });
    });
    
    $(document).on('click','.btnPdfExport',function() {
        const element = document.getElementById('content');
        var opt = {
            filename: 'report.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            }
        };
        // Choose the element that our invoice is rendered in.
        html2pdf().set(opt).from(element).save();
    });
</script>
</html>
