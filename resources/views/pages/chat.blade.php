@extends('layouts.template')
@section('title', 'Chat Room')

@section('pageCSS')
    <style>
        .erp_form__table_block{
            position: relative;
        }
        /*
      scrollbar styling
  */
        .table-scroll::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #ffffff;
            border-radius: 10px;

        }
        .table-scroll::-webkit-scrollbar
        {
            width: 4px !important;
            height: 4px !important;
            background-color: #ffffff;
        }
        .table-scroll::-webkit-scrollbar-thumb
        {
            background-color: #7f7f7f;
        }
        .table-scroll:hover,
        .table-scroll:focus {
            visibility: visible;
        }
        .table-scroll {
            position: relative;
            width:100%;
            overflow: auto;
            height: 200px;
        }
        /* th resize */
        .JCLRgrips{
            /*top:0 !important;*/
        }

        /* table and table-head*/
        .erp_form__grid {
            border: 1px solid #ebedf2;
        }
        .erp_form__grid thead tr th {
            position: -webkit-sticky;
            position: sticky;
            top: -1px;
            background-color: #f9f9f9;
            font-size: 11px;
            font-weight: 500 !important;
            text-align: center;
            padding: 0 !important;
            font-family: Roboto;
            border-right: 1px solid #ebedf2;
            z-index: 3;
        }
        .erp_form__grid_th_title {
            padding: 3px;
            border-top: 2px solid #d5d5d5 !important;
            border-bottom: 2px solid #d5d5d5 !important;
        }
        .erp_form__grid_th_btn,
        .erp_form__grid_th_input {
            border-bottom: 2px solid #d5d5d5 !important;
        }
        .erp_form__grid_th_input>input {
            border: 0;
        }
        .erp_form__grid_newBtn {
            padding: 4.25px 0 4.25px 5px !important;
            border-radius: 0 !important;
            margin: 0.2px 0;
        }
        /* Body */
        .erp_form__grid_body>tr>td>input {
            border: 0;
        }
        .erp_form__grid_body>tr>td {
            padding: 0;
            border-right: 1px solid #ebedf2;
        }
        .erp_form__grid_delBtn {
            padding: 4.25px 0 4.25px 5px !important;
            border-radius: 0 !important;
            margin: 0.2px 0;
        }
        .erp_form__grid_body>tr>td:first-child>input {
            width: calc(100% - 13px);
        }
        .erp_form__grid_body>tr>td>input[readonly],
        .erp_form__grid_body>tr>td:first-child {
            background: #f9f9f9 !important;
        }

        /* inline help */
        #inLineHelp{
            top: 0;
            left: 35px;
        }
    </style>
