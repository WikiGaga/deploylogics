@extends('prints.partial.thermal_template')

@section('pageCSS')
<style>
    body { font-size: 10px; }
    .thermal_print_body { width: 275px !important; margin: 0 auto; }
    .main_heading {
        text-align: center; font-weight: 800; border: 1px solid;
        padding: 3px; margin: 4px 0; border-bottom: 2px solid; font-size: 14px;
    }
    .address_heading {
        text-align: center; border: 1px solid;
        padding: 3px; margin: 4px 0; border-bottom: 2px solid; font-size: 12px;
    }
    .basic_info {
        font-weight: 800; border: 1px dashed; font-size: 11px; padding: 0 3px;
    }
    .basic_info > div { margin: 6px 0; border-bottom: 1px dashed; }
    .basic_info > div:last-child { border-bottom: 0; }

    .pos_table td,
    .pos_table th {
        border-top: 1px dotted; border-right: 1px dotted;
        padding: 3px;
    }

    .pos_table td:first-child { border-left: 1px dotted; }
    .pos_table tr:last-child td { border-bottom: 1px dotted; }

    .total_amount { font-size: 13px; font-weight: bold; }
    .footer_table td { text-align: center; font-size: 10px; padding-top: 5px; }

</style>
@endsection

@section('title', 'Close Out Report')
@section('content')
<div class="thermal_print_body">
    <div class="main_heading">malek al atemah <br> international lcc</div>
    <div class="address_heading">
        CR No. 1356079 <br>
        Oman <br>
        0056986161811
    </div>
    <div class="main_heading">Close Out Report</div>

    <div class="basic_info">
        <div><span>Sign In Time:</span> <span style="margin-left: 10px;">6/25/2025 10:57:33 AM</span></div>
        <div><span>Sign Out Time:</span> <span style="margin-left: 10px;">6/25/2025 2:00:44 AM</span></div>
    </div>

    <div class="main_heading">A. Cash In/Out</div>
    <table width="100%" class="pos_table">
        <tr><td>Beginning Balance</td><td class="text-right">0.000</td></tr>
        <tr><td>Total Cash Sales</td><td class="text-right">126.298</td></tr>
        <tr><td>Cash Payment In</td><td class="text-right">0.000</td></tr>
        <tr><td>Staff Payment Out</td><td class="text-right">0.000</td></tr>
        <tr><td>Vendor Payment</td><td class="text-right">0.000</td></tr>
        <tr><td>Vendor Payment (Cash)</td><td class="text-right">0.000</td></tr>
        <tr><td>Total Cash Collections</td><td class="text-right">126.298</td></tr>
        <tr><td>Staff Bank In Amount</td><td class="text-right">0.000</td></tr>
        <tr><td>Staff Bank Out Amount</td><td class="text-right">0.000</td></tr>
        <tr><td>Sales Commission Payments (Cash)</td><td class="text-right">0.000</td></tr>
        <tr><td>Net Total Cash in Register</td><td class="text-right">126.298</td></tr>
        <tr><td>Cashier-Out</td><td class="text-right">126.298</td></tr>
        <tr><td>Cash Over/Short</td><td class="text-right">0.000</td></tr>
        <tr><td>Delivery Cash Short</td><td class="text-right">0.000</td></tr>
    </table>

    <div class="main_heading">B. Sales Total</div>
    <table width="100%" class="pos_table">
        <tr><td>Net Sales</td><td class="text-right">637.823</td></tr>
        <tr><td>Item Discount</td><td class="text-right">1.552</td></tr>
        <tr><td>Tax</td><td class="text-right">7.690</td></tr>
        <tr><td>Delivery Charge</td><td class="text-right">0.000</td></tr>
        <tr><td>Table Charge</td><td class="text-right">0.000</td></tr>
        <tr><td>Round Off</td><td class="text-right">0.000</td></tr>
        <tr><td><b>Total</b></td><td class="text-right total_amount">645.384</td></tr>
    </table>

    <div class="main_heading">C. Tendered from Sales</div>
    <table width="100%" class="pos_table">
        <tr><th>Pay Type</th><th>Count</th><th>Amount</th></tr>
        <tr><td>Cash</td><td class="text-right">18</td><td class="text-right">126.298</td></tr>
        <tr><td>Visa</td><td class="text-right">68</td><td class="text-right">507.786</td></tr>
        <tr><td>Master Card</td><td class="text-right">2</td><td class="text-right">11.300</td></tr>
    </table>

    <div class="main_heading">D. Sales By Service Provider</div>
    <table width="100%" class="pos_table">
        <tr><td>Total Paid on Delivery</td><td class="text-right">0.000</td></tr>
        <tr><td>Total Paid Online by Credit Card</td><td class="text-right">0.000</td></tr>
        <tr><td>Total Paid Online by Debit Card</td><td class="text-right">0.000</td></tr>
        <tr><td>Total Paid by Cash</td><td class="text-right">0.000</td></tr>
    </table>

    <div class="main_heading">Other Activity</div>
    <table width="100%" class="pos_table">
        <tr><td>Gratuity (Tip Adjustments)</td><td class="text-right">0.000</td></tr>
        <tr><td>Tip Total</td><td class="text-right">(0.000)</td></tr>
        <tr><td>Refund Total</td><td class="text-right">0.000</td></tr>
        <tr><td>Void Total</td><td class="text-right">0.000</td></tr>
        <tr><td>Cancelled Total</td><td class="text-right">0.000</td></tr>
        <tr><td>House Total</td><td class="text-right">0.000</td></tr>
        <tr><td>Catering Collected Amount</td><td class="text-right">0.000</td></tr>
        <tr><td>Commission Payments (Cash)</td><td class="text-right">0.000</td></tr>
        <tr><td>Commission Payments (Card)</td><td class="text-right">0.000</td></tr>
        <tr><td>Commission Payments (Check)</td><td class="text-right">0.000</td></tr>
    </table>

    <table width="100%" class="footer_table" style="border-top: 2px solid #000; margin-top: 10px;">
        <tr><td>Operator: Jane Admin</td></tr>
        <tr><td>Software Developed By: <strong>Royalsoft</strong></td></tr>
        <tr><td>Print Date & Time: {{ date('d-m-Y h:i:s') }}</td></tr>
    </table>
</div>
@endsection
