
<div class="row">
    <div class="col-lg-12">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <table class="table table-striped">
                <thead>
                  <tr>
                    <td scope="col" align="center">Sr#</td>
                    <td scope="col" align="left">Branch Name</td>
                    <td scope="col" align="left">Description</td>
                    <td scope="col" align="center">Sync Last Date Time</td>
                    <td scope="col" align="center">Sync last Time Passed(Min)</td>
                  </tr>
                </thead>
                <tbody>
                @php
                    $i=1;
                @endphp
                @foreach ($data['sync_data'] as $value) 
                    @php
                        date_default_timezone_set("Asia/Karachi");
                        $datetime_1 = date("Y-m-d h:i:s");
                        $datetime_2 = date('Y-m-d h:i:s', strtotime($value->entry_date_time));

                        $from_time = strtotime($datetime_1); 
                        $to_time = strtotime($datetime_2); 
                        $diff_minutes_adjusted = round(abs($from_time - $to_time) / 60,0);
                        
                        if($diff_minutes_adjusted >= 10){
                            $style = "color:red;font-weight:bold;font-size:14px;";
                        }else{
                            $style = "color:green;font-weight:bold;font-size:14px;";
                        }
                    @endphp
                    <tr>
                        <td scope="row" align="center">{{ $i }}</td>
                        <td align="left">{{ $value->branch_name }}</td>
                        <td align="left">{{ $value->description }}</td>
                        <td align="center">{{ date('d-m-Y h:i:s A', strtotime($value->entry_date_time)) }}</td>
                        <td align="center" scope="row" style="{{$style}}" >
                            @if($diff_minutes_adjusted >= 10)
                                @php
                                    $hours = floor($diff_minutes_adjusted / 60);
                                    $min = $diff_minutes_adjusted - ($hours * 60);

                                    if($diff_minutes_adjusted >= 60)
                                    {
                                        $minutes = $hours.":".$min;
                                    }else{
                                        $minutes = $diff_minutes_adjusted;
                                    }
                                    

                                    $base64 = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
                                    $base64 .= "://".$_SERVER['HTTP_HOST']."/assets/mp3/GhabranaNahiHai.mp3";
                                    //$base64 .= "://".$_SERVER['HTTP_HOST']."/assets/mp3/program_to_war_giya.mp3";
                                @endphp
                                <script>
                                    var mp3_url = '{{$base64}}';
                                    (new Audio(mp3_url)).play();
                                </script>
                                {{ $minutes }}
                            @else
                                {{ $diff_minutes_adjusted }}
                            @endif
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach

                </tbody>
              </table>
        </div>
    </div>
</div>
