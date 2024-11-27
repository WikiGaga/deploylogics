<div style="border: 2px solid #454cdc;">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Product Group</h5>
        <button type="button" class="tree_modal_close" data-dismiss="modal" aria-label="Close" style="border: 0;"><i class="fa fa-times" style="color: #7c7c7c;"></i></button>
    </div>
    <div class="modal-body">
        <style>
            @media (min-width: 320px) and (max-width: 1190px){
                #ajax_data,
                #selected_products{
                    /*overflow: auto !important;*/
                }
            }

            #selected_products>table,
            #ajax_data>table{
                overflow-y: scroll !important;
                height: 75vh;
                width: 100% !important;
            }
            #selected_products>table>.kt-datatable__head,
            #ajax_data>table>.kt-datatable__head{
                position: absolute !important;
                width: calc(100% - 17px);
                top: -2px;
                border-top-width: 0px;
            }
            #selected_products>table>.kt-datatable__head>tr>th,
            #ajax_data>table>.kt-datatable__head>tr>th{
                background: #e3e3e3 !important;
            }
            #selected_products>table>.kt-datatable__body,
            #ajax_data>table>.kt-datatable__body{
                position: relative;
                top: 40px;
            }
            .erp-custom-select2>.select2>.selection>.select2-selection>.select2-selection__rendered{
                height: 34px;
                padding: 7px 8px;
            }
            .jstree-default .jstree-search {
                font-style: unset !important;
                color: #707bc5 !important;
            }
            .tree_search_block {
                margin-bottom: 15px;
                margin-left: 10px;
            }
            .tree_search_block>.tree_search_input {
                padding: 10px;
                height: 30px;
                border-radius: unset;
                width: 300px;
                background: #f0f5ff;
            }
        </style>
        <div class="row">
            <div class="col-lg-12">
                <div class="tree_search_block">
                    <input id="deliverable_search" placeholder="search..." class="form-control tree_search_input" type="text">
                </div>
                <div id="kt_tree_group_item" class="tree-demo"></div>
            </div>
        </div>
    </div>
</div>



<script src="/assets/plugins/custom/jstree/jstree.bundle.js" type="text/javascript"></script>
<script> var jqTree = $.noConflict(); </script>
<script>
    "use strict";
    var _selectedNodeId = false;
    var KTTreeview = function () {

        var demo2 = function () {
            jqTree('#kt_tree_group_item').jstree({
                "core" : {
                    "themes" : {
                        "responsive": false
                    },
                    // so that create works
                    "check_callback" : true,
                    'data' : {
                        'url' : function () {
                            return treeUrlList;
                        },
                        'data' : function (node) {
                            //  console.log(node);
                            // return { 'id' : node.parent };
                        }
                    },
                },
                "types" : {
                    "default" : {
                        "icon" : "fa fa-folder kt-font-warning"
                    },
                    "file" : {
                        "icon" : "fa fa-file  kt-font-warning"
                    }
                },
                "state" : { "key" : "demo2" },
                "plugins" : [ "contextmenu", "state", "types" ,'search'],
            }).bind("select_node.jstree", function (node, ref_node) {
                    // console.log(node);
                    // console.log(ref_node);
                    if(_selectedNodeId && ref_node.node.original.main_id){
                        var id = ref_node.node.original.main_id
                        var text = ref_node.node.original.text
                        var level = ref_node.node.original.level
                        if(level == 3){
                            console.log(id);
                            $('#select_product_group_name').html(text);
                            $('#product_group_id').val(id);
                            var arr = funGetFilterValues();
                            arr.product_group_id = id;
                            var validate = true;
                            if(requestFunGetProductCustomFilter){
                                funGetProductCustomFilter(arr,validate,requestFunGetProductCustomFilter);
                                funcTreeModalClose();
                            }
                        }
                    }
                    if(!_selectedNodeId){
                        ref_node.instance.deselect_node(ref_node.node);
                        _selectedNodeId = true;
                    }

            })
            // handle link clicks in tree nodes(support target="_blank" as well)
            jqTree('#kt_tree_2').on('select_node.jstree', function(e,data) {
                var link = $('#' + data.selected).find('a');
                if (link.attr("href") != "#" && link.attr("href") != "javascript:;" && link.attr("href") != "") {
                    if (link.attr("target") == "_blank") {
                        link.attr("href").target = "_blank";
                    }
                    document.location.href = link.attr("href");
                    return false;
                }
            });
        }

        return {
            //main function to initiate the module
            init: function () {
                demo2();
            }
        };
    }();

    jQuery(document).ready(function() {
        KTTreeview.init();
        jqTree('#deliverable_search').keyup(function(){
            jqTree('#kt_tree_group_item').jstree(true).show_all();
            jqTree('#kt_tree_group_item').jstree('search', jqTree(this).val());
        });
    });
</script>

