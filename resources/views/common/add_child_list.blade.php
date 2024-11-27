@foreach($parents as $parent)
    @php $name .= " -> ".$name; @endphp
    <option value="{{$id}}">{{$name}}</option>
    @if(count($parent->child_rec))
        @include('common.add_child_list',['parents' => $parent->child_rec, 'id' => $id, 'name'=> $name] )
    @endif
@endforeach
