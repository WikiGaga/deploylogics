<style>
    /*
     font-family: Verdana !important;
     font-weight: 400,700
     font-style: normal,bold
   */
    /* Styles go here */
    body{
        font-family: Verdana !important;
        font-style: normal;
        color: #646c9a;
        height: 100%;
        margin: 0px;
        padding: 0px;
        font-size: 13px;
        -ms-text-size-adjust: 100%;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    #kt_portlet_table{
        margin-bottom: 0 !important;
        border: 0;
    }
    table.static_report_table,
    table#dynamic_report_table,
    table.table-bordered,
    table.bt-datatable {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }
    td, th {
        padding: 4px 5px !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
    th {
        font-size: 12px;
        color: #777676 !important;
    }
    td {
        font-size: 10px;
        color: #000000 !important;
        /*color: #777676 !important;*/
    }
    .table-bordered {
        border: 1px solid #bbbbbb !important;
        border-spacing:0;
        /* border: 1px solid #777777 !important;*/
    }
    .table tr th {
        border-bottom: 1px solid #c7c7c7 !important;
        cursor: pointer;
    }
    .table tr th,
    .table tr td{
        border-right: 1px solid #ebedf2 !important;
    }
    tr:nth-child(even)>td {
        border-bottom: 1px solid #c7c7c7 !important;
    }
    tr:nth-child(odd)>td{
        border-bottom: 1px solid #ead8b1 !important;
    }

    table#rep_sale_invoice_datatable {
        border: 0 !important;
    }
    table#rep_sale_invoice_datatable tr>th:first-child,
    table#rep_sale_invoice_datatable tr>td:first-child {
        border-left: 0 !important;
    }
    table#rep_sale_invoice_datatable tr>th:last-child,
    table#rep_sale_invoice_datatable tr>td:last-child {
        border-right: 0 !important;
    }
    table#rep_sale_invoice_datatable tr>th{
        border-top: 1.5px solid #777777 !important;
        border-bottom: 1.5px solid #777777 !important;
        cursor: default;
    }
    table#rep_sale_invoice_datatable .total>td{
        border-top: 1px solid #000000 !important;
        border-bottom: 1px solid #000000 !important;
    }
    table#dynamic_report_table .sub_total>td,
    table#rep_sale_invoice_datatable .sub_total>td{
        border-bottom: 1px solid #000000 !important;
    }
    table#dynamic_report_table .grand_total>td,
    table#rep_sale_invoice_datatable .grand_total>td{
        border-bottom: 2px solid #969696 !important;
        border-top: 2px solid #cecece !important;
        background-color: #f7f8fa;
        font-size: 15px;
    }
    .sale_invoice_footer{
        background: #f7f8fa;
    }
    .sale_invoice_footer .date{
        color: #FE21BE;
    }
    .date {
        font-size: 12px;
        color: #7d7d7d;
    }
    .date>span {
        color: #000000;
    }
    .row.row-block {
        margin: 10px 0 !important;
        padding: 0 !important;
    }

    .kt-portlet {
        background-color: #ffffff;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .kt-portlet .kt-portlet__head {
        position: relative;
        width: 100%;
        border-bottom: 1px solid #ebedf2;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        padding-bottom: 60px;
        margin-bottom: 30px;
    }
    .kt-invoice__brand{
        width: 80%;
    }
    .kt-portlet__head-toolbar{
        position: absolute;
        top:0;
        right: 0;
        width: 18%;
        text-align: center;
    }
    .kt-invoice__title{
        font-size: 32.5px;
    }
    .kt-invoice__criteria{
        font-size: 13px;
    }
    .kt-invoice__desc{
        color: #646c9a;
    }
    a{
        text-decoration: unset;
    }
    h1,h6{
        margin-block-start: 0 !important;
        margin-block-end: 0 !important;
        margin-inline-start: 0 !important;
        margin-inline-end: 0 !important;
    }
    h1{
        font-family: Verdana !important;
        font-style: normal !important;
        font-weight: 400;
        margin-top: 0;
    }
    h6{
        margin-top: 0;
        font-weight: 400;
    }
    .kt-invoice__title {
        font-family: Verdana !important;
        font-weight: 400;
        font-style: normal;
    }
    .kt-invoice__criteria,
    .kt-invoice__desc{
        font-weight: 400;
    }
    .sale_invoice_footer{
        height: 25px;
    }
    .kt-align-center,.text-center{ text-align: center; }
    .kt-align-right,.text-right{ text-align: right; }
    .kt-align-left,.text-left{ text-align: left; }

    .data_entry_header{
        display: none;
    }

    @page {
        margin-top: 12mm;
        margin-right: 8mm;
        margin-bottom: 8mm;
        margin-left: 8mm;
    }

    html, body{
        width: 100%;
        height: auto;
        margin: 0 !important;
        padding: 0 !important;
    }

    #content{
        padding-top: 10mm;
        padding-left: 6mm;
        padding-right: 6mm;
    }

    #downloadBtn,
    .modal,
    .modal-backdrop{
        display: none !important;
    }

    .kt-portlet .kt-portlet__head{
        padding-top: 4mm;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    table{
        page-break-inside: auto;
        border-collapse: collapse;
    }
    thead{
        display: table-header-group;
    }
    tfoot{
        display: table-footer-group;
    }
    tr{
        page-break-inside: avoid;
        page-break-after: auto;
    }
</style>
