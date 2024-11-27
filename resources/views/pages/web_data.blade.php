@extends('layouts.pattern')
@section('title', 'Web Data')

@section('pageCSS')
    <link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />
    <style>
        .wdr-ui-element.wdr-ui.wdr-ui-label.wdr-credits{
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                {{--@include('elements.page_header',['page_data' => $data['page_data']])--}}
            </div>
        </div>
        <div class="kt-portlet__body">
            <div id="wdr-component"></div>

        </div>
    </div>
@endsection
@section('pageJS')
    <script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
    <script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
@endsection
@section('customJS')
    <script>
        var jsdata = <?php echo json_encode($data); ?>;
            console.log(jsdata);
        var pivot = $("#wdr-component").webdatarocks({
            beforetoolbarcreated: customizeToolbar,
            toolbar: true,
            report: {
                "dataSource": {
                    "dataSourceType": "json",
                    data : jsdata
                    // "filename": "https://cdn.webdatarocks.com/data/data.csv"
                    /*"data": [
                        {
                            category :'Accessories',
                            color:'red',
                            country:'pakistan',
                            price:200
                        },
                        {
                            category :'Bikes',
                            color:'white',
                            country:'bangladesh',
                            price:400
                        },
                        {
                            category :'Clothing',
                            color:'blue',
                            country:'india',
                            price:500
                        },
                        {
                            category :'Components',
                            color:'blue',
                            country:'iran',
                            price:600
                        },
                        {
                            category :'Components',
                            color:'red',
                            country:'pakistan',
                            price:499
                        },
                    ]*/
                },
                "slice": {
                    "rows": [
                        {
                            "uniqueName": "product_name"
                        },
                        {
                            "uniqueName": "customer_name"
                        },
                        {
                            "uniqueName": "sales_sales_man_name"
                        },
                        {
                            "uniqueName": "sales_dtl_total_amount"
                        }
                    ],
                    "columns": [],
                    "measures": [
                        {
                            "uniqueName": "sales_dtl_total_amount",
                            "aggregation": "sum",
                            "active": false,
                            "format": "495vvg7g"
                        }
                    ],
                    "flatOrder": [
                        "product_name",
                        "customer_name",
                        "sales_sales_man_name",
                        "sales_dtl_total_amount",
                    ]
                },
                "options": {
                    "grid": {
                        "type": "flat"
                    }
                },
                "formats": [
                    {
                        "name": "495vvg7g",
                        "thousandsSeparator": " ",
                        "decimalSeparator": ".",
                        "decimalPlaces": 2,
                        "currencySymbol": "",
                        "currencySymbolAlign": "left",
                        "nullValue": "",
                        "textAlign": "right",
                        "isPercent": false
                    }
                ]
            }
        });
        function customizeToolbar(toolbar) {
            var tabs = toolbar.getTabs(); // get all tabs from the toolbar
            toolbar.getTabs = function() {
                delete tabs[0]; // delete the first tab
                delete tabs[1]; // delete the first tab
                delete tabs[2]; // delete the first tab
                return tabs;
            }
        }
    </script>
@endsection
