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
    <form {{--id="_form"--}} class="kt-form" method="post" action="{{--{{ action('Development\ListingStudioController@store',isset($id)?$id:'') }}--}}">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-3">

                            <!--begin::Portlet-->
                            <div class="kt-portlet" id="kt_portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
												<span class="kt-portlet__head-icon">
													<i class="flaticon-map-location"></i>
												</span>
                                        <h3 class="kt-portlet__head-title">
                                            Events ADF
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <div id="kt_calendar_external_events" class="fc-unthemed">
                                        <div event-id="1" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--primary kt-badge--inline kt-margin-b-15' data-color="fc-event-primary">Meeting</div><br>
                                        <div event-id="2" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--brand kt-badge--inline kt-margin-b-15' data-color="fc-event-brand">Conference Call</div><br>
                                        <div event-id="3" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--success kt-badge--inline kt-margin-b-15' data-color="fc-event-success">Dinner</div><br>
                                        <div event-id="4" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--warning kt-badge--inline kt-margin-b-15' data-color="fc-event-warning">Product Launch</div><br>
                                        <div event-id="5" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--danger kt-badge--inline kt-margin-b-15' data-color="fc-event-danger">Reporting</div><br>
                                        <div event-id="6" class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                                        <div event-id="7" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--success kt-badge--inline kt-margin-b-15' data-color="fc-event-success">Project Update</div><br>
                                        <div event-id="8" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--info kt-badge--inline kt-margin-b-15' data-color="fc-event-info">Staff Meeting</div><br>
                                        <div event-id="9" class='fc-draggable-handle kt-badge kt-badge--lg kt-badge--dark kt-badge--inline kt-margin-b-15' data-color="fc-event-dark">Lunch</div>
                                        <div event-id="10" class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                                        <div>
                                            <label class="kt-checkbox kt-checkbox--brand">
                                                <input type="checkbox" id='kt_calendar_external_events_remove'> Remove after drop
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        <div class="col-lg-9">

                            <!--begin::Portlet-->
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
												<span class="kt-portlet__head-icon">
													<i class="flaticon-map-location"></i>
												</span>
                                        <h3 class="kt-portlet__head-title">
                                            My Events
                                        </h3>
                                    </div>
                                    <div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-group">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-elevate btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-plus"></i> Add Event
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Action</a>
                                                    <a class="dropdown-item" href="#">Another action</a>
                                                    <a class="dropdown-item" href="#">Something else here</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#">Separated link</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <div id="kt_calendar"></div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('pageJS')
@endsection
@section('customJS')
    <script src="{{--{{ asset('js/pages/js/master-form.js') }}--}}" type="text/javascript"></script>

    <!--begin::Page Vendors(used by this page) -->
    <script src="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js" type="text/javascript"></script>

    <!--end::Page Vendors -->

    <!--begin::Page Scripts(used by this page) -->
    <script src="/assets/js/pages/components/calendar/external-events.js" type="text/javascript"></script>

    <!--end::Page Scripts -->

    <script>

    </script>
@endsection
