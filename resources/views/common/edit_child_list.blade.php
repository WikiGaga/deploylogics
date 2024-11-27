{{--@foreach($parents as $parent)
    @php $n .= " -> " .$parent->display_location_name; @endphp
    <option value="{{$parent->display_location_id}}" {{$parent->display_location_id == $current ?"selected":""}}>{{$n}}</option>
    @if(count($parent->childern))
        @include('common.edit_child_list',['parents' => $parent->childern] )
    @else
        @php
            $str_arr = explode( "->" , $n );
            $str_arr=array_slice($str_arr,0,count($str_arr)-$a);
            print_r($str_arr);
            $str_imp = implode("-> ", $str_arr);
            $n = "";
            $n  = $str_arr;
        @endphp
    @endif
@endforeach--}}

@php $n .= " -> " .$parents->display_location_name; @endphp
{{$n}}
@if($parent->parent != null)
    @include('common.edit_child_list',['parents' => $parent->parent, 'current'=>$current] )
@endif

