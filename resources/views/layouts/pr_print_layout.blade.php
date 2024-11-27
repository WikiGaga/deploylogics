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
                <div class="col-md-10">
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=0">
                            <input type="radio" id="default" name="pi_types"{{$print_type==0?"checked":""}}> Default
                            <span></span>
                            </a>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=1">
                                <input type="radio" id="pi" name="pi_types"{{$print_type==1?"checked":""}}> Purchase Invoice
                                <span></span>
                            </a>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=2">
                                <input type="radio" id="expi" name="pi_types" {{$print_type==2?"checked":""}}> Ex Purchase Invoice With Inventory
                                <span></span>
                            </a>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=3">
                                <input type="radio" id="piuk" name="pi_types" {{$print_type==3?"checked":""}}>Purchase Invoice (UK)
                                <span></span>
                            </a>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=4">
                                <input type="radio" id="pil" name="pi_types" {{$print_type==4?"checked":""}}> Purchase Invoice Landscape
                                <span></span>
                            </a>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=5">
                                <input type="radio" id="sddi" name="pi_types" {{$print_type==5?"checked":""}}> Stock Direct Delivery Invoice
                                <span></span>
                            </a>
                        </label>
                        <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                            <a href="{{$data['print_link']}}?type=6">
                                <input type="radio" id="sddi" name="pi_types" {{$print_type==6?"checked":""}}> Dispatch
                                <span></span>
                            </a>
                        </label>
                    </div>
                </div>
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
    <table class="tab">
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
    </table>
    @if(isset($document_type) && ($document_type == "RI" || $document_type == "DRF" || $document_type == "LFS" || $document_type == "PDS" ))
        <div class="title aligncenter" style="font-size: 20px;">@yield('heading_tax')</div>
    @else
        @php
            $heading = strtoupper($data['title']);
        @endphp
        {{-- <div class="title pi_table grn_prints aligncenter">PURCHASE INVOICE</div>
        <div class="title expi_table grn_prints aligncenter">EX PURCHASE INVOICE WITH INVENTORY</div>
        <div class="title piuk_table grn_prints aligncenter">PURCHASE INVOICE (UK)</div>
        <div class="title pil_table grn_prints aligncenter">PURCHASE INVOICE LANDSCAPE</div>
        <div class="title sddi_table grn_prints aligncenter">STOCK DIRECT DELIVERY INVOICE</div> --}}
    @endif
        {{--form content--}}
        <table class="tableData">
            <tr>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Document No :</span>
                        <span class="normal normal-block">{{isset($code)?$code:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Document Date :</span>
                        <span class="normal normal-block">{{isset($date)?$date:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor:</span>
                        <span class="normal normal-block">{{isset($supplier_name)?$supplier_name:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor Phone No:</span>
                        <span class="normal normal-block">{{isset($phon_no)?$phon_no:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor Email:</span>
                        <span class="normal normal-block">{{isset($email)?$email:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Vendor Address:</span>
                        <span class="normal normal-block">{{isset($address)?$address:''}}</span>
                    </div>
                </td>
                <td width="33.33%"></td>
                <td width="33.33%">
                    <div>
                        <span class="heading heading-block">Currency:</span>
                        <span class="normal normal-block">{{isset($currency)?$currency:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Exchange Rate:</span>
                        <span class="normal normal-block">{{isset($exchange_rate)?$exchange_rate:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Store:</span>
                        <span class="normal normal-block">{{isset($store)?$store:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Payment Terms:</span>
                        <span class="normal normal-block">{{isset($days)?$days:''}}{{isset($payment_term)?$payment_term:''}}</span>
                    </div>
                    <div>
                        <span class="heading heading-block">Bill No:</span>
                        <span class="normal normal-block">{{isset($bill_no)?$bill_no:''}}</span>
                    </div>
                </td>
            </tr>
        </table>
    @yield('headings')

    @yield('content')
    @include('prints.partial.footer')
    <script src="{{ asset('/js/jquery3_5_1.min.js') }}"></script>
    <script src="{{ asset('/js/jquery.table2excel.min.js') }}"></script>
    <script>
        $("#default").click(function(){
            window.location = "{{$data['print_link']}}?type=0";
        });
        $("#pi").click(function(){
            window.location = "{{$data['print_link']}}?type=1";
        });
        $("#expi").click(function(){
            window.location = "{{$data['print_link']}}?type=2";
        });
        $("#piuk").click(function(){
            window.location = "{{$data['print_link']}}?type=3";
        });
        $("#pil").click(function(){
            window.location = "{{$data['print_link']}}?type=4";
        });
        $("#sddi").click(function(){
            window.location = "{{$data['print_link']}}?type=5";
        });
    </script>
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

        // //header fixed
        // window.onscroll = function() {myFunction()};

        // var header = document.getElementById("Top-Header");
        // var sticky = header.offsetTop;

        // function myFunction() {
        // if (window.pageYOffset > sticky) {
        //     header.classList.add("sticky");
        // } else {
        //     header.classList.remove("sticky");
        // }
        // }
    </script>
    @yield('customJS')

</body>
</html>