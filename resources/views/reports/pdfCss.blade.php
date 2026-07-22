<style>
    @font-face {
        font-family: 'NotoSansArabic';
        src: url('{{ str_replace('\\', '/', public_path('NotoSansArabic/NotoSansArabic-Regular.ttf')) }}') format('truetype');
    }

    body,
    table,
    td,
    th,
    h1,
    h6 {
        font-family: "dejavu sans", "NotoSansArabic", sans-serif !important;
        font-style: normal;
    }

    .arabic-text {
        font-family: "dejavu sans", "NotoSansArabic", sans-serif !important;
        direction: rtl;
        unicode-bidi: embed;
        text-align: right;
    }

    body {
        color: #333;
        height: auto;
        margin: 0;
        padding: 0;
        font-size: 10px;
    }

    #kt_portlet_table {
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
        padding: 3px 4px !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        vertical-align: top;
    }

    th {
        font-size: 9px;
        color: #333 !important;
        background-color: #e8eaf6 !important;
        font-weight: bold;
    }

    td {
        font-size: 9px;
        color: #000 !important;
    }

    .table-bordered {
        border: 1px solid #999 !important;
        border-spacing: 0;
    }

    .table tr th,
    .table tr td {
        border: 1px solid #ccc !important;
    }

    tr:nth-child(even) > td {
        background-color: #f9f9f9;
    }

    table#dynamic_report_table .sub_total > td,
    table#rep_sale_invoice_datatable .sub_total > td {
        border-bottom: 1px solid #000 !important;
        font-weight: bold;
    }

    table#dynamic_report_table .grand_total > td,
    table#rep_sale_invoice_datatable .grand_total > td {
        border-bottom: 2px solid #666 !important;
        border-top: 2px solid #999 !important;
        background-color: #f0f0f0;
        font-size: 10px;
        font-weight: bold;
    }

    table#dynamic_report_table tr.group_1 td,
    table#dynamic_report_table tr.group_2 td {
        background-color: #eceff1 !important;
        font-weight: bold;
    }

    .sale_invoice_footer {
        background: #f7f8fa;
    }

    .date {
        font-size: 10px;
        color: #555;
    }

    .date > span {
        color: #000;
    }

    .row.row-block {
        margin: 6px 0 !important;
        padding: 0 !important;
    }

    .kt-portlet {
        background-color: #fff;
        margin-bottom: 0;
    }

    .kt-portlet .kt-portlet__head {
        position: relative;
        width: 100%;
        border-bottom: 1px solid #ddd;
        padding: 8px 0 6px !important;
        margin-bottom: 8px !important;
        overflow: hidden;
    }

    .kt-invoice__brand {
        width: 70% !important;
        float: left;
    }

    .kt-portlet__head-toolbar {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        float: right;
        width: 25% !important;
        text-align: center;
    }

    .kt-invoice__title {
        font-size: 16px !important;
        font-weight: bold;
        margin: 0 0 4px 0 !important;
        line-height: 1.2;
    }

    .kt-invoice__criteria {
        font-size: 9px;
        margin: 2px 0;
        line-height: 1.3;
    }

    .kt-invoice__desc {
        color: #333;
        font-size: 9px;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    h1, h6 {
        margin: 0 !important;
    }

    .kt-align-center, .text-center { text-align: center; }
    .kt-align-right, .text-right { text-align: right; }
    .kt-align-left, .text-left { text-align: left; }

    .data_entry_header,
    .table-responsive-scroll select,
    .pagination,
    nav[role="navigation"],
    .clickable-cell {
        display: none !important;
    }

    .table-responsive-scroll {
        overflow: visible !important;
        max-height: none !important;
    }

    table#dynamic_report_table thead {
        position: static !important;
    }

    table#dynamic_report_table thead tr th {
        position: static !important;
        box-shadow: none !important;
    }

    @page {
        margin-top: 10mm;
        margin-right: 8mm;
        margin-bottom: 8mm;
        margin-left: 8mm;
    }

    html, body {
        width: 100%;
        height: auto;
        margin: 0 !important;
        padding: 0 !important;
    }

    #content {
        padding: 6mm 4mm 0;
    }

    #downloadBtn,
    .modal,
    .modal-backdrop {
        display: none !important;
    }

    table {
        page-break-inside: auto;
        border-collapse: collapse;
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-footer-group;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
</style>
