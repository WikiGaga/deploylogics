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
        input{
            height: 20px;
            width: 300px;
            margin-bottom: 10px;
        }
        select {
            height: 27px;
            width: 308px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div style="margin-bottom: 10px">
    <a href="{{route('db.db_table')}}">
        <button type="button"> <-- Table List</button>
    </a>
    <a href="{{route('db.db_table_dtl',$data['tbl'])}}">
        <button type="button"> <-- Redirect To : {{$data['tbl']}}</button>
    </a>
</div>
<form method="post" action="{{route('db.dbTableStoreColumn',$data['tbl'])}}" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <div>
        <div>Column Name</div>
        <input type="text" name="column_name">
    </div>
    <div>
        <div>Column Data Type</div>
        <select name="column_type">
            <option value="char2">varchar2(255)</option>
            <option value="num">number(22)</option>
            <option value="date">date(timestamp)</option>
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>
</body>
</html>
