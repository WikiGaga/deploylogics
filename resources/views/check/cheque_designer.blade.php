



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Designer Live Preview</title>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
   
</head>
<body class="bg-gray-50 p-8">

<h2>Cheque Designer: {{ $layout->name }}</h2>
<div id="cheque" style="width:{{ $layout->width_px }}px; height:{{ $layout->height_px }}px; border:1px solid #000; position:relative;">

@foreach($fields as $field)
    <input class="draggable" data-field="{{ $field->field_name }}" value="{{ ucfirst(str_replace('_',' ',$field->field_name)) }}"
           style="position:absolute; top:{{ $field->top_px }}px; left:{{ $field->left_px }}px; width:{{ $field->width_px }}px; height:{{ $field->height_px }}px; font-size:{{ $field->font_size }}px;">
@endforeach

</div>
<button id="saveLayout">Save Layout</button>

   
<script>
$('.draggable').draggable({ containment: "#cheque" }).resizable({ containment: "#cheque" });

$('#saveLayout').click(function () {
    let fields = [];
    $('.draggable').each(function () {
        fields.push({
            field_name: $(this).data('field'),
            top_px: $(this).position().top,
            left_px: $(this).position().left,
            width_px: $(this).width(),
            height_px: $(this).height(),
        });
    });

    $.post("{{ route('cheque.layout.save') }}", {
        _token: "{{ csrf_token() }}",
        layout_id: {{ $layout->id }},
        fields: fields
    }, function(res) { alert('Layout Saved!'); });
});
</script>
</body>
</html>