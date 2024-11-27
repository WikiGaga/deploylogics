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
    var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
    function listenerDragStart(e) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text', $(this).text());

        if (!isIE11) {
            var currentTable = $(this).parents('table.table_column_switch')
            console.log(currentTable.find('tbody'));
            //Create column's container
            var dragGhost = document.createElement("table");
            dragGhost.classList.add("tableGhost");
            dragGhost.classList.add("table-bordered");
            //in order tor etrieve the column's original width
            var srcStyle = document.defaultView.getComputedStyle(this);
            dragGhost.style.width = srcStyle.getPropertyValue("width");

            //Create head's clone
            var theadGhost = document.createElement("thead");
            var thisGhost = this.cloneNode(true);
            thisGhost.style.backgroundColor = "red";
            theadGhost.appendChild(thisGhost);
            dragGhost.appendChild(theadGhost);

            var srcTxt = $(this).text();
            var srcIndex = $("th:contains(" + srcTxt + ")").index() + 1;
            //Create body's clone
            currentTable.find('tbody').each(function(){
                var tbodyGhist = document.createElement("tbody");
                $.each($(this).find('tr td:nth-child(' + srcIndex + ')'), function (i, val) {
                    var currentTR = document.createElement("tr");
                    var currentTD = document.createElement("td");
                    currentTD.innerText = $(this).text();
                    currentTR.appendChild(currentTD);
                    tbodyGhist.appendChild(currentTR);
                });
                dragGhost.appendChild(tbodyGhist);
            });

            //Hide ghost
            dragGhost.style.position = "absolute";
            dragGhost.style.top = "-1500px";

            document.body.appendChild(dragGhost);
            e.dataTransfer.setDragImage(dragGhost, 0, 0);
        }
    }

    function listenerDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function listenerDragEnter(e) {
        this.classList.add('over');
    }

    function listenerDragLeave(e) {
        this.classList.remove('over');
    }

    function listenerDrop(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        var srcTxt = e.dataTransfer.getData('text');
        var destTxt = $(this).text();
        var currentTable = $(this).parents('table.table_column_switch');
        if (srcTxt != destTxt) {
            var dragSrcEl = currentTable.find("th:contains(" + srcTxt + ")");
            var srcIndex = dragSrcEl.index() + 1;
            var destIndex = $("th:contains(" + destTxt + ")").index() + 1;
            dragSrcEl.insertAfter($(this));
            currentTable.find('tbody').each(function(ti){
                var thix = $(this);
                $.each(thix.find('tr td:nth-child(' + srcIndex + ')'), function (i, val) {
                    var index = i + 1;
                    $(this).insertAfter(thix.find('tr:nth-child(' + index + ') td:nth-child(' + destIndex + ')'));
                });
            });
            listenerCustomFuc();
        }
        return false;
    }

    function listenerDragEnd(e) {
        var cols = document.querySelectorAll('th');
        [].forEach.call(cols, function (col) {
            col.classList.remove('over');
            col.style.opacity = 1;
        });
    }

    $(document).ready(function () {
        var cols = document.querySelectorAll('th');
        [].forEach.call(cols, function (col) {
            col.addEventListener('dragstart', listenerDragStart, false);
            col.addEventListener('dragenter', listenerDragEnter, false);
            col.addEventListener('dragover', listenerDragOver, false);
            col.addEventListener('dragleave', listenerDragLeave, false);
            col.addEventListener('drop', listenerDrop, false);
            col.addEventListener('dragend', listenerDragEnd, false);
        });
    });

    $("table.table_column_switch thead tr th").attr("draggable", true);

    function listenerCustomFuc() {
        $( ".erp_form__grid" ).colResizable({ disable : true });
        $( ".erp_form__grid_header>tr>th").each(function(){
            var val = $(this).width();
            colWidth.push(val);
        });
        funcGridThResize([]);
        funcFormWise();
    }
</script>