@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <!--begin: Search Form -->
                <div class="row">
                    <div class="col-md-4">
                        <h5 class="kt-portlet__head-title">
                            Chat Room
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input type="text" class="form-control form-control-sm" placeholder="Search..." id="generalSearch">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>
                </div>
                <!--end: Search Form -->
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="kt-widget3">
                            <div class="kt-widget3__item">
                                <div class="kt-widget3__header">
                                    <div class="kt-widget3__user-img">
                                        {{--<img class="kt-widget3__img" src="assets/media/users/user1.jpg" alt="">--}}
                                    </div>
                                    <div class="kt-widget3__info">
                                        <a href="#" class="kt-widget3__username">
                                            Melania Trump
                                        </a><br>
                                        <span class="kt-widget3__time">2 day ago</span>
                                    </div>
                                    <span class="kt-widget3__status kt-font-info"></span>
                                </div>
                                <div class="kt-widget3__body">
                                    <p class="kt-widget3__text">
                                        Lorem ipsum dolor sit amet,consectetuer edipiscing elit,sed diam nonummy nibh euismod tinciduntut laoreet doloremagna aliquam erat volutpat.
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget3__item">
                                <div class="kt-widget3__header">
                                    <div class="kt-widget3__user-img">
                                        {{--<img class="kt-widget3__img" src="assets/media/users/user4.jpg" alt="">--}}
                                    </div>
                                    <div class="kt-widget3__info">
                                        <a href="#" class="kt-widget3__username">
                                            Lebron King James
                                        </a><br>
                                        <span class="kt-widget3__time">1 day ago</span>
                                    </div>
                                    <span class="kt-widget3__status kt-font-brand"></span>
                                </div>
                                <div class="kt-widget3__body">
                                    <p class="kt-widget3__text">
                                        Lorem ipsum dolor sit amet,consectetuer edipiscing elit,sed diam nonummy nibh euismod tinciduntut laoreet doloremagna aliquam erat volutpat.Ut wisi enim ad minim veniam,quis nostrud exerci tation ullamcorper.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input type="text" class="form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Barcode
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input type="text" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Product Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">UOM</div>
                                            <div class="erp_form__grid_th_input">
                                                <input type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Packing</div>
                                            <div class="erp_form__grid_th_input">
                                                <input type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col" width="48">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" id="newBtn" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="1" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="7800" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="2" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="9999" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="3" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="8888" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="4" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="3333" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="5" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="3333" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="6" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="6666" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="handle text-center"><i class="fa fa-arrows-alt-v handle"></i>
                                            <input type="text" class="form-control erp-form-control-sm" value="7" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="5555" class="tb_moveIndex form-control erp-form-control-sm open_inline__help" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="Needle">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="PC">
                                        </td>
                                        <td>
                                            <input type="text" class="tb_moveIndex form-control erp-form-control-sm" value="1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" id="delBtn" class="erp_form__grid_delBtn btn btn-danger btn-sm">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        function table_th_resize(){
            // colResizable 1.6 - a jQuery plugin by Alvaro Prieto Lauroba http://www.bacubacu.com/colresizable/
            !function(t){var e,i=t(document),r=t("head"),o=null,s={},d=0,n="id",a="px",l="JColResizer",c="JCLRFlex",f=parseInt,h=Math,p=navigator.userAgent.indexOf("Trident/4.0")>0;try{e=sessionStorage}catch(g){}r.append("<style type='text/css'>  .JColResizer{table-layout:fixed;} .JColResizer > tbody > tr > td, .JColResizer > tbody > tr > th{overflow:hidden;padding-left:0!important; padding-right:0!important;}  .JCLRgrips{ height:0px; position:relative;} .JCLRgrip{margin-left:-5px; position:absolute; z-index:5; } .JCLRgrip .JColResizer{position:absolute;background-color:red;filter:alpha(opacity=1);opacity:0;width:10px;height:100%;cursor: e-resize;/*top:0px*/} .JCLRLastGrip{position:absolute; width:1px; } .JCLRgripDrag{ border-left:1px dotted black;	} .JCLRFlex{width:auto!important;} .JCLRgrip.JCLRdisabledGrip .JColResizer{cursor:default; display:none;}</style>");var u=function(e,i){var o=t(e);if(o.opt=i,o.mode=i.resizeMode,o.dc=o.opt.disabledColumns,o.opt.disable)return w(o);var a=o.id=o.attr(n)||l+d++;o.p=o.opt.postbackSafe,!o.is("table")||s[a]&&!o.opt.partialRefresh||("e-resize"!==o.opt.hoverCursor&&r.append("<style type='text/css'>.JCLRgrip .JColResizer:hover{cursor:"+o.opt.hoverCursor+"!important}</style>"),o.addClass(l).attr(n,a).before('<div class="JCLRgrips"/>'),o.g=[],o.c=[],o.w=o.width(),o.gc=o.prev(),o.f=o.opt.fixed,i.marginLeft&&o.gc.css("marginLeft",i.marginLeft),i.marginRight&&o.gc.css("marginRight",i.marginRight),o.cs=f(p?e.cellSpacing||e.currentStyle.borderSpacing:o.css("border-spacing"))||2,o.b=f(p?e.border||e.currentStyle.borderLeftWidth:o.css("border-left-width"))||1,s[a]=o,v(o))},w=function(t){var e=t.attr(n),t=s[e];t&&t.is("table")&&(t.removeClass(l+" "+c).gc.remove(),delete s[e])},v=function(i){var r=i.find(">thead>tr:first>th,>thead>tr:first>td");r.length||(r=i.find(">tbody>tr:first>th,>tr:first>th,>tbody>tr:first>td, >tr:first>td")),r=r.filter(":visible"),i.cg=i.find("col"),i.ln=r.length,i.p&&e&&e[i.id]&&m(i,r),r.each(function(e){var r=t(this),o=-1!=i.dc.indexOf(e),s=t(i.gc.append('<div class="JCLRgrip"></div>')[0].lastChild);s.append(o?"":i.opt.gripInnerHtml).append('<div class="'+l+'"></div>'),e==i.ln-1&&(s.addClass("JCLRLastGrip"),i.f&&s.html("")),s.bind("touchstart mousedown",J),o?s.addClass("JCLRdisabledGrip"):s.removeClass("JCLRdisabledGrip").bind("touchstart mousedown",J),s.t=i,s.i=e,s.c=r,r.w=r.width(),i.g.push(s),i.c.push(r),r.width(r.w).removeAttr("width"),s.data(l,{i:e,t:i.attr(n),last:e==i.ln-1})}),i.cg.removeAttr("width"),i.find("td, th").not(r).not("table th, table td").each(function(){t(this).removeAttr("width")}),i.f||i.removeAttr("width").addClass(c),C(i)},m=function(t,i){var r,o,s=0,d=0,n=[];if(i){if(t.cg.removeAttr("width"),t.opt.flush)return void(e[t.id]="");for(r=e[t.id].split(";"),o=r[t.ln+1],!t.f&&o&&(t.width(o*=1),t.opt.overflow&&(t.css("min-width",o+a),t.w=o));d<t.ln;d++)n.push(100*r[d]/r[t.ln]+"%"),i.eq(d).css("width",n[d]);for(d=0;d<t.ln;d++)t.cg.eq(d).css("width",n[d])}else{for(e[t.id]="";d<t.c.length;d++)r=t.c[d].width(),e[t.id]+=r+";",s+=r;e[t.id]+=s,t.f||(e[t.id]+=";"+t.width())}},C=function(t){t.gc.width(t.w);for(var e=0;e<t.ln;e++){var i=t.c[e];t.g[e].css({left:i.offset().left-t.offset().left+i.outerWidth(!1)+t.cs/2+a,height:t.opt.headerOnly?t.c[0].outerHeight(!1):t.outerHeight(!1)})}},b=function(t,e,i){var r=o.x-o.l,s=t.c[e],d=t.c[e+1],n=s.w+r,l=d.w-r;s.width(n+a),t.cg.eq(e).width(n+a),t.f?(d.width(l+a),t.cg.eq(e+1).width(l+a)):t.opt.overflow&&t.css("min-width",t.w+r),i&&(s.w=n,d.w=t.f?l:d.w)},R=function(e){var i=t.map(e.c,function(t){return t.width()});e.width(e.w=e.width()).removeClass(c),t.each(e.c,function(t,e){e.width(i[t]).w=i[t]}),e.addClass(c)},x=function(t){if(o){var e=o.t,i=t.originalEvent.touches,r=i?i[0].pageX:t.pageX,s=r-o.ox+o.l,d=e.opt.minWidth,n=o.i,l=1.5*e.cs+d+e.b,c=n==e.ln-1,f=n?e.g[n-1].position().left+e.cs+d:l,p=e.f?n==e.ln-1?e.w-l:e.g[n+1].position().left-e.cs-d:1/0;if(s=h.max(f,h.min(p,s)),o.x=s,o.css("left",s+a),c){var g=e.c[o.i];o.w=g.w+s-o.l}if(e.opt.liveDrag){c?(g.width(o.w),!e.f&&e.opt.overflow?e.css("min-width",e.w+s-o.l):e.w=e.width()):b(e,n),C(e);var u=e.opt.onDrag;u&&(t.currentTarget=e[0],u(t))}return!1}},y=function(r){if(i.unbind("touchend."+l+" mouseup."+l).unbind("touchmove."+l+" mousemove."+l),t("head :last-child").remove(),o){if(o.removeClass(o.t.opt.draggingClass),o.x-o.l!=0){var s=o.t,d=s.opt.onResize,n=o.i,a=n==s.ln-1,c=s.g[n].c;a?(c.width(o.w),c.w=o.w):b(s,n,!0),s.f||R(s),C(s),d&&(r.currentTarget=s[0],d(r)),s.p&&e&&m(s)}o=null}},J=function(e){var d=t(this).data(l),n=s[d.t],a=n.g[d.i],c=e.originalEvent.touches;if(a.ox=c?c[0].pageX:e.pageX,a.l=a.position().left,a.x=a.l,i.bind("touchmove."+l+" mousemove."+l,x).bind("touchend."+l+" mouseup."+l,y),r.append("<style type='text/css'>*{cursor:"+n.opt.dragCursor+"!important}</style>"),a.addClass(n.opt.draggingClass),o=a,n.c[d.i].l)for(var f,h=0;h<n.ln;h++)f=n.c[h],f.l=!1,f.w=f.width();return!1},L=function(){for(var t in s)if(s.hasOwnProperty(t)){t=s[t];var i,r=0;if(t.removeClass(l),t.f){for(t.w=t.width(),i=0;i<t.ln;i++)r+=t.c[i].w;for(i=0;i<t.ln;i++)t.c[i].css("width",h.round(1e3*t.c[i].w/r)/10+"%").l=!0}else R(t),"flex"==t.mode&&t.p&&e&&m(t);C(t.addClass(l))}};t(window).bind("resize."+l,L),t.fn.extend({colResizable:function(e){var i={resizeMode:"fit",draggingClass:"JCLRgripDrag",gripInnerHtml:"",liveDrag:!1,minWidth:15,headerOnly:!1,hoverCursor:"e-resize",dragCursor:"e-resize",postbackSafe:!1,flush:!1,marginLeft:null,marginRight:null,disable:!1,partialRefresh:!1,disabledColumns:[],onDrag:null,onResize:null},e=t.extend(i,e);switch(e.fixed=!0,e.overflow=!1,e.resizeMode){case"flex":e.fixed=!1;break;case"overflow":e.fixed=!1,e.overflow=!0}return this.each(function(){u(this,e)})}})}(jQuery);
            $(function(){
                var onSampleResized = function(e){
                    var table = $(e.currentTarget); //reference to the resized table
                };
                $(".erp_form__grid_th_resize").colResizable({
                    // fixed:false,
                    /*disabledColumns: [0],*/
                    headerOnly: true,
                    liveDrag:true,
                    // gripInnerHtml:"<div class='grip'></div>",
                    resizeMode:'overflow',
                    draggingClass:"dragging",
                    onResize:onSampleResized
                });
            });
        }
        table_th_resize();

        function table_td_sortable(){
            $( ".erp_form__grid_body" ).sortable({
                handle: ".handle",
                /*update: function (e,ui) {
                   // tdUpDownInit();
                }*/
            });
            $( ".erp_form__grid_body>tr" ).disableSelection();
        }
        table_td_sortable();

        $(document).on('focusin','.open_inline__help',function (e) {
            $(this).addClass('open_inline__help__focus');
        }).on('focusout','.open_inline__help',function (e) {
            $(this).removeClass('open_inline__help__focus');
        });
        function display_help(that,table_block,table_block__table){
            table_block__table.after('<div id="inLineHelp"><div class="inLineHelp"></div></div>');
            var inLineHelp = table_block.find('.inLineHelp');

            if(that.parents('thead').hasClass('erp_form__grid_header')){
                var offsetTop = that.parents('tr').height();
            }
            if(that.parents('tbody').hasClass('erp_form__grid_body')){
                var offsetTop = $('.open_inline__help__focus').offset().top - $('.erp_form___block').offset().top + that.parents('tr').height();
            }
            var cssTop = offsetTop+'px';
            $('#inLineHelp').css({top:cssTop});

            //
            if (that.parents('.open-modal-group').length != 0) {
                inLineHelp.addClass("inline_help");
            } else {
                inLineHelp.addClass("inline_help_table");
            }
        }
        $(document).on('keyup','.open_inline__help',function (e) {
            var that = $(this);
            var table_block = that.parents('.erp_form___block');
            var table_block__table = that.parents('.erp_form___block>.form_input__block');
            if (e.which === 113) {
                $('#inLineHelp').remove();
                if (that.siblings('#inLineHelp').length == 0) {
                    display_help(that,table_block,table_block__table);
                    var inLineHelp = table_block.find('.inLineHelp');
                    // load data
                    var data_url = that.attr('data-url');
                    if(inLineHelp.length != 0){
                        inLineHelp.load(data_url);
                    }
                }
            }
            if(e.which === 40 && table_block.find('.inLineHelp').length != 0){
                var inLineHelp = table_block.find('.inLineHelp');
                if(inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false){
                    inLineHelp.find('.data_tbody_row:eq(0)').addClass('selected_row');
                }else{
                    var index = inLineHelp.find('.data_tbody_row.selected_row').index();
                    var ww_index =  index - 2;
                    index = index - 1;
                    inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
                    inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
                }
                var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
                that.val(val);
            }
            if(e.which === 38 && table_block.find('.inLineHelp').length != 0){
                var inLineHelp = table_block.find('.inLineHelp');
                if(inLineHelp.find('.data_tbody_row').hasClass('selected_row') == true){
                    var index = inLineHelp.find('.data_tbody_row.selected_row').index();
                    var ww_index =  index - 2;
                    index = index - 3;
                    inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
                    inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
                }
                var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
                that.val(val);
            }
            var mobileRequest = true;
            if (((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || e.keyCode === 8 )
                && that.val() != '') {
                display_help(that,table_block,table_block__table);
                var inLineHelp = table_block.find('.inLineHelp');
                inLineHelp.find('.data_tbody_row').removeClass('selected_row');
                if(inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false) {
                    var data_url = that.attr('data-url');
                    var url = data_url + '/' + encodeURIComponent($(this).val());
                    if(inLineHelp.length != 0){
                        inLineHelp.load(url);
                    }
                    mobileRequest = false;
                }
            }
            if($( window ).width() <= 1024 && mobileRequest == true){
                display_help(that,table_block,table_block__table);
                var inLineHelp = table_block.find('.inLineHelp');
                var data_url = $(this).attr('data-url');
                var url = data_url + '/' + encodeURIComponent($(this).val());
                if(inLineHelp.length != 0){
                    inLineHelp.load(url);
                }
            }
            if($('.inLineHelp .data_tbody_row').hasClass('selected_row')){
                $("#inLineHelp .inline_help_table").scrollTop(0);//set to top
                var selected_row = $("#inLineHelp").find('.selected_row:first');
                $("#inLineHelp .inline_help_table").scrollTop(selected_row.position().top - 150);
            }
        });
        if($( window ).width() <= 1024){
            $(document).on('click','#mobOpenInlineHelp',function (e) {
                $(document).find('#inLineHelp').remove();
                var thix = $(this);
                var that = thix.parents('th').find('.open_inline__help');
                var table_block = that.parents('.erp_form___block');
                var table_block__table = that.parents('.erp_form___block>.form_input__block');
                display_help(that,table_block,table_block__table);
                // load data
                var inLineHelp = table_block.find('.inLineHelp');
                var data_url = that.attr('data-url');
                if(inLineHelp.length != 0){
                    inLineHelp.load(data_url);
                }
            });
        }
        $(document).on('click',function(e){
            if(!$(e.target).hasClass('open-inline-help')){
                if($( window ).width() <= 1024) {
                    $('#inLineHelp').hide();
                }else{
                    $('#inLineHelp').remove();
                }
            }
            if($(e.target).is('#mobOpenInlineHelp') || $(e.target).is('#mobOpenInlineHelp>i')
                || $(e.target).is('#mobOpenInlineSupplierHelp') || $(e.target).is('#mobOpenInlineSupplierHelp>i')){
                $('#inLineHelp').show();
            }
        });
        $(document).on('keydown','.tb_moveIndex',function (e) {
            if(e.which === 13){
                if($(this).hasClass('tb_moveIndexBtn')){
                    $('.tb_moveIndexBtn').click();
                    if($(this).parents('thead').hasClass('erp_form__grid_header')){
                        $('.erp_form__grid_header').find('th:eq(1) input').focus();
                    }
                }else{
                    var index = $('.tb_moveIndex').index(this) + 1;
                    $('.tb_moveIndex').eq(index).focus();
                }
            }
            if(e.which === 40 && $('.erp_form___block').find('.inLineHelp').length == 0){
                $(".kt_datepicker_3").datepicker("hide");
                var currentTd = $(this).parents('td').index();
                var next = $(this).parents('tr').closest('tr').next('tr')
                next.find('td:eq('+currentTd+')>input').focus();
            }
            if(e.which === 38 && $('.erp_form___block').find('.inLineHelp').length == 0){
                $(".kt_datepicker_3").datepicker("hide");
                var currentTd = $(this).parents('td').index();
                var prev = $(this).parents('tr').closest('tr').prev('tr')
                prev.find('td:eq('+currentTd+')>input').focus();
            }
            if($('.erp_form___block').find('.inLineHelp').length == 0){
                $(".table-scroll").scrollTop(0); //set to top
                var focus_input = $(".tb_moveIndex__focus").parents('tr').height() * $(".tb_moveIndex__focus").parents('tr').index() + $('.erp_form__grid_header>tr').height();
                $(".table-scroll").scrollTop(focus_input - 84);

                $('.table-scroll>.JCLRgrips').css('top','0px');
                var JCLRgripsTop = $('.table-scroll').offset().top - $('.JCLRgrips').offset().top;
                JCLRgripsTop = JCLRgripsTop+'px';
                $('.table-scroll>.JCLRgrips').css('top', JCLRgripsTop);
            }
        });
        $(document).on('focusin','.tb_moveIndex',function (e) {
            $(this).addClass('tb_moveIndex__focus');
        }).on('focusout','.tb_moveIndex',function (e) {
            $(this).removeClass('tb_moveIndex__focus');
        });
        $(document).on('mousewheel', '.table-scroll', function () {
            $('.table-scroll>.JCLRgrips').css('top','0px');
            var JCLRgripsTop = $('.table-scroll').offset().top - $('.JCLRgrips').offset().top;
            JCLRgripsTop = JCLRgripsTop+'px';
            $('.table-scroll>.JCLRgrips').css('top', JCLRgripsTop);
        });
        // on click and on enter
    </script>

@endsection

