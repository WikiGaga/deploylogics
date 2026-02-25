@extends('layouts.layout')
{{--@section('title', 'Page Title')--}}
@section('pageCSS')
    <link href="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
 
@endsection

@section('content')
   <style>
                /* Fix the event container */
        .fc-timeline-event {
            padding: 2px 4px !important;
            border: none !important;
            display: flex !important;
            align-items: center !important; /* Centers text vertically */
            justify-content: center !important;
            text-align: center;
        }

        /* Ensure the title container allows for multiple lines */
        .fc-content {
            line-height: 1.2 !important;
            white-space: normal !important; /* Allows text to wrap if box is narrow */
        }

        /* Fix the dots/icons if they are pushing text away */
        .fc-day-grid-event .fc-content:before, .fc-event-dot {
            display: none !important; /* Removes that white dot if you don't want it */
        }
    </style>
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){

        }
    @endphp
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">

                  <form id="filterForm">
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Shift Type:</label>
                            <select id="type" class="form-control">
                                <option value="morning">Morning</option>
                                <option value="night">Night</option>
                                <option value="leave">Leave</option>
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Branch:</label>
                            <select class="form-control filter_emp kt-select2" multiple name="branch[]" id="branch">
                                <option value="0">Select</option>
                                @foreach($data['branch'] as $branch)
                                    <option value="{{$branch->branch_id}}">
                                        {{ucfirst(strtolower($branch->branch_name))}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Department :</label>
                            <select class="form-control filter_emp kt-select2" name="department" id="department">
                                <option value="0">Select</option>
                                @foreach($data['department'] as $department)
                                    <option value="{{$department->department_id}}">
                                        {{ucfirst(strtolower($department->department_name))}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 d-flex align-items-end">
                            <button type="button" class="btn btn-warning btn-sm w-100" data-toggle="modal" data-target="#bulkOffModal">
                                Bulk Assign
                            </button>
                        </div>
                    </div>
                </form>
                <br>
             

                    <!-- //////////// -->

                    <div class="modal fade" id="editShiftModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Shift</h5>
                                    <button type="button" class="close" data-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="edit_event_id">
                                    <!-- <div class="form-group">
                                        <label>Shift Type</label>
                                        <select id="edit_type" class="form-control">
                                            <option value="morning">Morning</option>
                                            <option value="night">Night</option>
                                            <option value="leave">Leave</option>
                                        </select>
                                    </div> -->
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="time" id="edit_start_time" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="time" id="edit_end_time" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" id="btnDeleteShift">Delete</button>
                                    <button type="button" class="btn btn-primary" id="btnUpdateShift">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- //////////////////? -->

                    <div class="modal fade" id="bulkOffModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Bulk Assign Days</h5>
                                    <button type="button" class="close" data-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Select Date Range</label>
                                        <div class="row">
                                            <div class="col-6"><input type="date" id="bulk_start" class="form-control"></div>
                                            <div class="col-6"><input type="date" id="bulk_end" class="form-control"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Select Week Day:</label><br>
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="0"> Sun</label>
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="1"> Mon</label>
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="2"> Tue</label>
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="3"> Wed</label>
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="4"> Thu</label>
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="5"> Fri</label>
                                            <label class="btn btn-outline-primary btn-sm"><input type="checkbox" class="off-days" value="6"> Sat</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Shift Type</label>
                                        <select id="bulk_type" class="form-control">
                                            <option value="leave">Leave / Off</option>
                                            <option value="morning">Morning</option>
                                            <option value="night">Night</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" id="btnSubmitBulk">Apply Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="calendar"></div>
                </div>
            </div>
        </div>

@endsection

@section('pageJS')
@endsection
@section('customJS')
  

    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/fullcalendar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/fullcalendar.print.css') }}" media="print" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/scheduler/scheduler.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/shiftSchedule.css') }}?v={{ filemtime(public_path('assets/plugins/fullcalendar/shiftSchedule.css')) }}" />
    <script src="{{ asset('assets/plugins/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/plugins/scheduler/scheduler.js') }}"></script>
    
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
       
    $(document).ready(function() {
        $('#filterForm').validate(); 

        // 2. Initialize Select2
        $('.kt-select2').select2();

        $('.filter_emp').on('change', function() {
            $('#calendar').fullCalendar('refetchResources');

            // SAFE VALIDATION CHECK
            // We check if the validator exists for this form before calling .valid()
            if ($(this).closest('form').data('validator')) {
                $(this).valid();
            }
        });

        $.validator.setDefaults({
            ignore: [] // This tells the plugin to validate hidden Select2 elements
        });

        $('#calendar').fullCalendar({
            timezone: 'local',
            nextDayThreshold: '00:00:01', //extend box after this time
            firstDay: 0, // fist day of week

            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'customWeek,timelineMonth'
            },
            // Fix for arrows (using standard text if icons fail)
            buttonText: {
                prev: '<',
                next: '>'
            },
            defaultView: 'customWeek',
        
            views: {
                customWeek: {
                    type: 'timeline',
                    duration: { weeks: 1 },
                    slotDuration: { days: 1 },
                    // INCREASE THIS: default is usually 25, try 45 or 50
                    eventHeight: 50, 
                    buttonText: 'Week'
                }
            },
            resourceAreaWidth: '15%',
            resourceColumns: [{ labelText: 'Employees', field: 'title' }],
            // resources: {
            //     url: '/employees',
            //     type: 'GET',
            //     data: function() {
            //         return {
            //             branch_id: $('#branch').val(), 
            //             department_id: $('#department').val()
            //         };
            //     }
            // },
            events: '/shifts',

            selectable: true,
            editable: true,
            eventDurationEditable: false, // Prevents dragging to 4 days

            resources: function(callback) {
                $.ajax({
                    url: '/employees',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        branch_id: $('#branch').val(),
                        department_id: $('#department').val()
                    },
                    success: function(response) {
                        // response must be an array of resource objects
                        callback(response);
                    },
                    error: function() {
                        console.error("Could not load resources.");
                    }
                });
            },

            // Use dayClick for single clicks on the grid
            dayClick: function(date, jsEvent, view, resource) {
                
                if (!resource) {
                    console.log("No resource (employee) found for this row.");
                    return;
                }

                let shiftType = $('#type').val();
                let dateStr = date.format('YYYY-MM-DD');
                let startTime, endTime, endDateStr;
                endDateStr = dateStr;

                // Logic for times
                if (shiftType === 'morning') {
                    startTime = '08:00:00';
                    endTime = '16:00:00';
                } else if (shiftType === 'night') {
                    startTime = '22:00:00';
                    endTime = '06:00:00';
                    endDateStr = date.clone().add(1, 'days').format('YYYY-MM-DD');
                } else {
                    startTime = '00:00:00';
                    endTime = '23:59:59';
                }

                $.ajax({
                    url: '/shifts',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: resource.id,
                        shift_type: shiftType,
                        start: dateStr + ' ' + startTime,
                        end: endDateStr + ' ' + endTime
                    },
                    success: function(response) {
                        $('#calendar').fullCalendar('refetchEvents');
                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.status + " Check console.");
                    }
                });
            },
            

            eventRender: function(event, element) {
                // Clear the default content and structure it with a smaller font
                element.find('.fc-content').html(
                    '<div style="font-weight:bold; font-size: 11px;">' + event.title + '</div>' +
                    '<div style="font-size: 10px;">(' + moment(event.start).format('h:mm A')+'<br> '+ moment(event.end).format('h:mm A') + ')</div>'
                );
            },
            //     // Existing eventDrop, eventResize, and eventClick logic...
            eventDrop: function(event) {

                $.ajax({
                    url: '/shifts/' + event.id,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: event.id,
                        start: event.start.format(),
                        end: event.end ? event.end.format() : event.start.format()
                    },
                    success: function() {
                        $('#calendar').fullCalendar('refetchEvents');
                    }
                });

            },
            
            eventClick: function(event) {
                // Fill the modal with existing shift data
                $('#edit_event_id').val(event.id);
                $('#edit_type').val(event.shift_type); // Ensure your event object has shift_type
                $('#edit_start_time').val(event.start.format('HH:mm'));
                $('#edit_end_time').val(event.end ? event.end.format('HH:mm') : '');
                
                $('#editShiftModal').modal('show');
            },
            
        });

        $('#btnUpdateShift').click(function() {
            let id = $('#edit_event_id').val();
            let startTime = $('#edit_start_time').val();
            let endTime = $('#edit_end_time').val();
            let type = $('#edit_type').val();

            // Get the original date from the calendar event to keep the date same
            let event = $('#calendar').fullCalendar('clientEvents', id)[0];
            let dateStr = event.start.format('YYYY-MM-DD');

            $.ajax({
                url: '/shifts/' + id,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    shift_type: type,
                    start: dateStr + ' ' + startTime,
                    end: dateStr + ' ' + endTime
                },
                success: function() {
                    $('#editShiftModal').modal('hide');
                    $('#calendar').fullCalendar('refetchEvents');
                }
            });
        });

        // 2. DELETE SHIFT (Moved from eventClick to here)
        $('#btnDeleteShift').click(function() {
            let id = $('#edit_event_id').val();
            if(confirm("Are you sure you want to delete this shift?")) {
                $.ajax({
                    url: '/shifts/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        $('#editShiftModal').modal('hide');
                        $('#calendar').fullCalendar('refetchEvents');
                    }
                });
            }
        });

        $('#btnSubmitBulk').click(function() {
            let resources = $('#calendar').fullCalendar('getResources');
            let employeeIds = resources.map(r => r.id);
            
            // Get checked days (0 for Sunday, 6 for Saturday)
            let selectedDays = [];
            $('.off-days:checked').each(function() {
                selectedDays.push($(this).val());
            });

            if (selectedDays.length === 0) {
                alert("Please select at least one day of the week.");
                return;
            }

            $.ajax({
                url: '/shifts/bulk-store',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    employee_ids: employeeIds,
                    start_date: $('#bulk_start').val(),
                    end_date: $('#bulk_end').val(),
                    selected_days: selectedDays, // Array of days
                    shift_type: $('#bulk_type').val()
                },
                success: function(response) {
                    $('#bulkOffModal').modal('hide');
                    $('#calendar').fullCalendar('refetchEvents');
                    alert("Process Completed!");
                }
            });
        });
    });
 
       

</script>
    

@endsection






