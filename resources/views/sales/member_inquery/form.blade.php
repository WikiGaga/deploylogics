@extends('layouts.template')
@section('title', 'Member Inquiry')

@section('pageCSS')
    <link href="/assets/css/pages/support-center/home-1.css" rel="stylesheet" type="text/css" />
@endsection
<style>
    .erp-card{
        height:185px;
        box-shadow:rgba(82, 63, 105, 0.05) 0px 0px 30px 0px;
        display:flex;
        flex-direction:column;
        position:relative;
        margin-bottom: 20px;
    }
    .erp-card-search {
        height: 185px;
        width: 70% !important;
        padding-left: 65px;
        padding-right: 0px;
        padding-top: 20px;
    }
    .erp-card-search>h1 {
        font-size: 26px;
        font-weight: 600 !important;
    }
    .erp-card-search>.font-size-h4.mb-8 {
        font-size: 17.55px;
        font-weight: 400;
        margin-bottom: 26px;
        overflow-wrap: break-word;
    }
    .erp-bg-cover {
        width: 30% !important;
        background-position-x: 100%;
        background-position-y: -12px;
        background-size: 195px;
        background-repeat: no-repeat;
    }
    .erp-card-search-input-group {
        height: 45px;
        align-items: center;
        background-color: rgb(255, 255, 255);
        border-radius: 5.46px;
        color: rgb(63, 66, 84);
        display: flex;
        font-size: 13px;
        font-weight: 400;
        padding: 6.5px 19.5px;
    }
</style>
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $date =  date('d-m-Y');
        }
    @endphp
    <div class="col-lg-12">
        <div class="erp-card">
            <div class="erp-card-body rounded kt-padding-0 d-flex bg-light">
                <div class="erp-card-search">
                    <h1 class="font-weight-bolder text-dark mb-0">Member Inquiry</h1>
                    <div class="font-size-h4 mb-8"></div>
                    <!--begin::Form-->
                    <div class="erp-card-search-input-group">
                        <span class="svg-icon svg-icon-lg svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                                </g>
                            </svg>
                        </span>
                        <input type="text" id="SearchCard" class="erp-card-search-input form-control border-0" placeholder="Enter Card Number">
                    </div>
                    <br>
                    <button type="button" class="moveIndex btn btn-sm btn-primary" id="get_data">Get Data</button>
                </div>
                <div class="erp-bg-cover" style="background-image: url(/assets/media/custom/copy.svg);"></div>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div id="data_re_order_stock" class="form-group row">
                
                <div class="col-lg-12">
                    <table width="100%" class="table erp_form__grid">
                        <tr>
                            <td colspan="2">
                                <strong style="color:orange;font-size:24px;">
                                    <span id="customer_name"></span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td width="15%">
                                <strong>Type: </strong>
                            </td>
                            <td width="84%">
                                <strong style="color:chocolate;font-size:14px;">
                                    <span id="card_type"></span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Address: </strong>
                            </td>
                            <td>
                                <strong style="color:chocolate;font-size:14px;">
                                    <span id="address"></span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Expiry Date: </strong>
                            </td>
                            <td>
                                <strong style="color:chocolate;font-size:14px;">
                                    <span id="exp_date"></span>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Balance Points: </strong>
                            </td>
                            <td>
                                <strong style="color:chocolate;font-size:14px;">
                                    <span id="bal_point"></span>
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection
@section('customJS')
    <script>
      
        var xhrGetDataStatus = true;
        $(document).on('click','#get_data',function(){
            var validate = true;
            var SearchCard = $('#SearchCard').val();

            if(valueEmpty(SearchCard)){
                toastr.error("Card Number is required");
                validate = false;
                return true;
            }

            if(validate && xhrGetDataStatus){
                xhrGetDataStatus = false;
                var formData = {
                    SearchCard : SearchCard,
                }
                var url = '{{action('Sales\MemberInqueryController@getByCard')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : "POST",
                    url         :  url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {

                    },
                    success: function(response,data) {
                        if(response['status'] == 'success'){
                            var data = response['data']['items'];
                            var len = data.length;
                            for(var i=0;i<len;i++)
                            {
                                document.getElementById("customer_name").innerHTML = data[i]['customer_name'].toUpperCase();
                                document.getElementById("address").innerHTML = data[i]['customer_address'];
                                document.getElementById("card_type").innerHTML = data[i]['membership_type_id'];
                                document.getElementById("exp_date").innerHTML = data[i]['expiry_date'];
                                document.getElementById("bal_point").innerHTML = data[i]['loyalty_bal'];
                            }
                        }else{
                            alert("No Data Found...");
                        }
                        xhrGetDataStatus = true;
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetDataStatus = true;
                    }
                });
            }
        });
    </script>
@endsection

