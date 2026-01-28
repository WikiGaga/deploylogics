<!-- <!DOCTYPE html>
<html>
<head>
    <title>Design Cheque Template</title>

    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .draggable {
            touch-action: none;
            user-select: none;
            background: rgba(59, 130, 246, 0.1);
            border: 1px dashed #3b82f6;
            padding: 6px;
            cursor: move;
        }

        #check-canvas {
            background-color: #fff;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>

<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">

<h2 class="text-2xl font-bold mb-4">
    Design Template: {{ $template->name }}
</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">


<div class="bg-white p-6 rounded shadow space-y-4">
    <h3 class="font-semibold border-b pb-2">Canvas Size (px)</h3>

    <div class="flex gap-2">
        <input type="number" id="canvas-w" value="{{ $template->width_px }}"
               class="w-1/2 border p-2 rounded">

        <input type="number" id="canvas-h" value="{{ $template->height_px }}"
               class="w-1/2 border p-2 rounded">
    </div>

    <button id="saveTemplate"
            class="w-full bg-green-600 text-white py-3 rounded-lg font-bold">
        Save Template Layout
    </button>
</div>


<div class="md:col-span-2">
<div class="overflow-auto border-2 border-gray-300 rounded-lg p-4 bg-gray-200">

<div id="check-canvas"
     class="relative shadow-2xl mx-auto border border-gray-400"
     style="width: {{ $template->width_px }}px;
            height: {{ $template->height_px }}px;">

@foreach($fields as $field)

<div class="draggable absolute"
     id="field-{{ $field->field_name }}"
     data-field="{{ $field->field_name }}"
     data-x="{{ $field->left_px }}"
     data-y="{{ $field->top_px }}"
     style="transform: translate({{ $field->left_px }}px, {{ $field->top_px }}px)">

    <span class="text-gray-400 text-xs block">
        {{ ucfirst(str_replace('_',' ', $field->field_name)) }}
    </span>

    <span class="font-bold">
        SAMPLE TEXT
    </span>

</div>

@endforeach

</div>
</div>

</div>

<script>
const canvas = document.getElementById('check-canvas');

document.getElementById('canvas-w').addEventListener('input', e => {
    canvas.style.width = e.target.value + 'px';
});

document.getElementById('canvas-h').addEventListener('input', e => {
    canvas.style.height = e.target.value + 'px';
});


interact('.draggable').draggable({
    listeners: {
        move (event) {
            const target = event.target;

            let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            target.style.transform = `translate(${x}px, ${y}px)`;
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
        }
    },
    modifiers: [
        interact.modifiers.restrictRect({
            restriction: 'parent'
        })
    ]
});

document.getElementById('saveTemplate').addEventListener('click', function(){

    let fields = [];

    document.querySelectorAll('.draggable').forEach(el => {
        fields.push({
            field_name: el.dataset.field,
            left_px: parseFloat(el.getAttribute('data-x')),
            top_px: parseFloat(el.getAttribute('data-y')),
            width_px: el.offsetWidth,
            height_px: el.offsetHeight
        });
    });

    fetch('/cheque/template/save-layout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            template_id: {{ $template->id }},
            fields: fields
        })
    })
    .then(r => r.json())
    .then(() => alert('Template Saved Successfully!'));
});
</script>

</body>
</html> -->



@extends('layouts.template')
@section('title', 'Design Cheque Template')

@section('pageCSS')
    <link href="/assets/css/pages/wizard/wizard-1.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .draggable {
            touch-action: none;
            user-select: none;
            background: rgba(59, 130, 246, 0.1);
            border: 1px dashed #3b82f6;
            padding: 6px;
            cursor: move;
        }

        #check-canvas {
            background-color: #fff;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
@endsection

