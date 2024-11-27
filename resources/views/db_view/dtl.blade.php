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
<div style="margin-bottom: 10px">
    <a href="{{route('db.db_table')}}">
        <button type="button"> <-- Table List</button>
    </a>
    <a href="{{route('db.dbTableCreateColumn',$data['tbl'])}}">
        <button type="button"> Create New Column</button>
    </a>
</div>

<table border="1" width="100%">
    <thead>
    <tr>
        <th width="10%">Sr#</th>
        <th width="20%">Table Name</th>
        <th width="30%">Column Name</th>
        <th width="20%">Data Type</th>
        <th width="20%">Data Length</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data['table'] as $k=>$tbl)
        <tr>
            <td>{{$k+1}}</td>
            <td>{{strtolower($tbl->table_name)}}</td>
            <td>{{strtolower($tbl->column_name)}}</td>
            <td>{{strtolower($tbl->data_type)}}</td>
            <td>{{strtolower($tbl->data_length)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
