<div style="position:relative; width:{{ $layout->width_px }}px; height:{{ $layout->height_px }}px; border:1px solid #000;">
@foreach($fields as $field)
    <div style="position:absolute; top:{{ $field->top_px }}px; left:{{ $field->left_px }}px; width:{{ $field->width_px }}px; height:{{ $field->height_px }}px; font-size:{{ $field->font_size }}px;">
        {{ $field->field_name == 'amount_words' ? $inputs['amount_words'] : ($inputs[$field->field_name] ?? '') }}
    </div>
@endforeach
</div>
