{{-- <div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
    <div class="row">
        <div class="col-lg-6">
            <div class="date text-left">Powered by <span>Royalsoft</span></div>
        </div>
        <div class="col-lg-6 kt-align-right">
            <div class="date"><span>Date: </span>{{ date('d-m-Y h:i:s A') }} - <span>User: </span>{{auth()->user()->name}}</div>
        </div>
    </div>
</div> --}}
<div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
    <table width="100%">
        <tbody>
        <tr style="border: 0px;">
            <td width="50%">
                <span style="color: #FE21BE !important;">Powered by</span> Royalsoft
            </td>
            <td width="50%" style="text-align:right;">Date: <span style="color: #FE21BE !important;">{{date("d-m-Y h:i:s A")}} - </span>User <span style="color: #FE21BE !important;">{{auth()->user()->name}}</span></td>
        </tr>
        </tbody>
    </table>
</div>

