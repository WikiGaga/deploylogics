@extends('layouts.layout2')
@section('title', 'Chart of Account Tree')

@section('pageCSS')
<!--begin::Page Vendors Styles(used by this page) -->
<link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />
<style>
    .jstree-default .jstree-search {
        font-style: unset !important;
        color: #707bc5 !important;
    }
</style>
@endsection

@section('content')
		<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
				<div class="kt-portlet__head kt-portlet__head--lg">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Chart of Account Tree
						</h3>
					</div>
					<div class="kt-portlet__head-toolbar">
                        <input id="deliverable_search" placeholder="search..." class="form-control" type="text" style="border: 1px solid #af8d8d;    background: antiquewhite;">
					</div>
                </div>
				<div class="kt-portlet__body">
					<div class="row">
						<div class="col-lg-12">
							<div id="kt_tree_4" class="tree-demo">
								{{--{!! $tree !!}--}}
							</div>
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
    <script> var $ = $.noConflict(); </script>
    <script src="/assets/plugins/custom/jstree/jstree.bundle.js" type="text/javascript"></script>
    <script> var jqTree = $.noConflict(); </script>
    <script src="/assets/js/pages/components/extended/treeview2.js" type="text/javascript"></script>
    <script>
        $(document).on('click','.close',function(){
            var tree =  jqTree("#kt_tree_4");
            var id = $(document).find('.jstree-clicked').attr('id');
            var node = tree.jstree(true).get_node(id);
            var parent_node = tree.jstree(true).get_node(node.parent);
            tree.jstree("select_node", parent_node);
            tree.jstree("delete_node", node);
        });
    </script>
@endsection
