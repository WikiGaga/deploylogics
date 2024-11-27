@extends('layouts.layout2')
@section('title', 'Grid Test')

@section('pageCSS')
    <style>
        /* Code By Webdevtrick ( https://webdevtrick.com ) */
        * {
            box-sizing: border-box;
        }

        html,
        body {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: BlinkMacSystemFont, -apple-system, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }

        table {
            min-width: 100vw;
            width: auto;
            -webkit-box-flex: 1;
            flex: 1;
            display: grid;
            border-collapse: collapse;
            grid-template-columns:
                minmax(150px, 1fr)
                minmax(150px, 1.67fr)
                minmax(150px, 1.67fr)
                minmax(150px, 1.67fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 1.67fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 3.33fr)
                minmax(150px, 1.67fr);
        }

        thead,
        tbody,
        tr {
            display: contents;
        }

        th,
        td {
            padding: 15px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        th {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            background: #5cb85c;
            text-align: left;
            font-weight: normal;
            font-size: 1.1rem;
            color: white;
            position: relative;
        }

        th:last-child {
            border: 0;
        }

        .resize-handle {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            background: black;
            opacity: 0;
            width: 3px;
            cursor: col-resize;
        }

        .resize-handle:hover,
        .header--being-resized .resize-handle {
            opacity: 0.5;
        }

        th:hover .resize-handle {
            opacity: 0.3;
        }

        td {
            padding-top: 10px;
            padding-bottom: 10px;
            color: #808080;
        }

        tr:nth-child(even) td {
            background: #f8f6ff;
        }
    </style>
@endsection

@section('content')
    @php

    @endphp
    <form id="purchase_order_form" class="kt-form" method="post" action="">
    <input type="hidden" value='purc_order' id="form_type">
    @csrf
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        TEST-000897
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="po_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly id="kt_datepicker_3"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table>
                                <thead>
                                <tr>
                                    <th data-type="numeric">ID <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Barcode<span class="resize-handle"></span></th>
                                    <th data-type="text-short">Product Name <span class="resize-handle"></span></th>
                                    <th data-type="text-short">UOM <span class="resize-handle"></span></th>
                                    <th data-type="text-long">Packing <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Qty <span class="resize-handle"></span></th>
                                    <th data-type="text-long">FOC Qty <span class="resize-handle"></span></th>
                                    <th data-type="text-short">FC Rate <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Rate <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Amount <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Disc % <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Disc Amt <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Vat % <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Vat Amt <span class="resize-handle"></span></th>
                                    <th data-type="text-short">Gross Amt <span class="resize-handle"></span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['current']->po_details as $dtl)
                                        <tr>
                                            <td><input type="text" value="{{$loop->iteration}}" name="code"></td>
                                            <td><input type="text" value="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:""}}" name="f_name"></td>
                                            <td><input type="text" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" name="l_name"></td>
                                            <td><input type="text" value="{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}" name="email"></td>
                                            <td><input type="text" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" name="city"></td>
                                            <td><input type="text" value="{{$dtl->purchase_order_dtlquantity}}" name="country"></td>
                                            <td><input type="text" value="{{$dtl->purchase_order_dtlfoc_quantity}}" name="postal_code"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtlfc_rate,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtlrate,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtlamount,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtldisc_percent,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtldisc_amount,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtlvat_percent,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtlvat_amount,2)}}" name="iban"></td>
                                            <td><input type="text" value="{{number_format($dtl->purchase_order_dtltotal_amount,2)}}" name="iban"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
        </div>
    </div>
    </form>
    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/select2.js" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/select2.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')
    <script>
        var cd = console.log;
        // Code By Webdevtrick ( https://webdevtrick.com )
        const min = 50;
        // The max (fr) values for grid-template-columns
        const columnTypeToRatioMap = {
            numeric: 1,
            'text-short': 1.67,
            'text-long': 3.33 };


        const table = document.querySelector('table');


        const columns = [];
        let headerBeingResized;

        // The next three functions are mouse event callbacks

        // Where the magic happens. I.e. when they're actually resizing
        const onMouseMove = e => requestAnimationFrame(() => {
            console.log('onMouseMove');
            console.log(e);
            console.log(e.clientX);
            var clientX =  e.clientX - 320;
            // Calculate the desired width
            horizontalScrollOffset = document.documentElement.scrollLeft;
            const width = horizontalScrollOffset + clientX - headerBeingResized.offsetLeft;
            var b = " H:"+ horizontalScrollOffset;
            b += " X:"+ e.clientX;
            b += " X2:"+ clientX;
            b += " L:"+ headerBeingResized.offsetLeft;
            b += " =:"+ width;
            cd(b);
            // Update the column object with the new size value
            const column = columns.find(({ header }) => header === headerBeingResized);
            column.size = Math.max(min, width) + 'px'; // Enforce our minimum

            // For the other headers which don't have a set width, fix it to their computed width
            columns.forEach(column => {
                if (column.size.startsWith('minmax')) {// isn't fixed yet (it would be a pixel value otherwise)
                    column.size = parseInt(column.header.clientWidth, 10) + 'px';
                }
            });

            /*
                  Update the column sizes
                  Reminder: grid-template-columns sets the width for all columns in one value
                */
            table.style.gridTemplateColumns = columns.
            map(({ header, size }) => size).
            join(' ');
        });

        // Clean up event listeners, classes, etc.
        const onMouseUp = () => {
            console.log('onMouseUp');

            window.removeEventListener('mousemove', onMouseMove);
            window.removeEventListener('mouseup', onMouseUp);
            headerBeingResized.classList.remove('header--being-resized');
            headerBeingResized = null;
        };

        // Get ready, they're about to resize
        const initResize = ({ target }) => {
            console.log('initResize');

            headerBeingResized = target.parentNode;
            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);
            headerBeingResized.classList.add('header--being-resized');
        };

        // Let's populate that columns array and add listeners to the resize handles
        document.querySelectorAll('th').forEach(header => {
            const max = columnTypeToRatioMap[header.dataset.type] + 'fr';
            columns.push({
                header,
                // The initial size value for grid-template-columns:
                size: `minmax(${min}px, ${max})` });

            header.querySelector('.resize-handle').addEventListener('mousedown', initResize);
        });
    </script>
@endsection
