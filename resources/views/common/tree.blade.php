<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Chart of Tree</h5>
    <button type="button" class="close" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row ">
        <div class="col-lg-6">
            <div id="kt_tree_2">
                {!! $data !!}
            </div>
        </div>
        <div class="col-lg-6">
            <div class="last_child_item">
                {!! $data2 !!}
            </div>
        </div>
    </div>
</div>
{{--<div class="modal-footer">
    <button type="button" class="btn btn-primary" id="calculate_generate">Generate</button>
</div>--}}
<link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<script src="/assets/plugins/custom/jstree/jstree.bundle.js" type="text/javascript"></script>
<script src="/assets/js/pages/components/extended/treeview.js" type="text/javascript"></script>

