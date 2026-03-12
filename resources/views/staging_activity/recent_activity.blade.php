<div class="row">
    <div class="col-lg-12">
        <h5>Activity log</h5>
        <div class="stg-timeline kt-timeline-v2 kt-margin-t-10">
            <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                @if(isset($data['flow_dtls']['current']))
                    @php
                        $currentFlowName = is_object($data['flow_dtls']['current']) ? $data['flow_dtls']['current']->stg_flows_name : 'Unknown';
                    @endphp
                    <div class="kt-timeline-v2__item">
                        <span class="kt-timeline-v2__item-time">{{date("d-m-Y | h:ia")}}</span>
                        <div class="kt-timeline-v2__item-cricle">
                            <i class="fa fa-genderless kt-font-danger"></i>
                        </div>
                        <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                            <span style="color: #ffb822;font-weight: 400;">Current Working:</span> {{$currentFlowName}}  of <b>{{\Illuminate\Support\Facades\Auth::user()->name}} </b>
                        </div>
                    </div>
                @endif
                @if(isset($current_stg_activities))
                    @foreach($current_stg_activities as $activity)
                        <div class="kt-timeline-v2__item">
                            <span class="kt-timeline-v2__item-time">{{date("d-m-Y | h:ia",strtotime($activity->created_at))}}</span>
                            <div class="kt-timeline-v2__item-cricle">
                                <i class="fa fa-genderless kt-font-danger"></i>
                            </div>
                            <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                @php
                                    $flowName = optional($activity->flow_criteria_flow)->flow_name ?? optional($activity->flow_dtl)->stg_flows_name ?? null;
                                    $actionName = optional($activity->criteria_action)->action_name ?? optional($activity->action_btn_dtl)->stg_actions_name ?? null;
                                    if (!$flowName) {
                                        $flowName = $activity->stg_flows_id ? 'Stage ' . $activity->stg_flows_id : 'Unknown';
                                    }
                                    if (!$actionName) {
                                        $actionName = $activity->stg_actions_id ? 'Action' : 'Save';
                                    }
                                @endphp
                                <span style="color: #5d78ff;font-weight: 400;">{{ $flowName }}: </span> {{ $actionName }} By  <b> {{ $activity->user ? $activity->user->name : 'Unknown' }} </b>
                                    <span class="badge badge-info badge-pill ml-1">{{ $actionName }}</span>
                                @if($activity->remarks)
                                    <br><b>Remarks:</b> {{$activity->remarks}}
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
