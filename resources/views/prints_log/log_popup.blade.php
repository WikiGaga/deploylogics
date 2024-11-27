@php
    $current = $data['current'];
    $title = $data['title'];
    // dd($current->toArray());
@endphp
<div class="modal-content">
    <div class="modal-header" style="padding: 5px 16px;">
        <h5 class="modal-title" id="exampleModalLabel">{{$title}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if(count($current) != 0)
            <table class="table log_table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                <tr>
                    <th>Date</th>
                    <th>Activity Form</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Branch</th>
                </tr>
                <tbody>
                @foreach($current as $item)
                    <tr>
                        <td><a href="/{{$data['prefix_url']}}/log-print/{{$item->user_activity_log_id}}" target="_blank">{{date('d-m-Y H:i:s', strtotime($item->created_at))}}</a></td>
                        <td>{{$item->activity_form_type}}</td>
                        <td>{{$item->action_type}}</td>
                        <td>{{$item->user['name']}}</td>
                        <td>{{$item->branch['branch_name']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div>Logs not founds...</div>
        @endif
    </div>
</div>
