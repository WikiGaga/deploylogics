@extends('layouts.template')
@section('title', 'Staging Dashboard')

@section('pageCSS')
@endsection

@section('content')
    @permission(['flow-dash-view'])
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Flow Dashboard
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <ul class="erp-main-nav nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                    @foreach($data['menu'] as $menu)
                        <li class="nav-item">
                            <a class="nav-link {{$loop->first?" active":""}}" data-toggle="tab" href="#{{str_replace(' ','_',$menu->menu_name)}}" role="tab">
                                {{$menu->menu_name}}
                                @if(isset($data['flows_menu_dtl'][$menu->menu_id]['document_count']) && array_sum($data['flows_menu_dtl'][$menu->menu_id]['document_count']) > 0)
                                    <span class="kt-badge kt-badge--danger">{{ array_sum($data['flows_menu_dtl'][$menu->menu_id]['document_count']) }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($data['menu'] as $menu)
                        <div class="tab-pane {{$loop->first?" active":""}}" id="{{str_replace(' ','_',$menu->menu_name)}}" role="tabpanel">
                            @foreach($data['flows_menu_dtl'] as $k=>$flows_menu_dtls)
                                @if(isset($menu->menu_id) && $menu->menu_id == $k)
                                    @foreach($flows_menu_dtls['data'] as $flows_menu_dtl)
                                        @if(isset($flows_menu_dtl['rows']) && count($flows_menu_dtl['rows']) > 0)
                                            <div class="flow_group_block">
                                                <div class="form-group row" style="background: #eff0ff">
                                                    <div class="erp-col-form-label col-lg-12">
                                                        <div>{{$flows_menu_dtl['flow']['stg_flows_name']}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table">
                                                <tbody>
                                                    @foreach($flows_menu_dtl['rows'] as $k=>$m_dtl)
                                                        <tr onclick="window.open('/staging-dashboard/{{$k}}', '_blank')" style="cursor: pointer">
                                                            <td width="90%">
                                                                <i class="fa fa-caret-right"></i>
                                                                <span style="margin-left:10px;display:inline-block;font-weight: 500;">{{$m_dtl['name']}}</span>
                                                            </td>
                                                            <td width="10%">
                                                                <span class="kt-badge kt-badge--danger kt-badge--md kt-badge--rounded">{{ $m_dtl['count'] ?? 0 }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
