{{--<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
    <button type="button" class="btn btn-outline-info">Approve</button>
    <button type="button" class="btn btn-outline-info">Post</button>
    <button type="button" class="btn btn-outline-info">Archive</button>
</div>--}}
@if(count($data['stg']['btns']) == 0 && $data['stg']['current_stg_id'] == '')
    <button type="submit" data-id="{{$header_data['action_id']}}" class="btn btn-sm btn-success">{{$header_data['action']}}</button>
@endif
@if(count($data['stg']['btns']) != 0 && $data['stg']['staging_apply'] == 0)
    <div class="dropdown dropdown-inline">
        <button type="button" class="btn btn-sm btn-success btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Select Actions
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <ul class="kt-nav">
                @foreach($data['stg']['btns'] as $btn )
                    <li class="kt-nav__item">
                        <button type="submit" data-id="{{$btn->stg_actions_id}}" class="btn btn-sm btn-default" style="width: 100%;padding: 0.55rem 1.75rem;border:none;">{{$btn->stg_actions_name}}</button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
