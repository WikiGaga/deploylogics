<!-- <div style="position:relative;
width:{{ $template->width_px }}px;
height:{{ $template->height_px }}px;
border:1px solid black;">

@foreach($fields as $field)

<div style="
position:absolute;
top:{{ $field->top_px }}px;
left:{{ $field->left_px }}px;
width:{{ $field->width_px }}px;
height:{{ $field->height_px }}px;
font-size:{{ $field->font_size }}px;">

@if($field->field_name == 'amount_words')
{{ $inputs['amount_words'] }}
@else
{{ $inputs[$field->field_name] ?? 'zz' }}
@endif

</div>

@endforeach
</div> -->

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- <style>
    body { margin: 0; padding: 0; }
    #cheque {
        position: relative;
        width: {{ $template->width_px }}px;
        height: {{ $template->height_px }}px;
        border: 1px solid #000;
    }
    .field {
        position: absolute;
        font-family: Arial, sans-serif;
    }
</style> -->
<style>
    @page {
        margin: 0px;
    }

    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }

    #cheque {
        position: relative;
        width: {{ $template->width_px }}px;
        height: {{ $template->height_px }}px;
    }

    .field {
        position: absolute;
        font-family: Arial, sans-serif;
        white-space: nowrap;
    }
</style>

</head>
<body>

<div id="cheque">

@foreach($fields as $field)

<div class="field"
     style="
        top: {{ $field->top_px }}px;
        left: {{ $field->left_px }}px;
        width: {{ $field->width_px }}px;
        height: {{ $field->height_px }}px;
        font-size: {{ $field->font_size ?? 14 }}px;
     ">

@if($field->field_name == 'amount_words')
    {{ $inputs['amount_words'] }}
@else
    {{ $inputs[$field->field_name] ?? '' }}
@endif

</div>

@endforeach

</div>

</body>
</html>

