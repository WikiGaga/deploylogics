
<head>
  
<link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

</head>
<body>
    <div class="modal-header">
    <h5 class="modal-title">
        Parent Group Tree
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="kt-input-icon kt-input-icon--left">
                <input type="text" class="form-control" placeholder="Search..." id="generalSearch">
                <span class="kt-input-icon__icon kt-input-icon__icon--left">
                    <span><i class="la la-search"></i></span>
                </span>
            </div>
        </div>
    </div>
        	<div class="col-lg-6">

                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__body">
                        <div id="kt_tree_1" class="tree-demo">
                            {!! $tree !!}
                        </div>
                    </div>
                </div>

                <!--end::Portlet-->

            </div>


</div> 
</body>
</html>
    <!-- end:: Content -->
</div>

<script src="/assets/js/pages/crud/metronic-datatable/base/html-table.js" type="text/javascript"></script>
<script src="/assets/plugins/custom/jstree/jstree.bundle.js" type="text/javascript"></script>
<script src="/assets/js/pages/components/extended/treeview.js" type="text/javascript"></script>

<script>
    $('#kt_tree_1').click(function() {
       
        $('#kt_modal_KTDatatable_local').modal('hide');
       
        //$('#kt_tree_1').val($(this).find('#kt_tree_1').text());
        //$('#kt_modal_KTDatatable_local').find('.modal-content').html('');
        //$('#kt_modal_KTDatatable_local').modal('hide');
    });
</script>


