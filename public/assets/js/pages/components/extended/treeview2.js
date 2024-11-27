"use strict";

var KTTreeview = function () {

    var demo1 = function () {
        $('#kt_tree_2').jstree({
            "core" : {
                "themes" : {
                    "responsive": false
                }
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder kt-font-warning"
                },
                "file" : {
                    "icon" : "fa fa-file "
                }
            },
            "plugins": ["types"]
        });
    }
    var demo4 = function() {

        jqTree("#kt_tree_4").jstree({
            "core" : {
                "themes" : {
                    "responsive": false
                },
                // so that create works
                "check_callback" : true,
                'data' : {
                    'url' : function () {
                        return '/accounts/get-coa-tree';
                    },
                    'data' : function (node) {
                      //  console.log(node);
                        // return { 'id' : node.parent };
                    }
                },
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder kt-font-brand"
                },
                "file" : {
                    "icon" : "fa fa-file  kt-font-brand"
                }
            },
            "state" : { "key" : "demo2" },
            "plugins" : [ "contextmenu", "state", "types" ,'search'],
            "contextmenu":{
                "items": function($node) {
                    var tree = jqTree("#kt_tree_4").jstree(true);
                    return {
                        createItem : {
                            "label" : "Create",
                            "action" : function(obj) {
                                cd($node);
                                cd(obj);
                                var main_id = '';
                                var level = $node.original.level + 1;
                                var parent_id = $node.original.main_id;
                                $node = tree.create_node($node, {
                                    text: 'New File',
                                });
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                var formData = {
                                    type : "acc_tree",
                                    main_id : main_id,
                                    parent_id : parent_id,
                                    level : level,
                                }
                                cd(formData);
                                if(level == 1 || level > 4){
                                    tree.delete_node($node);
                                    toastr.error("Cannot create Level "+level);
                                }else{
                                    $('#kt_modal_md').modal('show').find('.modal-content').load('/accounts-tree/tree-form',formData);
                                }

                                tree.deselect_all();
                                tree.select_node($node);
                                /* tree.delete_node($node);*/
                            },
                            "_class" : "class"
                        },
                        renameItem : {
                            "label" : "Update",
                            "action" : function(obj) {
                                cd($node);
                                cd(obj);
                                var main_id = $node.original.main_id;
                                var parent_id = $node.original.parent_main_id;
                                var level = $node.original.level;
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                var formData = {
                                    type : "acc_tree",
                                    main_id : main_id,
                                    parent_id : parent_id,
                                    level : level,
                                }
                                var data_url = '/accounts-tree/tree-form/'+main_id;
                                if(level == 1 || level > 4){
                                    toastr.error("Cannot update Level "+level);
                                }else{
                                    $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
                                }
                                tree.deselect_all();
                                tree.select_node($node);
                            }
                        },
                        deleteItem : {
                            "label" : "Delete",
                            "action" : function(obj) {
                                var id = $node.original.main_id;
                                if($node.children.length > 0 || $node.children_d.length > 0){
                                    toastr.error("Account cannot delete because its own child accounts");
                                }else{
                                    var notDel = 0;
                                    $.ajax({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        async:false,
                                        type        : 'post',
                                        url         : "/coa/delete/"+id,
                                        success: function(response) {
                                            if(response.status == 'success'){
                                                toastr.success(response.message);
                                                notDel = 2;
                                            }else{
                                                toastr.error(response.message);
                                                notDel = 1;
                                            }
                                        },
                                        error: function(response,status) {
                                            toastr.error("Something wrong e..");
                                            notDel = 1;
                                        },
                                    });
                                    if(notDel == 1){ tree.refresh() }
                                    if(notDel == 2){ tree.delete_node($node); }
                                }
                            }
                        }
                    };
                }
            },

        });
    }
    return {
        //main function to initiate the module
        init: function () {
            demo4();
        }
    };
}();

jQuery(document).ready(function() {
    KTTreeview.init();
    jqTree('#deliverable_search').keyup(function(){
        jqTree('#kt_tree_4').jstree(true).show_all();
        jqTree('#kt_tree_4').jstree('search', jqTree(this).val());
    });

});
