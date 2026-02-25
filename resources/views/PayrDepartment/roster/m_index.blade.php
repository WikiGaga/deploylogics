@extends('layouts.layout')
{{--@section('title', 'Page Title')--}}
@section('pageCSS')
    <link href="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){

        }
    @endphp
   @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->
                <div class="form-group d-flex align-items-center gap-3 flex-wrap">
                    <select id="employee" class="form-control" style="width: 220px; min-width: 180px;">
                        @foreach($employees as $e)
                            <option value="{{$e->employee_id}}">{{$e->employee_name}}</option>
                        @endforeach
                    </select>

                    <select id="type" class="form-control" style="width: 140px;">
                        <option value="morning">Morning</option>
                        <option value="night">Night</option>
                        <option value="leave">Leave</option>
                    </select>
                </div>

                    <div id="calendar"></div>
                </div>
            </div>
        </div>
@endsection

@section('pageJS')
@endsection
@section('customJS')
    <!-- <script src="{{--{{ asset('js/pages/js/master-form.js') }}--}}" type="text/javascript"></script> -->

    <!--begin::Page Vendors(used by this page) -->
    <!-- <script src="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js" type="text/javascript"></script> -->

    <!--end::Page Vendors -->

    <!--begin::Page Scripts(used by this page) -->
    <!-- <script src="/assets/js/pages/components/calendar/external-events.js" type="text/javascript"></script> -->

    <!--end::Page Scripts -->

     <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
       
        document.addEventListener('DOMContentLoaded', function() {

            let calendar=new FullCalendar.Calendar(document.getElementById('calendar'),{
                initialView:'timeGridWeek',
                editable:true,
                selectable:true,
                events:function(fetchInfo,success){
                    let emp=document.getElementById('employee').value;
                    fetch('/m_shifts/'+emp)
                    .then(r=>r.json())
                    .then(data=>success(data));
                },


                select:function(info){

                    fetch('/m_shifts',{
                        method:'POST',
                        headers:{
                            'Content-Type':'application/json',
                            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                        },
                        body:JSON.stringify({
                            employee_id:document.getElementById('employee').value,
                            shift_type:document.getElementById('type').value,
                            start:info.startStr,
                            end:info.endStr
                        })
                    }).then(()=>calendar.refetchEvents());
                },

                eventDrop:function(info){
                    fetch('/m_shifts/'+info.event.id,{
                        method:'PUT',
                        headers:{
                            'Content-Type':'application/json',
                            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                        },
                        body:JSON.stringify({
                            start:info.event.start.toISOString(),
                            end:info.event.end.toISOString()
                        })
                    }).then(()=>calendar.refetchEvents());
                },


                eventClick:function(info){
                    if(confirm("Delete shift?")){
                        fetch('/m_shifts/'+info.event.id,{
                            method:'DELETE',
                            headers:{
                                'Content-Type':'application/json',
                                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(res => res.json())
                        .then(()=>calendar.refetchEvents());
                    }
                }

            });

            calendar.render();

            document.getElementById('employee').addEventListener('change',function(){
                calendar.refetchEvents();
            });

        });

    </script>
    

@endsection