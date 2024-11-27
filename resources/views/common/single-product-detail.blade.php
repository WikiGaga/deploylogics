<style>
    /***********************************
    Single Product Name
 */
    #single_product_detail>.card-body>.prod>.prod-img>img{
        height: 108px;
        object-fit: contain;
    }
    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless {
        line-height: 1.5;
        font-size: 13px !important;
        font-weight: 400;
        color: #3F4254;
        box-sizing: border-box;
        position: relative;
        min-width: 0;
        border-radius: 0.42rem;
        border: 0;
        display: flex;
        height: 100%;
        background-color: transparent;
    }

    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless>.card-body{
        padding: 2rem 0;
    }

    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless>.card-body>h3{
        font-size: 20px !important;
    }

    #single_product_detail>.card-body>.prod>.prod-head>.card-shadowless>.card-body>.barcode_name{
        line-height: 1.5;
        color: #181C32 !important;
        font-size: 16px !important;
    }
    button.modal_close {
        position: absolute;
        top: 4px;
        width: 20px;
        height: 20px;
        right: 10px;
        z-index: 999999;
    }
    .product_detail_table {
        margin-top: 10px;
    }

    .product_detail_table>thead {
        color: #000;
        background: #ffbb38;
    }

    .product_detail_table>thead>tr>th {
        padding: 2px 5px;
        font-weight: 400;
    }

    .product_detail_table>tbody>tr>td {
        padding: 3px 5px;
    }
    .product_detail_table>tbody>tr:nth-child(even)>td {
        background: #fffbf4;
    }

    .product_detail_table>tbody>tr>td{
        font-size: 12px;
        font-weight: 400;
    }
    .search_barcode{
        cursor: pointer;
    }
    .prod_head{
        cursor: move;
        }
    .text-black{
        color: #000;
    }
    /**
        End Single Product Name
    **********************************/
</style>
<div class="modal-body" style="padding: 2px;">
    <div class="row prod_head" style="padding: 10px 0; background: #ffc107; margin: 0;">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="search_barcode" autocomplete="off" class="form-control erp-form-control-sm" placeholder="Enter here" autofocus>
                <div class="input-group-append">
                    <span class="input-group-text search_barcode" style="padding: 3px 10px;"> GO </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <button type="button" class="modal_close close" onclick="closeModal()" aria-label="Close"><i class="la la-times"></i></button>
        </div>
    </div>
    @include('common.single-product-detail-block')
</div>
<script>
    function closeModal(){
        $('#kt_modal_md').find('.modal-content').empty();
        $('#kt_modal_md').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
        $('#kt_modal_md').modal('hide');
    }

    $(document).on('keyup','#search_barcode',function(e){
        if(e.which == 13){
            initProd();
        }
    });
    $(document).on('click','.search_barcode',function(e){
        initProd();
    });
    function initProd(){
        var val = $('.modal-body').find('#search_barcode').val();
        var input = document.getElementById('search_barcode');
        $('.prod_block').html('<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style="top: 72px;"> <span style="text-align: center;">loading..</span></div>')
        var type = 'ajax_prod_search';
        if(val != null && val != ''){
            input.select();
            var data_url = '/common/get-product-detail/'+type+'/'+val;
            $('.prod_block').load(data_url);
            
        }else{
            $('.prod_block').html('<div style="text-align: center; line-height: 150px; font-size: 29px; font-weight: 400; color: #9e9e9e;">Found new barcode data..</div>');
        }
    }
</script>
