<div class="stg_breadcrumb">
    <input type="hidden" name="current_flow_id" value="{{isset($data['stg']['flows']['current']->stg_flows_id)?$data['stg']['flows']['current']->stg_flows_id:$data['stg']['flows']['current']}}">
    <input type="hidden" name="next_flow_id" value="{{isset($data['stg']['flows']['next']->stg_flows_id)?$data['stg']['flows']['next']->stg_flows_id:$data['stg']['flows']['next']}}">
    @php $stg = true; @endphp
    @foreach($data['stg']['flows']['all'] as $flows)
        @php
            $current_stg_id = $data['stg']['flows']['current']->stg_flows_id;
            if($data['stg']['staging_apply'] == 1){
                $current_stg_id = $flows->stg_flows_id;
            }
        @endphp
        <span class="stg_breadcrumb__step {{ $current_stg_id == $flows->stg_flows_id ?" stg_breadcrumb__step--active":"" }}">{{$flows->stg_flows_name}}</span>
    @endforeach
</div>
