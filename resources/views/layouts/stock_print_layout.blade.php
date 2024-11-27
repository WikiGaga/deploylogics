<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @yield('pageCSS')
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="/js/pages/js/lang/en.js" type="text/javascript"></script>
</head>

<body>
    @if(isset($print_type) && $print_type == 'pdf')
        @include('prints.pdfCss')
    @else
    <div id="Top-Header" style="height: 30px; font-size:10px;">
        <style>
            #block_container
            {
                text-align:center;
            }
            #exports, #prints_types
            {
                display:inline;
            }
            #exports
            {
                float: left;
            }
            a:link, :visited {
            color: unset;
            background-color: transparent;
            text-decoration: none;
            }
        </style>
        <div id="block_container">
            {{-- <div id="exports">
                <span >Header </span><span>Show:</span>
                <input type="checkbox" onclick="HeaderHide();" id="HeaderHide" checked>

                <a href="#" onclick="window.print();" style="text-decoration: none;color:black;" title="Print"><i class="material-icons">print</i></a>
                <a href="{{isset($pdf_link)?$pdf_link:''}}" target="_blanck" style="text-decoration: none;color:black;" title="Pdf"><i class="material-icons">description</i></a>
                <a href="" id="excel" style="text-decoration: none;color:black;" title="Excel"><i class="material-icons">assessment</i></a>
            </div> --}}
            <div class="row prints_types">
                @if ($type == 'st')
                    <div class="col-md-10">
                        <div class="kt-radio-inline">
                            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                <a href="{{$data['stock_transfer_link']}}?print=0">
                                <input type="radio" {{$print==0?"checked":""}}> Stock Transfer
                                <span></span>
                                </a>
                            </label>
                            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                <a href="{{$data['stock_transfer_link']}}?print=1">
                                    <input type="radio" {{$print==1?"checked":""}}> Stock Transfer Landscape
                                    <span></span>
                                </a>
                            </label>
                            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                <a href="{{$data['stock_transfer_link']}}?print=4">
                                    <input type="radio" {{$print==4?"checked":""}}> Dispatch
                                    <span></span>
                                </a>
                            </label>
                        </div>
                    </div>                    
                @elseif($type == 'str')
                    <div class="col-md-10">
                        <div class="kt-radio-inline">
                            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                <a href="{{$data['print_link']}}?print=2">
                                <input type="radio" {{$print==2 || $print=='' ?"checked":""}}> Stock Recieve
                                <span></span>
                                </a>
                            </label>
                            <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                <a href="{{$data['print_link']}}?print=3">
                                    <input type="radio" {{$print==3?"checked":""}}> Stock Recieve Landscape
                                    <span></span>
                                </a>
                            </label>
                        </div>
                    </div>                    
                @endif
            </div>
        </div>

        <div id="styleCss">
            <style>
                /*@media print{
                    .head{display:none;}
                    .headerHeight{height: 97.33px !important;}
                }*/
            </style>
        </div>
    </div>
    @endif
    <div class="headerHeight"></div>
    <table class="head" width="100%">
        <tbody>
        <tr>
            <td width="30%">
                <table class="tab">
                    <tr>
                        <td>
                            @php
                                $QrCode = new \TheUmar98\BarcodeBundle\Utils\QrCode();
                                $QrCode->setText($code);
                                $QrCode->setExtension('jpg');
                                $QrCode->setSize(40);
                                $image = $QrCode->generate();
                            @endphp
                            @if(isset($image) && $image != '')
                                <img src="data:image/png;base64,{{$image}}" />
    
                            @else
                                <div></div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%"></td>
            <td width="45%">
                <table class="tab">
                    <tr>
                        <td class="title aligncenter">@yield('page_heading')</td>
                    </tr>
                    <tr>
                        <td>                            
                            <div class="title aligncenter" style="font-weight:normal; font-size:14px;">{{auth()->user()->branch->branch_name}}</div>
                            <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Tax No:</b>{{auth()->user()->branch->branch_tax_certificate_no}}</div>
                            <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Phone:</b>{{auth()->user()->branch->branch_mobile_no}}</div>
                            <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Fax:</b>{{auth()->user()->branch->branch_fax}}</div>
                            <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Email:</b>{{auth()->user()->branch->branch_email}}</div>
                            <div class="title aligncenter" style="font-weight:normal; font-size:11px;"><b>Address:</b>{{auth()->user()->branch->branch_address}}</div>                        
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    @if(isset($document_type) && ($document_type == "RI" || $document_type == "DRF" || $document_type == "LFS" || $document_type == "PDS" ))
        <div class="title aligncenter" style="font-size: 20px;">@yield('heading_tax')</div>
    @else
        <div class="title aligncenter">@yield('heading')</div>
    @endif

    @yield('content')

    @include('prints.partial.footer')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('/js/jquery.table2excel.min.js') }}"></script>
    <script>
        function HeaderHide(){
            // if checked
            if(document.getElementById('HeaderHide').checked == true){
                document.getElementById('styleCss').innerHTML = '';
            }
            // if unchecked
            if(document.getElementById('HeaderHide').checked == false){
                var styleCss = document.getElementById('styleCss');
                var css = '@media print{ .head{display:none;}.headerHeight{height: 97.33px !important;} }';
                var style = document.createElement('style');
                styleCss.appendChild(style);

                style.type = 'text/css';
                if (style.styleSheet){
                    // This is required for IE8 and below.
                    style.styleSheet.cssText = css;
                } else {
                    style.appendChild(document.createTextNode(css));
                }
            }
        }

        $('#btn_toggle').click(function(event){
            $('.table_column_dropdown-menu').toggle(600);
            event.stopPropagation();
        })
        $('.table_column_dropdown-menu').click( function(e) {
            e.stopPropagation();
            // when you click within the content area,
            // it stops the page from seeing it as clicking the body too
        });
        $("body").click(function(event) {
            $(".table_column_dropdown-menu").hide(600);
            event.stopPropagation();
        });
        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
            var table = document.getElementById('document_table_data');
            var tr = table.querySelectorAll('tr');
            var tbody = table.querySelectorAll('tbody');
            tr.forEach(function(tr1) {
                tbody[0].appendChild(tr1);
            });
            var val = $(this).val();
            $('.data_listing thead tr').find('th:eq('+val+')').toggle();
            $('.data_listing tbody tr').find('th:eq('+val+')').toggle();
            $('.data_listing tbody tr').find('td:eq('+val+')').toggle();
            hiddenFiledsCount();

        });
        function hiddenFiledsCount(){
            var count = 0;
            var hiddenFiled = [];
            $('.listing_dropdown>li').each(function(){
                if(!$(this).find('label>input').is(':checked')){
                    count += 1;
                    hiddenFiled.push($(this).find('label>input').val());
                }
            });
            $('.hiddenFiledsCount>span').html(count);
        }

        // generate excel
        $('#excel').click(function(event){
            $("#document_table_data").table2excel({
                // exclude: ".noExport",
                filename: "PO.xls",
            });
        });

        //header fixed
        window.onscroll = function() {myFunction()};

        var header = document.getElementById("Top-Header");
        var sticky = header.offsetTop;

        function myFunction() {
        if (window.pageYOffset > sticky) {
            header.classList.add("sticky");
        } else {
            header.classList.remove("sticky");
        }
        }
    </script>
    @yield('customJS')

</body>
</html>