@extends('layouts.layout')
{{--@section('title', 'Page Title')--}}
@section('pageCSS')

    <style>
        .fc-timeline-event {
            padding: 2px 4px !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center;
        }
        .fc-content {
            line-height: 1.2 !important;
            white-space: normal !important;
        }
        .shift-title { font-weight: bold; font-size: 11px; }
        .shift-time { font-size: 10px; }
    </style>
@endsection

@section('content')
  
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $roaster_no = $data['roaster_no'];
        $roaster_id = $data['roaster_id'];

        if(empty($roaster_id)){
            $date =  date('d-m-Y');
        }else{
            $date = date('d-m-Y', strtotime($data['roaster_date']));
        }

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

                   <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$roaster_no}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-5 erp-col-form-label">Date:</label>
                                <div class="col-lg-7">
                                    <div class="input-group date">
                                        <input type="text" name="date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{ $date}}"  id="kt_datepicker_3" autofocus/>
                                        <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                            <select class="form-control filter_emp kt-select2" multiple name="department[]" id="department">
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
                                        <label>Select Week off Day:</label><br>
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
    <script src="{{ asset('assets/plugins/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/plugins/scheduler/scheduler.js') }}"></script>
    {{-- Add SweetAlert2 for better confirmations --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('customJS')

    <link href="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/fullcalendar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/fullcalendar.print.css') }}" media="print" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/scheduler/scheduler.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/shiftSchedule.css') }}?v={{ filemtime(public_path('assets/plugins/fullcalendar/shiftSchedule.css')) }}" />
    
  

  
    
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
       
 $(document).ready(function() {
    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
        }
    });

    // Initialize validation
    $('#filterForm').validate({
        ignore: [],
        errorClass: 'text-danger'
    });

    // Select2 init
    $('.kt-select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    // Filter change handler
    $('.filter_emp').on('change', function() {
        $('#calendar').fullCalendar('refetchResources');
        $('#calendar').fullCalendar('refetchEvents');
        
        const validator = $(this).closest('form').data('validator');
        if (validator) $(this).valid();
    });

    // Initialize Calendar
    const $calendar = $('#calendar');
    
    $calendar.fullCalendar({
          themeSystem: 'bootstrap4',
            // bootstrapFontAwesome: true,
            // refetchResourcesOnNavigate: true,
            // navLinks: true,
            // editable: true,
            // utc: true,
        timezone: 'local',
        // timezone: false, 
        // timezone: 'UTC',
        nextDayThreshold: '00:00:01',
        firstDay: 3,
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'customWeek,timelineMonth'
        },
        
        buttonText: {
            prev: '‹',
            next: '›',
            today: 'Today'
        },
        
        defaultView: 'customWeek',
        
        views: {
            customWeek: {
                type: 'timeline',
                duration: { weeks: 1 },
                slotDuration: { days: 1 },
                eventHeight: 50,
                buttonText: 'Week'
            }
        },
        
        resourceAreaWidth: '15%',
        resourceColumns: [{ 
            labelText: 'Employees', 
            field: 'title',
            width: '100%'
        }],
        
        // Resources with error handling
        resources: function(callback) {
            $.ajax({
                url: '/Employee-Roaster/employees',
                type: 'GET',
                dataType: 'json',
                data: {
                    branch_id: $('#branch').val(),
                    department_id: $('#department').val()
                },
                success: callback,
                error: function(xhr) {
                    toastr.error('Failed to load employees');
                    callback([]);
                }
            });
        },
        
        events: {
            url: '/Employee-Roaster/shifts',
            data: function() {
                return {
                    roaster_id:"{{ $roaster_id }}",
                    branch_id: $('#branch').val(),
                    department_id: $('#department').val()
                };
            },
            error: function() {
                toastr.error('Failed to load shifts');
            }
        },
        
        selectable: true,
        editable: true,
        eventDurationEditable: false,
        
        //// ADDED: Handle resize
        eventResize: function(event, delta, revertFunc) {
            updateShift(event, revertFunc);
        },
        
        //// ADDED: Better drop handling
        eventDrop: function(event, delta, revertFunc) {
            updateShift(event, revertFunc);
        },
        
        dayClick: function(date, jsEvent, view, resource) {
            if (!resource) return;
            
            createShift(resource.id, date);
        },
        
        eventClick: function(event) {
            openEditModal(event);
        },
        
        // eventRender: function(event, element) {
        //     const start = moment(event.start).format('h:mm A');
        //     const end = moment(event.end).format('h:mm A');
            
        //     element.find('.fc-content').html(`
        //         <div class="shift-title">${event.title}</div>
        //         <div class="shift-time">${start} - ${end}</div>
        //     `);
        // }

        // eventRender: function(event, element) {
        //     // .format() without the 'Z' ensures it shows exactly what is in the DB
        //     const start = moment(event.start).format('h:mm A');
        //     const end = moment(event.end).format('h:mm A');
        //      console.log(event.start,'ggggggggg',event)
        //     element.find('.fc-content').html(`
        //         <div class="shift-title" style="font-weight:bold;">${event.title}</div>
        //         <div class="shift-time">${start} - ${end}</div>
        //     `);
        // }

        eventRender: function(event, element) {

            const start = moment.parseZone(event.start).format('HH:mm');
            const end = moment.parseZone(event.end).format('HH:mm');

            element.find('.fc-content').html(`
                <div class="shift-title" style="font-weight:bold;">${event.title}</div>
                <div class="shift-time">${start} - ${end}</div>
            `);
        }
    });

    // Helper functions
    function createShift(employeeId, date) {
        const shiftType = $('#type').val();
        const dateStr = date.format('YYYY-MM-DD');
        
        const configs = {
            morning: { start: '08:00:00', end: '16:00:00', addDay: 0 },
            night: { start: '22:00:00', end: '06:00:00', addDay: 1 },
            leave: { start: '00:00:01', end: '23:59:59', addDay: 0 }
        };
        
        const cfg = configs[shiftType];
        const endDate = date.clone().add(cfg.addDay, 'days');
        
        $.ajax({
            url: '/Employee-Roaster/shifts',
            type: 'POST',
            data: {
                employee_id: employeeId,
                shift_type: shiftType,
                roaster_no: "{{ $roaster_no }}",
                roaster_id: "{{ $roaster_id }}",
                start: `${dateStr} ${cfg.start}`,
                end: `${endDate.format('YYYY-MM-DD')} ${cfg.end}`
            },
            success: (res) => {

                
              
                if(res.status === 'redirect'){
                    window.location = res.url;
                }

                if(res.status === 'duplicate'){
                    toastr.error(res.message);
                }else{
                    $calendar.fullCalendar('refetchEvents');
                    toastr.success('Shift created');
                }
                
                
            },
            error: (xhr) => {
                toastr.error(xhr.responseJSON?.message || 'Error creating shift');
            }
        });
    }

    function updateShift(event, revertFunc) {
        $.ajax({
            url: `/Employee-Roaster/shifts/${event.id}`,
            type: 'PUT',
            data: {
                id: event.id, // Ensure ID is passed
                start: event.start.format('YYYY-MM-DD HH:mm:ss'),
                end: event.end ? event.end.format('YYYY-MM-DD HH:mm:ss') : event.start.format('YYYY-MM-DD HH:mm:ss'),
                employee_id: event.resourceId // <--- ADD THIS LINE to capture the new employee
            },
            success: () => {
                toastr.success('Shift updated');
            },
            error: () => {
                revertFunc();
                toastr.error('Update failed');
            }
        });
    }

    function openEditModal(event) {
        $('#edit_event_id').val(event.id);
        $('#edit_start_time').val(event.start.format('HH:mm'));
        $('#edit_end_time').val(event.end ? event.end.format('HH:mm') : '');
        
        // Store original date
        $('#editShiftModal').data('originalDate', event.start.format('YYYY-MM-DD'));
        $('#editShiftModal').modal('show');
    }

    // Update handler
    $('#btnUpdateShift').click(function() {
        const id = $('#edit_event_id').val();
        const dateStr = $('#editShiftModal').data('originalDate');
        const startTime = $('#edit_start_time').val();
        const endTime = $('#edit_end_time').val();
        
        $.ajax({
            url: `/Employee-Roaster/shifts/${id}`,
            type: 'PUT',
            data: {
                start: `${dateStr} ${startTime}`,
                end: `${dateStr} ${endTime}`
            },
            success: () => {
                $('#editShiftModal').modal('hide');
                $calendar.fullCalendar('refetchEvents');
                toastr.success('Shift updated');
            },
            error: () => toastr.error('Update failed')
        });
    });

    // Delete handler
    $('#btnDeleteShift').click(function() {
        const id = $('#edit_event_id').val();
        
        Swal.fire({
            title: 'Delete shift?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/Employee-Roaster/shifts/${id}`,
                    type: 'DELETE',
                    success: () => {
                        $('#editShiftModal').modal('hide');
                        $calendar.fullCalendar('refetchEvents');
                        toastr.success('Shift deleted');
                    }
                });
            }
        });
    });

    // Bulk handler with validation
    $('#btnSubmitBulk').click(function() {
        const resources = $calendar.fullCalendar('getResources');
        if (!resources.length) {
            toastr.error('No employees loaded');
            return;
        }

        const employeeIds = resources.map(r => r.id);
        const selectedDays = $('.off-days:checked').map(function() {
            return parseInt(this.value);
        }).get();

        if (!selectedDays.length) {
            toastr.error('Select at least one day');
            return;
        }

        const startDate = $('#bulk_start').val();
        const endDate = $('#bulk_end').val();
        
        if (!startDate || !endDate) {
            toastr.error('Select date range');
            return;
        }

        $(this).prop('disabled', true).text('Processing...');

        $.ajax({
            url: '/Employee-Roaster/shifts/bulk-store',
            type: 'POST',
            data: {
                employee_ids: employeeIds,
                roaster_no: "{{ $roaster_no }}",
                roaster_id: "{{ $roaster_id }}",
                start_date: startDate,
                end_date: endDate,
                selected_days: selectedDays,
                shift_type: $('#bulk_type').val()
            },
            success: (response) => {
                $('#bulkOffModal').modal('hide');
                if(res.status === 'redirect'){
                    window.location = res.url;
                }
                $calendar.fullCalendar('refetchEvents');
                toastr.success(response.message);
                 
            },
            error: (xhr) => {
                toastr.error(xhr.responseJSON?.message || 'Bulk operation failed');
            },
            complete: () => {
                $(this).prop('disabled', false).text('Apply Changes');
            }
        });
    });
});  

</script>
    

@endsection






