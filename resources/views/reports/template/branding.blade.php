<div class="kt-portlet__head-toolbar">
    <div class="kt-invoice__logo">
        <div>
            @php
                $logo = auth()->user()->branch->branch_logo;
                $path = !empty($logo) ? base_path()."/public/images/".$logo : null;
                if(!$path || !is_file($path)){
                    $path = base_path()."/public/assets/images/logo.png";
                }

                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data_img = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
            @endphp
            <img src="{{$base64}}" width="80px">
        </div>
        <div class="kt-invoice__desc">
            <div style="font-weight: 500;color: #000;margin-top: 10px;">{{strtoupper(auth()->user()->branch->branch_name)}}</div>
        </div>
    </div>
</div>
