<div class="row">
    <div class="col-lg-12">
        <h5>Activity log</h5>
        <div class="stg-timeline kt-timeline-v2 kt-margin-t-10">
            <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                @if(isset($data['flow_dtls']['current']))
                    @php
                        $currentFlowName = is_object($data['flow_dtls']['current']) ? $data['flow_dtls']['current']->stg_flows_name : 'Unknown';
                        if (is_object($data['flow_dtls']['current']) && !empty($data['flow_dtls']['current']->flow_name)) {
                            $currentFlowName = $data['flow_dtls']['current']->flow_name;
                        }
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
                        @php
                            $flowName = $activity->display_flow_name;
                            $actionName = $activity->display_action_name;
                            $actionCode = $activity->display_action_code;
                            $badgeClass = 'badge-info';
                            if (in_array($actionCode, ['back'], true)) {
                                $badgeClass = 'badge-warning';
                            } elseif (in_array($actionCode, ['forward', 'post'], true)) {
                                $badgeClass = 'badge-primary';
                            } elseif (in_array($actionCode, ['create'], true)) {
                                $badgeClass = 'badge-success';
                            } elseif (in_array($actionCode, ['update', 'save', 'edit'], true)) {
                                $badgeClass = 'badge-info';
                            } elseif (in_array($actionCode, ['cancel'], true)) {
                                $badgeClass = 'badge-danger';
                            } elseif (in_array($actionCode, ['un_post', 'unpost'], true)) {
                                $badgeClass = 'badge-secondary';
                            }
                            $userRemarks = $activity->display_user_remarks;
                        @endphp
                        <div class="kt-timeline-v2__item">
                            <span class="kt-timeline-v2__item-time">{{date("d-m-Y | h:ia",strtotime($activity->created_at))}}</span>
                            <div class="kt-timeline-v2__item-cricle">
                                <i class="fa fa-genderless kt-font-danger"></i>
                            </div>
                            <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                <span style="color: #5d78ff;font-weight: 400;">{{ $flowName }}: </span> {{ $actionName }} By  <b> {{ $activity->user ? $activity->user->name : 'Unknown' }} </b>
                                <span class="badge {{ $badgeClass }} badge-pill ml-1">{{ $actionName }}</span>
                                @if($userRemarks)
                                    <br><b>Remarks:</b> {{ $userRemarks }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
