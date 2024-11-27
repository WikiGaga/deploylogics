$(document).on('click','.add_data',function(){
    var cd = console.log;
    var thix = $(this);
    var tr = thix.parents('tr');
    var lenTh = tr.find('th').length;
    var nameAttrPrefix = thix.parents('table').attr('data-prefix');
    var tbody = thix.parents('table').find('.erp_form__grid_body');
    var bodyLenTr = tbody.find('tr').length + 1;
    var selectElements = [];
    for(var v=0;v < lenTh;v++){
        var field = tr.find('th:eq('+v+') input');
        var fieldTitle = tr.find('th:eq('+v+') .erp_form__grid_th_title').text().trim();
        var newValue = field.val();
        if(field.attr('data-require')=='true' && newValue==""){
            alert(fieldTitle+" is required");
            return false;
        }
        if(v==0){
            for (var c=0;c<field.length;c++){
                var id = "#"+field[c].id;
                var newValue = tr.find(id).val();
                var data_require = tr.find(id).attr("data-require");
                var msg = tr.find(id).attr("data-msg");
                if(data_require == "true" && newValue==""){
                    alert(msg);
                    return false;
                }
            }
        }
    }
    var tds = "";
    var notUseAttr = ['id','data-require','data-msg','data-url','data-readonly','data-help','data-convert','data-convert-id'];
    for(var i=0;i < lenTh;i++){
        var field = tr.find('th:eq('+i+') input');
        if(field.length == 0){
            var field = tr.find('th:eq('+i+') select');
        }
        var inputs = "";
        var td = "<td>";
        var extraClass = "";
        if(i == 0){
            for (var c=0;c<field.length;c++){
                var attributes = field[c].attributes;
                inputs += "<input ";
                for (var d=0;d<attributes.length;d++){
                    var name = attributes[d].name;
                    var value = attributes[d].value;
                    if(value != ""){
                        if(!notUseAttr.includes(name)){
                            inputs += name+"='"+value+"' ";
                        }
                    }
                    if(name == 'readonly'){
                        inputs += " " +name +" ";
                    }
                    if(name == 'id'){ id = value; }
                    if(value == 'sr_no'){ inputs += "value='"+bodyLenTr+"'"; }
                }
                inputs += " name='"+nameAttrPrefix+"["+bodyLenTr+"]["+id+"]'";
                inputs += " data-id='"+id+"'";
                inputs += " >";
            }
        }
        if(i != 0 && (lenTh-1) != i){
            var nodeName = field[0].nodeName;
            var attributes = field[0].attributes;
            var type = field[0].attributes;

            var id = "#"+field[0].id;
            var newValue = tr.find(id).val();

            if(nodeName == "SELECT"){
                newValue = tr.find(id).find("option:selected").text();
                var convert_id_arr = {
                    'data_id' : tr.find(id).attr('data-convert-id'),
                    'data_val' : tr.find(id).find("option:selected").val()
                }
                selectElements.push(convert_id_arr);
            }
            var data_readonly = "";
            inputs += "<input ";
            for (var d=0;d<attributes.length;d++){
                var name = attributes[d].name;
                var value = attributes[d].value;
                if(value != ""){
                    if(!notUseAttr.includes(name)){
                        inputs += name+"='"+value+"' ";
                    }
                }
                if(name == 'readonly'){
                    inputs += name +" ";
                }
                if(name == 'id'){ id = value; }
                if(name == "data-readonly" && value=="true"){
                    data_readonly = "true";
                }
            }
            inputs += "name='"+nameAttrPrefix+"["+bodyLenTr+"]["+id+"]'";
            inputs += "value='"+newValue+"'";
            inputs += "data-id='"+id+"'";
            if(data_readonly != ""){
                inputs += "readonly='true' ";
            }
            if(nodeName == "SELECT"){
                inputs += "type='text' ";
            }
            inputs += " >";
        }
        if((lenTh-1) == i){
            inputs = '<div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button></div>';
            var td = '<td class="text-center">';
        }
        td += inputs+"</td>";
        tds += td;
    }
    tbody.append('<tr>'+tds+'</tr>');
    var lastTr =  tbody.find('tr:last-child');
    selectElements.forEach(function(item){
        $(lastTr).find("input[data-id='"+item['data_id']+"']").attr('value',item['data_val']);
    })
    mformClear(tr)
});
$(document).on('click','.del_row',function(){
    var thix = $(this);
    var table = thix.parents('table');
    $(this).parents("tr").remove();
    mupdateKeys(table);
});
function mformClear(tr){
    tr.find('input').val("");
    tr.find('input[type="radio"]').prop('checked', false);
    tr.find('select').prop('selectedIndex',0);
}
function mupdateKeys(table){
    var cd = console.log;
    var tbody = table.find('.erp_form__grid_body');
    var total_length = tbody.find('tr').length + 1;
    var nameAttrPrefix = table.attr('data-prefix');
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            var tr = table.find('.erp_form__grid_body tr:eq('+i+')');
            var j = i+1;
            $(tr.find('td:eq(0) input[type="hidden"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name',nameAttrPrefix+'['+j+']['+data_id+']');
            });
            $(tr.find('td input[type="text"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',nameAttrPrefix+'['+j+']['+data_id+']');
            });
            $(tr.find('td input[type="radio"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',nameAttrPrefix+'['+j+'][action]');
            });
            $(tr.find('td select')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',nameAttrPrefix+'['+j+']['+data_id+']');
            });
            tr.find('td:eq(0) input[data-id="sr_no"]').val(j);
        }
    }
}
/*$(document).on('change','.select_node',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var data_convert_id = thix.attr('data-convert-id');
    tr.find('#'+data_convert_id).val(thix.val());
})*/
