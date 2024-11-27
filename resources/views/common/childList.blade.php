

@foreach($parents as $parent)
    @php $n .= " -> ".$parent->group_item_name; @endphp
    <option value="{{$parent->group_item_id}}" {{$current ==$parent->group_item_id?"selected":"" }}>{{$n}}</option>
    @if(count($parent->child_rec))
        @include('common.childList',['parents' => $parent->child_rec])
    @endif
@endforeach
