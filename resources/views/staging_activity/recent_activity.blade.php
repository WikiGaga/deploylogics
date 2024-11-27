<div class="row">
    <div class="col-lg-12">
        <hr>
        <h5>Activity</h5>
        <div class="stg-timeline kt-timeline-v2 kt-margin-t-10">
            <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                <div class="kt-timeline-v2__item">
                    <span class="kt-timeline-v2__item-time">{{date("d-m-Y | h:ia")}}</span>
                    <div class="kt-timeline-v2__item-cricle">
                        <i class="fa fa-genderless kt-font-danger"></i>
                    </div>
                    <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                        <span style="color: #ffb822;font-weight: 400;">Current Working::</span>  {{$data['stg']['flows']['current']->stg_flows_name}}  <b>{{\Illuminate\Support\Facades\Auth::user()->name}} </b>
                    </div>
                </div>
                @if(isset($data['stg']['activity']))
                    @foreach($data['stg']['activity'] as $activity)
                        <div class="kt-timeline-v2__item">
                            <span class="kt-timeline-v2__item-time">{{date("d-m-Y | h:ia",strtotime($activity->created_at))}}</span>
                            <div class="kt-timeline-v2__item-cricle">
                                <i class="fa fa-genderless kt-font-danger"></i>
                            </div>
                            <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                <span style="color: #5d78ff;font-weight: 400;">{{$activity->flow_dtl->stg_flows_name}}:: </span>   <b> {{$activity->user->name}} </b> {{$activity->action_btn_dtl->stg_actions_name}}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
