@extends('prints.partial.thermal_template')

@section('pageCSS')
<style>
    body { font-size: 10px; }
    .thermal_print_body { width: 275px !important; margin: 0 auto; }
    .main_heading {
        text-align: center; font-weight: 800; border: 1px solid;
        padding: 3px; margin: 4px 0; border-bottom: 2px solid; font-size: 12px;
    }
    .cls_shift { font-size: 14px; }
    .basic_info {
        font-weight: 800; border: 1px dashed; font-size: 11px; padding: 0 3px;
    }
    .basic_info > div { margin: 6px 0; border-bottom: 1px dashed; }
    .basic_info > div:last-child { border-bottom: 0; }
    .pos_document_activity td:first-child,
    .cash_received_summary td:first-child { border-left: 1px dotted; }
    .pos_document_activity td,
    .cash_received_summary td {
        border-top: 1px dotted; border-right: 1px dotted;
        padding-top: 3px; padding-bottom: 3px;
    }
    .pos_document_activity tr:last-child td,
    .cash_received_summary tr:last-child td { border-bottom: 1px dotted; }
    .total_amount { font-size: 14px; font-weight: 800; }
    .voucher_title { font-size: 12px; font-weight: 800; background: #d8d8d8; }
    .br-none { border-right: none !important; }
</style>
@endsection

@section('title', 'POS DUMMY SLIP')
@section('content')
<div class="thermal_print_body">
    <div class="main_heading">POS Session Activity Report</div>
    <div class="main_heading cls_shift">Counter: Terminal 1</div>
    <div class="main_heading cls_shift">Shift A</div>

    <div class="basic_info">
        <div><span>Opening Date:</span> <span style="margin-left: 10px;">01-Jan-2025 Wed 09:00 AM</span></div>
        <div><span>Closing Date:</span> <span style="margin-left: 17px;">01-Jan-2025 Wed 06:00 PM</span></div>
        <div><span>Salesman:</span> <span style="margin-left: 35px;">John Doe</span></div>
    </div>

    <div class="main_heading">POS DOCUMENT ACTIVITY</div>
    <table width="100%" class="pos_document_activity">
        <tr><th>Document Type</th><th># of Doc.</th><th>Amount</th><th>Discount</th></tr>
        <tr><td>Sales Invoice</td><td class="text-right">10</td><td class="text-right">5000</td><td class="text-right">250</td></tr>
    </table>

    <div class="main_heading">CASH RECEIVED SUMMARY</div>
    <table width="100%" class="cash_received_summary">
        <tr><td>Opening Amount</td><td class="text-right">0</td></tr>
        <tr><td>Sale - Cash</td><td class="text-right">5000</td></tr>
        <tr><td>Total Amount</td><td class="text-right total_amount">5000</td></tr>
    </table>

    <div class="main_heading">CASH PAID SUMMARY</div>
    <table width="100%" class="cash_received_summary">
        <tr><td>Cash Back/Sale Return</td><td class="text-right">200</td></tr>
        <tr><td>Paid To Office</td><td class="text-right">1000</td></tr>
        <tr><td>Total Amount</td><td class="text-right total_amount">1200</td></tr>
    </table>

    <div class="main_heading">CURRENCY DENOMINATION</div>
    <table width="100%" class="cash_received_summary">
        <tr><td class="text-right">1000/=</td><td class="text-right">3</td><td class="text-right">3000</td></tr>
        <tr><td class="text-right">500/=</td><td class="text-right">4</td><td class="text-right">2000</td></tr>
        <tr><td>Total Amount</td><td></td><td class="text-right total_amount">5000</td></tr>
        <tr><td>Short/Excess Amount</td><td></td><td class="text-right total_amount">0</td></tr>
        <tr><td>As Per Software</td><td></td><td class="text-right total_amount">5000</td></tr>
    </table>

    <div class="main_heading">CREDIT CARD MERCHANT SUMMARY</div>
    <table width="100%" class="pos_document_activity">
        <tr><th>Merchant Name</th><th># of Doc.</th><th>Amount</th></tr>
        <tr><td>Bank A</td><td class="text-right">3</td><td class="text-right">3000</td></tr>
        <tr><td><b>Total</b></td><td class="text-right"><b>3</b></td><td class="text-right"><b>3000</b></td></tr>
    </table>

    <div class="main_heading">DISCOUNT SUMMARY</div>
    <table width="100%" class="pos_document_activity">
        <tr><th>Communicate Type</th><th>Sale</th><th>Discount</th></tr>
        <tr><td>None</td><td class="text-right">5250</td><td class="text-right">250</td></tr>
        <tr><td class="br-none">Total Amount</td><td colspan="2" class="text-right total_amount">5250</td></tr>
    </table>

    <div class="main_heading">TAX SUMMARY</div>
    <table width="100%" class="pos_document_activity">
        <tr><th>Document Type</th><th># of Doc.</th><th>Amount</th><th>GST</th></tr>
        <tr><td>Sales Invoice</td><td class="text-right">10</td><td class="text-right">4500</td><td class="text-right">500</td></tr>
        <tr><td colspan="2" class="br-none">Total</td><td class="text-right total_amount">4500</td><td class="text-right total_amount">500</td></tr>
    </table>

    <table width="100%" style="text-align:center;margin-top:10px; border-top: 2px solid #000;">
        <tr><td>Operator: Jane Admin</td></tr>
        <tr><td>Software Developed By: <b>Royalsoft</b></td></tr>
        <tr><td>Print Date & Time: {{ date('d-m-Y h:i:s') }}</td></tr>
    </table>
</div>
@endsection
