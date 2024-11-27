<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @yield('pageCSS')
    <link href="{{ asset('css/printnew.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="/js/pages/js/lang/en.js" type="text/javascript"></script>
</head>

<body>
   
    @if(isset($print_type) && $print_type == 'pdf')
        @include('prints.pdfCssNew')
    @else
        @if(isset($heading) && $heading != 'BROUCHER')
            <div id="Top-Header">
                <span style="display: block;position: absolute;vertical-align: text-top;">Header </span><span>Show:</span>
                {{-- <input type="checkbox" onclick="HeaderHide();" id="HeaderHide" checked> --}}

                {{-- <a href="#" onclick="window.print();" style="text-decoration: none;color:black;" title="Print"><i class="material-icons">print</i></a> --}}
                <a href="{{isset($pdf_link)?$pdf_link:''}}" target="_blanck" style="text-decoration: none;color:black;" title="Pdf"><i class="material-icons">description</i></a>
                <!--<a href="" id="excel" style="text-decoration: none;color:black;" title="Excel"><i class="material-icons">assessment</i></a><br>-->

                {{-- <div id="styleCss">
                    <style>
                        /*@media print{
                            .head{display:none;}
                            .headerHeight{height: 97.33px !important;}
                        }*/
                    </style>
                </div> --}}
            </div>
        @endif
    @endif
    <div class="headerHeight"></div>


    @yield('content')
    
    @if(isset($heading) && $heading != 'BROUCHER')
        <table class="tab" style="margin-top: 15px">
            <tr>
                <td class="alignright"><span style="font-size:10px;">Print Date & Time: {{date("d-m-Y h:i:s")}} User Name: {{auth()->user()->name}}</td>
            </tr>
        </table>
    @endif
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
