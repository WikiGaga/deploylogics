@extends('layouts.template')
@section('title', 'Loan Configuration')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['loan_code'];
            $date =  date('d-m-Y');
            $loan_type = 0;
            $occurence_type=0;
            $allowance_type=0;
            $user_id = Auth::user()->id;
        }
        if($case == 'edit'){
        $id = $data['current']->loan_configuration_id;
        $description = $data['current']->description;
        $loan_type = $data['current']->loan_type;
        $occurence_type = $data['current']->occurence_type;
        $occurence_type = $data['current']->occurence_type;
      
        $minimum_installment = $data['current']->minimum_installment;
        $maximum_installment = $data['current']->maximum_installment;
        $allowance_type=$data['current']->allowance;

        $rate_type_allowance = $data['current']->rate_type;
        $rate_value_allowance = $data['current']->rate_value;
        $minimun_value = $data['current']->minimum_value;
        $maximum_value = $data['current']->maximum_value;
        $employer_contribution = $data['current']->employee_contribution;
        $rate_type_contribution = $data['current']->employee_rate_type;
        $rate_value_contribution = $data['current']->employee_rate_value;
        $apply_on_loan = $data['current']->apply_on_loan;

        }
    @endphp
    {{-- @permission($data['permission']) --}}
    <form id="loan_configuration_form" class="kt-form" method="post" action="{{ action('PayrDepartment\LoanConfigurationController@store', isset($id)?$id:'') }}">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{isset($code)?$code:""}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row" style="margin-bottom: 10px">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-2 erp-col-form-label">Descripiton:</label>
                                    <div class="col-lg-10" >
                                        <input type="text" name="description" maxlength="255" class="form-control erp-form-control-sm moveIndex" value="{{isset($description)?$description:''}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Loan Type:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="loan_type" name="loan_type">
                                                <option value="0">Select</option>                                      
                                                @foreach($data['loan_type'] as $loan)
                                                    <option value="{{$loan->advance_type_id}}" {{ $loan_type == $loan->advance_type_id?"selected":""}}>{{ucfirst(strtolower($loan->advance_type_name))}}</option>
                                                @endforeach
                                             
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Occurrence Type:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="occurence_type" name="occurence_type">
                                                <option value="0">Select</option>
                                                <option value="monthly" {{$occurence_type=='monthly'?"selected":""}}>Monthly</option>
                                                <option value="once" {{$occurence_type=='once'?"selected":""}}>Once</option>
                                                <option value="salary_base" {{$occurence_type=='salary_base'?"selected":""}}>Salary Base</option>
                                                <option value="not_deduction" {{$occurence_type=='not_deduction'?"selected":""}}>Not Deduction</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Minimum installments:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="min_installment" value="{{isset($minimum_installment)?$minimum_installment:""}}" class="moveIndex form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                        <label class="col-lg-4 erp-col-form-label">Maximum installments:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="max_installment" value="{{isset($maximum_installment)?$maximum_installment:""}}" class="moveIndex form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block" style="margin-top: 15px">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Apply To Allowance:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="allowance_type" name="allowance_type">
                                                <option value="0">Select</option>
                                                @foreach($data['allowance_type'] as $allowance)
                                                <option value="{{$allowance->allowance_deduction_id}}" {{ $allowance_type== $allowance->allowance_deduction_id?"selected":""}}>{{ucfirst(strtolower($allowance->allowance_deduction_name))}}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Rate Type:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rate_type_allowance" name="rate_type_allowance">
                                                @php $rate_type_allowance = isset($rate_type_allowance) ? $rate_type_allowance : 0 ; @endphp
                                                <option value="0"  @if($rate_type_allowance == "0") selected @endif>Select</option>
                                                <option value="percent"  @if($rate_type_allowance == "percent") selected @endif>Percent</option>
                                                <option value="fix"  @if($rate_type_allowance == "fix") selected @endif>Fix</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Rate Value:</label>
                                    <div class="col-lg-8">
                                        @php $rate_value_allowance = isset($rate_value_allowance) ? $rate_value_allowance : '' ; @endphp
                                        <input type="text" class="form-control moveIndex form-control erp-form-control-sm" name="rate_value_allowance" value="{{ $rate_value_allowance }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Minimum Value:</label>
                                    <div class="col-lg-8">
                                        @php $minimun_value = isset($minimun_value) ? $minimun_value : '' ; @endphp
                                        <input type="text" name="min_value" id="min_value" class="moveIndex form-control erp-form-control-sm" value="{{ $minimun_value }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Maximum Value:</label>
                                    <div class="col-lg-8">
                                        @php $maximum_value = isset($maximum_value) ? $maximum_value : '' ; @endphp
                                        <input type="text" name="max_value" id="max_value" class="moveIndex form-control erp-form-control-sm" value="{{ $maximum_value }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block" style="margin-top: 15px">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Employer Contribution:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            @php $employer_contribution = isset($employer_contribution) ? $employer_contribution : 0 ; @endphp
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="employer_contribution" name="employer_contribution">
                                                <option value="0">Select</option>
                                                @foreach($data['allowance_type'] as $allowance)
                                                    <option value="{{ $allowance->allowance_deduction_id }}" @if($employer_contribution == $allowance->allowance_deduction_id) selected @endif>{{ $allowance->allowance_deduction_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Rate Type:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            @php $rate_type_contribution = isset($rate_type_contribution) ? $rate_type_contribution : 0 ; @endphp
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rate_type_contribution" name="rate_type_contribution">
                                                <option value="0" @if($rate_type_contribution == "0") selected @endif>Select</option>
                                                <option value="percent" @if($rate_type_contribution == "percent") selected @endif>Percent</option>
                                                <option value="fix" @if($rate_type_contribution == "fix") selected @endif>Fix</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Rate Value:</label>
                                    <div class="col-lg-8">
                                        @php $rate_value_contribution = isset($rate_value_contribution) ? $rate_value_contribution : '';  @endphp
                                        <input type="text" class="form-control moveIndex form-control erp-form-control-sm" name="rate_value_contribution" value="{{ $rate_value_contribution }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Apply on Loan:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                <label>
                                                    @php $apply_on_loan = isset($apply_on_loan) ? $apply_on_loan : 'off' ; @endphp
                                                    <input type="checkbox" id="apply_on_loan" @if($apply_on_loan == "on") checked @endif name="apply_on_loan" autocomplete="off">
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- ./kt-portlet__body --}}
                </div>
            </div>
            @include('common.criteria')
        </div>
    </form>
                <!--end::Form-->
    {{-- @endpermission --}}
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/payr-department/loan-configuration.js') }}" type="text/javascript"></script>
@endsection
