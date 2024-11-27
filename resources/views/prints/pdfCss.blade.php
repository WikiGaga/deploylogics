<style>
*{font-family:dejavu sans, Verdana, Geneva, Arial, sans-serif; }
.tab{border-spacing:0; border-collapse:collapse; width:100%; text-align:center;}
.tab ,td{padding:0; spacing:0;}
.tabData{border-spacing:0; border-collapse:collapse; padding:5px; width:100%; text-align:center; border:1px solid #000;}
.tableData{border-spacing:0; border-collapse:collapse; padding:5px; width:100%; border:1px solid #000;}
.tableData>tbody>tr:first-child>td{vertical-align: top;}
.company{ color:#000;  font-size:15px; font-weight:bold;}
.title{font-size:16px; font-weight:bold; padding:1px 0;}
.heading{font-size:12px; font-weight:bold; padding:2px;}
.dtl-head{font-weight:bold;font-size:12px;vertical-align:middle;border-right:1px solid #000;border-bottom:1px solid #000; height:22px; padding:0 3px;background-color:#f9f9f9; color:#000;}
.dtl-contents{border-right:1px solid #000;border-bottom:1px dotted #000;font-weight:normal;font-size:11px; padding:3px 3px;}
.dtl-bottom{font-size:12px; border-right:1px solid #000;  border-top:1px solid #000; font-weight:bold; padding:3px 3px;}
.normal{font-weight:normal; font-size:11px; padding:2px;}
.normal-bold{font-weight:bold; font-size:11px; padding:2px;}
.alignleft{text-align:left;}
.alignright{text-align:right;}
.aligncenter{text-align:center;}
.paddingNotes{padding-left:45px;}
.heading-block{width: 40%; display: inline-block; vertical-align: top;}
.normal-block{width: 50%; display: inline-block;}
.mrgn-top{margin-top:100px;}
.sign-line{height:1.4px;border-width:0;color:black;background-color:black;width:70%;text-align:center;}
.fixed-layout{table-layout:fixed;}
#Top-Header{
    padding: 5px 16px;
    background: #FFF4DE;
    color: #000000;
    margin-bottom: 10px;
    font-size: 8px;
}
.sticky {
    position: fixed;
    top: 0;
    width:1301px;
    box-shadow: 0 5px 5px 0 rgba(0, 0, 0, 0.3);
}
button#btn_toggle {
    cursor: pointer;
    background: #d8d8d8;
    position: relative;
    border: 1px solid #eae2e2;
    padding: 0;
    top: 7px;
}
button#btn_toggle:focus{
    outline: none;
}
.toggle_table_column {
    float: right;
    margin-bottom: 15px;
}
.table_column_dropdown{
    position: relative;
    box-shadow: 0px 0px 50px 0px rgb(82 63 105);
}
ul.table_column_dropdown-menu>li {
    list-style: none;
}

ul.table_column_dropdown-menu {
    width: 200px;
    height: 200px;
    overflow: auto;
    position: absolute;
    background: #fff;
    padding: 0;
    border: 1px solid lightgrey;
    right: 0;
    top: -13px;
}

ul.table_column_dropdown-menu>li>label {
    display: block;
    padding: 2px 10px;
    clear: both;
    font-weight: normal;
    line-height: 1.42857143;
    color: #333;
    white-space: nowrap;
    margin: 0;
    transition: background-color .4s ease;
    font-size: 12px;
}
/*****for Thermal print***********/
.thermal{width:288px;text-align: center;}
.thermal-title{font-size:11px; font-weight:bold; padding:1px 0;}
.thermal-dtl-head{font-weight:bold;font-size:11px;vertical-align:middle;border-top:1px solid #000;border-bottom:1px solid #000; height:22px; padding:0 3px;color:#000;}
.thermal-dtl-contents{font-weight:normal;font-size:10px; padding:3px 3px;}
.thermal-heading{font-size:11px; font-weight:bold; padding:2px;}
.thermal-normal{font-weight:normal; font-size:10px; padding:2px;}
.thermal-print-date{font-weight:normal; font-size:9px; padding:1px;}

@media print{
    .toggle_table_column,
    #Top-Header,#print_btn{display:none;}
}


</style>
