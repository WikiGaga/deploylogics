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
                    <td class="title aligncenter" style="font-weight:normal; font-size:14px;">{{auth()->user()->branch->branch_name}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
