@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {{strtoupper($data['title'])}}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-lg-12" id="user_settings">
                    <div class="alert alert-secondary" role="alert">
                        <div class="alert-icon">
                            <i class="flaticon-warning kt-font-brand"></i>
                        </div>
                        <div class="alert-text" id="query">
                            {{$data['sql']}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-block text-right" id="user_settings">
                <div class="col-lg-3">
                    <div class="kt-input-icon kt-input-icon--left">
                        <input type="text" class="form-control" placeholder="Search..." id="generalSearch">
                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                            <span><i class="la la-search"></i></span>
                        </span>
                    </div>
                </div>
                <div class="col-lg-3"></div>
                <div class="col-lg-3"></div>
                <div class="col-lg-3">
                    <button class="btn btn-sm btn-primary" id="btnPrint" onclick="window.print()">
                        <i class="la la-print"></i>Print
                    </button>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    Total Record: {{count($data['all'])}}
                    <table id="rep_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            @if(isset($data['headings']))
                                @foreach($data['headings'] as $heading)
                                    <th>@php echo $heading; @endphp</th>
                                @endforeach
                            @endif
                        </tr>
                        @if(isset($data['all']))
                            @foreach($data['all'] as $val)
                                <tr>
                                    @foreach($data['cols'] as $col)
                                        <td>{{$val->$col}}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot">
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User: </span>{{auth()->user()->name}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script>
        var str = $('#query').text().split("Select").join('<span style="color:#ff0000;">Select</span>')
            .split("where").join('<span style="color:#ff0000;">where</span>')
            .split("from").join('<span style="color:#ff0000;">from</span>')
            .split("OR").join('<span style="color:#ff0000;">OR</span>')
            .split("like").join('<span style="color:#0000ff;">like</span>')
            .split("AND").join('<span style="color:#ff0000;">AND</span>')
            .split("is null").join('<span style="color:#e400ff;">is null</span>')
            .split("is not null").join('<span style="color:#e400ff;">is not null</span>');
        $('#query').html(str);

        // Html Table Sorting ASC and DESC
        const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
        const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
        )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
        // do the work...
        document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
            const table = th.closest('table');
            Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                .forEach(tr => table.appendChild(tr) );
        })));
    </script>
@endsection

