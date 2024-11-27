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
        $("#kt_tree_4").jstree({
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
                        console.log(node);
                       // return { 'id' : node.parent };
                    }
                },
                /*
                'data2': [
                    {
                        "text": "Parent Node",
                        "children": [
                            {
                                "text": "Initially selected",
                            },
                            {
                                "text": "Custom Icon",
                            },
                            {
                                "text": "Initially open",
                                "children": [{"text": "Another node"}]
                            },
                            {
                                "text": "Another Custom Icon",
                            },
                            {
                                "text": "Disabled Node",
                            },
                            {
                                "text": "Sub Nodes",
                                "children": [
                                    {"text": "Item 1"},
                                    {"text": "Item 2"},
                                    {"text": "Item 3"},
                                    {"text": "Item 4"},
                                    {"text": "Item 5"}
                                ]
                            }
                        ]
                    },
                    "Another Node"
                ]*/
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

        }).on('create_node.jstree',function(e, data) {
            cd("create");
            var level = data.node.parents.length
            var parent = data.node.parent;
            var code = "";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                async:false,
                type        : 'get',
                url         : "/coa/coa-max/"+level+"/"+parent,
                success: function(response) {
                    code = response;
                }
            });
            data.node.text = "["+code+"] New Account";
            data.node.icon = "fa fa-folder kt-font-success";
            data.node.original.main_id = 0;
            $(this).jstree(true).set_id(data.node, code);

        }).on("changed.jstree", function (e, data) {
            cd("changed");

        }).on('rename_node.jstree', function (e, data) {
            cd("rename");
            var name = data.node.text;
            var new_main_id = 0;
            var main_id = data.node.original.main_id;
            var notDel = 0;
            var formData = {
                level : data.node.parents.length,
                code : data.node.id,
                parent : data.node.parent,
                name : name
            }
            if(main_id !== 0){
                formData.main_id = main_id
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                async:false,
                type        : 'post',
                url         : "/accounts/create-coa",
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    if(response.status == 'success'){
                        toastr.success(response.message);
                        new_main_id = response['data']['main_id'];
                    }else{
                        toastr.error(response.message);
                        notDel = 1;
                    }
                },
                error: function(response,status) {
                    toastr.error("Something wrong e..");
                },
            });
            if(notDel == 1){
                data.instance.refresh();
            }
            if(new_main_id != 0){
                data.node.original.main_id = new_main_id;
            }
        }).on('delete_node.jstree', function (e, data) {
            cd("delete_node");
            // return false;
            var notDel = 0;
            var id = data.node.original.main_id;
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
                    }else{
                        toastr.error(response.message);
                        notDel = 1;
                    }
                },
                error: function(response,status) {
                    toastr.error("Something wrong e..");
                },
            });
            if(notDel == 1){
                data.instance.refresh();
            }
        }).on('select_node.jstree', function (e, data) {
            cd("select_node");
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
    $('#deliverable_search').keyup(function(){
        $('#kt_tree_4').jstree(true).show_all();
        $('#kt_tree_4').jstree('search', $(this).val());
    });
});
