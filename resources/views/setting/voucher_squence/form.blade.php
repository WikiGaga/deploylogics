@extends('layouts.layout')
@section('title', 'Voucher Sequence')

@section('pageCSS')
<style>
.handle:hover{
    cursor:pointer;
}
</style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
        }
    @endphp
    @permission($data['permission']);
    <form id="form" class="voucher_sequance kt-form" method="post" action="{{ action('Setting\VoucherSequanceController@store') }}">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-6 offset-lg-3">
                                <table class="table table-bordered table-striped table-sm table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="10px">Sr No</th>
                                            <th width="40px">Voucher Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body">
                                        @if(isset($data['voucher_types']))
                                            @foreach($data['voucher_types'] as $types)
                                                <tr>
                                                    <td class="handle"><i class="fas fa-arrows-alt"></i>  {{$loop->iteration}}</td>
                                                    <td><input type="hidden" name="pd[{{$loop->iteration}}][type]" value="{{$types->voucher_type}}">{{strtoupper($types->voucher_type)}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">-->

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/setting/voucher-sequance.js') }}" type="text/javascript"></script>
    <script type="text/javascript">

       $("tbody").sortable({
        handle: ".handle",
        update: function (e, ui) {
            $( ".body>tr" ).each(function (index) {
                $(this).find(".handle").html('<i class="fas fa-arrows-alt"></i>  '+(index+1));
            });
        }
    });
    </script>
@endsection
