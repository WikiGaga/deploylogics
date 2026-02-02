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
<!-- 
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
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
 -->

 <!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin: 0px;
    }

    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        background-color: transparent;
    }

     #cheque {
            position: relative;
            width: {{ $template->width_px }}px;
            height: {{ $template->height_px }}px;
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

    .field {
        position: absolute;
        font-family: Arial, sans-serif;
    }

    /* 1. FORCE DATE TO STAY ON ONE LINE */
    /* .date-container {
        width: 100%;
        height: 100%;
        white-space: nowrap !important; 
        overflow: hidden;
    } */

    /* .date-char {
        display: inline-block;
        text-align: center;
        margin: 0;
        padding: 0;
    }  */

    /* 2. ALLOW AMOUNT WORDS TO BREAK INTO LINES */
    .field-amount_words {
        white-space: normal !important; 
        word-wrap: break-word;
        line-height: 1.2;
    }
</style>
</head>
<body>

<div id="cheque">
@foreach($fields as $field)
    @php
        $val = $inputs[$field->field_name] ?? '';
        $isDate = ($field->field_name == 'date');
        $isWords = ($field->field_name == 'amount_words');
    @endphp

    <div class="field {{ $isWords ? 'field-amount_words' : '' }}"
         style="
            top: {{ $field->top_px }}px;
            left: {{ $field->left_px }}px;
            width: {{ $field->width_px }}px;
            height: {{ $field->height_px }}px;
            font-size: {{ $field->font_size ?? 14 }}px;
            @if($isDate) white-space: nowrap; @endif
         ">

        @if($isDate)
            @php 
                // Ensure exactly 8 digits or characters
                $digits = str_split(str_pad($val, 8, ' ', STR_PAD_RIGHT)); 
                $charWidth = floor($field->width_px / 8); 
            @endphp
            <div class="date-container">
                @foreach($digits as $char)
                    <span class="date-char" style="width: {{ $charWidth }}px;">{{ $char }}</span>
                @endforeach
            </div>

        @elseif($isWords)
            {{-- This field will wrap because of the .field-amount_words CSS --}}
            {{ strtoupper($val) }}

        @else
            {{ $val }}
        @endif

    </div>
@endforeach
</div>

</body>
</html>
