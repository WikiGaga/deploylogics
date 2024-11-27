<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Database') }}</title>
    <style>
        td a {
            display: block;
            width: 100%;
            height: 100%;
        }
        td:hover{
            background: #f8f8f8;
        }
    </style>
</head>
<body>
<table border="1" width="100%">
    <thead>
        <tr>
            <th width="10%">Sr#</th>
            <th width="90%">Table Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['table'] as $k=>$tbl)
            <tr>
                <td>{{$k+1}}</td>
                <td><a href="/db/dtl/{{strtolower($tbl->table_name)}}">{{strtolower($tbl->table_name)}}</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