@section('content')
    @php

    @endphp
   
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
             
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid kt-wizard-v1 kt-wizard-v1--white bg-gray-100 p-8" id="kt_wizard_v1" data-ktwizard-state="step-first">
                    
                       
                        <div class="max-w-6xl mx-auto">

                        <h2 class="text-2xl font-bold mb-4">
                            Design Template: {{ $template->name }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <!-- LEFT PANEL -->
                        <div class="bg-white p-6 rounded shadow space-y-4">
                            <h3 class="font-semibold border-b pb-2">Canvas Size (px)</h3>

                            <div class="flex gap-2">
                                <input type="number" id="canvas-w" value="{{ $template->width_px }}"
                                    class="w-1/2 border p-2 rounded">

                                <input type="number" id="canvas-h" value="{{ $template->height_px }}"
                                    class="w-1/2 border p-2 rounded">
                            </div>

                            <button id="saveTemplate"
                                    class="w-full bg-green-600 text-white py-3 rounded-lg font-bold">
                                Save Template Layout
                            </button>
                            <label>Date X:</label>
                            <input id='field-date_x' name='date_x' val="" readonly><br>
                            <label>Date Y:</label>
                            <input id='field-date_y' name='date_y' val="" readonly>
                            <br>

                            <label>Account Title X:</label>
                            <input id='field-account_title_x' name='account_title_x' val="" readonly><br>
                            <label>Account Title Y:</label>
                            <input id='field-account_title_y' name='account_title_y' val="" readonly>
                            <br>

                            <label>Amount X:</label>
                            <input id='field-amount_x' name='amount_x' val="" readonly><br>
                            <label>Amount Y:</label>
                            <input id='field-amount_y' name='amount_y' val="" readonly>
                            <br>

                            <label>Amount In Words X:</label>
                            <input id='field-amount_words_x' name='amount_words_x' val="" readonly><br>
                            <label>Amount In Words Y:</label>
                            <input id='field-amount_words_y' name='amount_words_y' val="" readonly>
                        </div>

                        <!-- RIGHT CANVAS -->
                        <div class="md:col-span-2">
                        <div class="overflow-auto border-2 border-gray-300 rounded-lg p-4 bg-gray-200">

                        <div id="check-canvas"
                            class="relative shadow-2xl mx-auto border border-gray-400"
                            style="width: {{ $template->width_px }}px;
                                    height: {{ $template->height_px }}px;">

                        @foreach($fields as $field)

                        <div class="draggable absolute"
                            id="field-{{ $field->field_name }}"
                            data-field="{{ $field->field_name }}"
                            data-x="{{ $field->left_px }}"
                            data-y="{{ $field->top_px }}"
                            style="transform: translate({{ $field->left_px }}px, {{ $field->top_px }}px)">
                       
                            <span class="text-gray-400 text-xs block">
                                {{ ucfirst(str_replace('_',' ', $field->field_name)) }}
                            </span>

                            <span class="font-bold">
                                SAMPLE TEXT
                            </span>

                        </div>

                        @endforeach

                        </div>
                        </div>

                        </div>



                     
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/business.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')

<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Loop through all draggables and update their respective input boxes
        document.querySelectorAll('.draggable').forEach(el => {
            const fieldName = el.getAttribute('data-field');
            const x = el.getAttribute('data-x');
            const y = el.getAttribute('data-y');

            // Update the sidebar inputs
            if(document.getElementById(`field-${fieldName}_x`)) {
                document.getElementById(`field-${fieldName}_x`).value = x;
                document.getElementById(`field-${fieldName}_y`).value = y;
            }
        });
    });
const canvas = document.getElementById('check-canvas');

document.getElementById('canvas-w').addEventListener('input', e => {
    canvas.style.width = e.target.value + 'px';
});

document.getElementById('canvas-h').addEventListener('input', e => {
    canvas.style.height = e.target.value + 'px';
});

// ===== DRAG LOGIC (INTERACT.JS) =====
interact('.draggable').draggable({
    listeners: {
        move (event) {
            const target = event.target;

            let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
            let field_name = target.getAttribute('id')

            $('#'+field_name+'_x').val(x);
            $('#'+field_name+'_y').val(y);

            // console.log(x,y,target.getAttribute('id'));

            target.style.transform = `translate(${x}px, ${y}px)`;
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
        }
    },
    modifiers: [
        interact.modifiers.restrictRect({
            restriction: 'parent'
        })
    ]
});

// ===== SAVE TEMPLATE TO LARAVEL =====
document.getElementById('saveTemplate').addEventListener('click', function(){

    let fields = [];

    document.querySelectorAll('.draggable').forEach(el => {
        fields.push({
            field_name: el.dataset.field,
            left_px: parseFloat(el.getAttribute('data-x')),
            top_px: parseFloat(el.getAttribute('data-y')),
            width_px: el.offsetWidth,
            height_px: el.offsetHeight
        });
    });

    fetch('/cheque/template/save-layout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            template_id: {{ $template->id }},
            fields: fields
        })
    })
    .then(r => r.json())
    .then(() => alert('Template Saved Successfully!'));
});
</script>

@endsection
