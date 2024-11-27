@extends('layouts.layout2')
@section('title', 'Product Tree')

@section('pageCSS')
    <!--begin::Page Vendors Styles(used by this page) -->
    <link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />
    <style>
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
        .total-product{
            color: #0abb87;
            position: relative;
            left: 8px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Product Tree
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">

                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="tree_search_block">
                            <input id="deliverable_search" placeholder="search..." class="form-control tree_search_input" type="text">
                        </div>
                        <div id="kt_tree_group_item" class="tree-demo"></div>
                    </div>
                    <div class="col-lg-6">
                        <div class="tree_search_block">
                            <input id="product_search" placeholder="search..." class="form-control tree_search_input" type="text">
                        </div>
                        <div id="kt_tree_products" class="tree-demo"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="contextChartTreeMenu" class="dropdown clearfix" style="display: none">
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
            <li class="tree_menu_li"><a tabindex="-1" href="javascript:;" id="new_create">Create</a></li>
            <li class="tree_menu_li"><a tabindex="-1" href="javascript:;" id="rename">Rename</a></li>
            <li class="tree_menu_li"><a tabindex="-1" href="javascript:;" id="delete">Delete</a></li>
        </ul>
    </div>
@endsection


@section('customJS')

    <script>
        var $ = $.noConflict();
        var treeUrlList = '{{action('Purchase\ProductTreeController@productGroupTreeList')}}';
        var treeProductUrlList = '{{action('Purchase\ProductTreeController@productTreeList')}}';
    </script>
    <script src="/assets/plugins/custom/jstree/jstree.bundle.js" type="text/javascript"></script>
    <script> var jqTree = $.noConflict(); </script>
    <script src="/js/pages/js/purchase/product-tree-view.js" type="text/javascript"></script>
    <script>
        $(document).on('click','.close_new',function(){
            var tree =  jqTree("#kt_tree_group_item");
            var id = $(document).find('.jstree-clicked').attr('id');
            var node = tree.jstree(true).get_node(id);
            var parent_node = tree.jstree(true).get_node(node.parent);
            tree.jstree("select_node", parent_node);
            tree.jstree("delete_node", node);


        });
    </script>
@endsection
