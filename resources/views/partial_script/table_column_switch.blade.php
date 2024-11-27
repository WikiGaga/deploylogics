<style>
    table {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
    }

    .jsdragtable-contents {
        background: #fff;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        box-shadow: 2px 2px 5px #aaa;
        padding: 0;
    }

    .jsdragtable-contents table {
        margin-bottom: 0;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    tbody>tr>td,thead>tr>th {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd
    }

    thead>tr>th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd
    }


    .table-bordered {
        border: 1px solid #ddd;
    }

    .table-bordered>tbody>tr>td,.table-bordered>thead>tr>th {
        border: 1px solid #ddd
    }

    .table-bordered>thead>tr>th {
        border-bottom-width: 2px;
        background-color: yellow;
    }

    .table-hover>tbody>tr:hover {
        background-color: #f5f5f5
    }

    .table-bordered>thead>tr>th.over {
        border: 2px dashed #000;
    }

    [draggable] {
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        user-select: none;
        /* Required to make elements draggable in old WebKit */
        -khtml-user-drag: element;
        -webkit-user-drag: element;
    }

    th{
        cursor:move;
    }
</style>
<script>
    /* this function run only when do class "table_column_switch" to table element*/
    var isIE11=!!window.MSInputMethodContext&&!!document.documentMode;function listenerDragStart(t){if(t.dataTransfer.effectAllowed="move",t.dataTransfer.setData("text",$(this).text()),!isIE11){var e=$(this).parents("table.table_column_switch"),a=document.createElement("table");a.classList.add("tableGhost"),a.classList.add("table-bordered");var r=document.defaultView.getComputedStyle(this);a.style.width=r.getPropertyValue("width");var n=document.createElement("thead"),i=this.cloneNode(!0);i.style.backgroundColor="red",n.appendChild(i),a.appendChild(n);var d=$(this).text(),s=$("th:contains("+d+")").index()+1,l=document.createElement("tbody");e.find("tbody").each(function(){var t=document.createElement("tbody");$.each($(this).find("tr td:nth-child("+s+")"),function(e,a){var r=document.createElement("tr"),n=document.createElement("td");n.innerText=$(this).text(),r.appendChild(n),t.appendChild(r)}),a.appendChild(t)}),a.appendChild(l),a.style.position="absolute",a.style.top="-1500px",document.body.appendChild(a),t.dataTransfer.setDragImage(a,0,0)}}function listenerDragOver(t){return t.preventDefault&&t.preventDefault(),t.dataTransfer.dropEffect="move",!1}function listenerDragEnter(t){this.classList.add("over")}function listenerDragLeave(t){this.classList.remove("over")}function listenerDrop(t){t.preventDefault&&t.preventDefault(),t.stopPropagation&&t.stopPropagation();var e=t.dataTransfer.getData("text"),a=$(this).text(),r=$(this).parents("table.table_column_switch");if(e!=a){var n=r.find("th:contains("+e+")"),i=n.index()+1,d=$("th:contains("+a+")").index()+1;n.insertAfter($(this)),r.find("tbody").each(function(t){var e=$(this);$.each(e.find("tr td:nth-child("+i+")"),function(t,a){$(this).insertAfter(e.find("tr:nth-child("+(t+1)+") td:nth-child("+d+")"))})}),listenerCustomFuc()}return!1}function listenerDragEnd(t){var e=document.querySelectorAll("th");[].forEach.call(e,function(t){t.classList.remove("over"),t.style.opacity=1})}$(document).ready(function(){var t=document.querySelectorAll("th");[].forEach.call(t,function(t){t.addEventListener("dragstart",listenerDragStart,!1),t.addEventListener("dragenter",listenerDragEnter,!1),t.addEventListener("dragover",listenerDragOver,!1),t.addEventListener("dragleave",listenerDragLeave,!1),t.addEventListener("drop",listenerDrop,!1),t.addEventListener("dragend",listenerDragEnd,!1)})});

    $("table.table_column_switch thead tr th").attr("draggable",!0)
    function listenerCustomFuc() {
        $( ".erp_form__grid" ).colResizable({ disable : true });
        $( ".erp_form__grid_header>tr>th").each(function(){
            var val = $(this).width();
            colWidth.push(val);
        });
        funcGridThResize(colWidth);
        funcFormWise();
    }
</script>
