"use strict";

var KTTreeview = function () {
    var demo3 = function() {

        jqTree("#kt_tree_products").jstree({
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
                    var tree = jqTree("#kt_tree_products").jstree(true);
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
                        },
                    };
                }
            },

        });
    }
    var demo4 = function() {
        jqTree("#kt_tree_group_item").jstree({
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
                    var tree = jqTree("#kt_tree_group_item").jstree(true);
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
                                    type : "group_item_tree",
                                    main_id : main_id,
                                    parent_id : parent_id,
                                    level : level,
                                }
                                cd(formData);
                                if(level == 1){
                                    tree.delete_node($node);
                                    toastr.error("Cannot create Level "+level);
                                }else{
                                    $('#kt_modal_md').modal('show').find('.modal-content').load('/product-tree-chart/create-product-group',formData);
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
                                    type : "group_item_tree",
                                    main_id : main_id,
                                    parent_id : parent_id,
                                    level : level,
                                }
                                var data_url = '/product-tree-chart/create-product-group/'+main_id;
                                if(level == 1){
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
                                        url         : "/product-group/delete/"+id,
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
                        },
                        items: {
                            "label" : "Create Product",
                            "action" : function(obj){
                                var id = $node.original.main_id;
                                localStorage.setItem('product_group', id);
                                var href = '/product/new';
                                window.open(href)
                            }
                        },
                    };
                }
            },
        }).bind("select_node.jstree", function (node, ref_node) {
          //  console.log(node);
          //  console.log(ref_node);
            jqTree('#kt_tree_products').jstree("destroy").empty();
            if(ref_node.node.original.main_id){
                var id = ref_node.node.original.main_id
                jqTree("#kt_tree_products").jstree({
                    "core" : {
                        "themes" : {
                            "responsive": false
                        },
                        // so that create works
                        "check_callback" : true,
                        'data' : {
                            'url' : function () {
                                return treeProductUrlList+'/'+id;
                            },
                            'data' : function (node) {
                                //  console.log(node);
                                // return { 'id' : node.parent };
                            }
                        },
                    },
                    "types" : {
                        "default" : {
                            "icon" : "fa fa-briefcase kt-font-success"
                        },
                        "file" : {
                            "icon" : "fa fa-file  kt-font-brand"
                        }
                    },
                    "state" : { "key" : "demo3" },
                    "plugins" : [ "contextmenu", "state", "types" ,'search'],
                    "contextmenu":{
                        "items": function($node) {
                            var tree = jqTree("#kt_tree_products").jstree(true);
                            return {
                                items: {
                                    "label" : "Update",
                                    "action" : function(obj){
                                        var id = $node.original.main_id;
                                        var href = '/product/edit/'+id;
                                        window.open(href)
                                    }
                                },
                                items2: {
                                    "label" : "Product Detail",
                                    "action" : function(obj){
                                        var id = $node.original.main_id;
                                        var data_url = '/common/get-product-detail/get-product/'+id;
                                        $('#kt_modal_md').modal('show').find('.modal-content').load(data_url);
                                    }
                                },
                                items3: {
                                    "label" : "Product Activity",
                                    "action" : function(obj){
                                       // console.log($node)
                                        var id = $node.original.main_id;
                                        var product_val = $node.original.text;
                                        var data_url = '/report/criteria-list';
                                        var data = {
                                            'title':'Product Activity Report',
                                            'product_id':id,
                                            'code_val':'',
                                            'name_val':product_val,
                                            'btn_id':'generate_report_popup',
                                        }
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                            }
                                        });
                                        $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,data);
                                    }
                                },
                            };
                        }
                    },

                });
            }

        })
    }
    return {
        //main function to initiate the module
        init: function () {
           // demo3();
            demo4();
        }
    };
}();

jQuery(document).ready(function() {
    KTTreeview.init();
    jqTree('#deliverable_search').keyup(function(){
        jqTree('#kt_tree_group_item').jstree(true).show_all();
        jqTree('#kt_tree_group_item').jstree('search', jqTree(this).val());
    });
    jqTree('#product_search').keyup(function(){
        jqTree('#kt_tree_products').jstree(true).show_all();
        jqTree('#kt_tree_products').jstree('search', jqTree(this).val());
    });

});
