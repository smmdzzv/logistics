<!doctype html>
<html>
<head> <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ asset('js/main.js') }}" defer></script>
    <style>
        body {
            font-family: Arial;
            font-size: 12px;
            background: white;
        }
        @page {
            size: USER;
            margin: 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                font-weight: bold;
            }
            .page {
                margin: 0;
                padding: 15px 0 0 15px;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }

        }
    </style>
</head>
<body>
<div id="app">
@foreach($storedItems as $storedItem)
    <div class="page">
        Дата: {{$storedItem->created_at }}<br>
        Товар: {{$storedItem->info->item->name}}<br>
        Вес: {{$storedItem->info->weight}} кг | ШхВхД: {{$storedItem->info->width}}x{{$storedItem->info->height}}x{{$storedItem->info->length}}<br>
        Владелец: {{$storedItem->info->owner->code}} <br>
        ID: {{$storedItem->code}}<br>

        <div style="margin-left: -8px">
            <barcode :options='{displayValue:false, height:40, width:1.5}' value="{{$storedItem->code}}" tag="svg"></barcode>
        </div>
    </div>
@endforeach
</div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        window.print();
    }, false);
</script>
</html>
