<div class="row">
    <div class="col-lg-12 m-4">
        <h4>Select Groups To Add</h4>
        <hr/>
        @if(isset($groups) && count($groups) > 0)
            <form action="" method="POST">
                <input type="hidden" value="{{$phoneNo}}">
                <div class="kt-checkbox-list">
                    @foreach($groups as $group)
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand my-4">
                            <input type="checkbox" value="{{ $group->grp_id }}" name="groups[]"> {{ $group->grp_name }}
                            <span></span>
                        </label>
                    @endforeach
                </div>
                <hr/>
                <div>
                    <button class="btn btn-success" type="button">Add In Groups</button>
                </div>
            </form>
        @else
            <div>
                <em>No Groups Found</em>
            </div>
        @endif
    </div>
</div>