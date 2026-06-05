
@if ($case == 'new')

    <div class="col-lg-4">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Select Branch:</label>
            <div class="col-lg-6">
                <select class="form-control erp-form-control-sm moveIndex kt-select2" id="new_branch_id" name="new_branch_id" required>
                    
                    @foreach($user_branches as $branch)
                        <option value="{{$branch->branch_id}}" {{auth()->user()->branch_id == $branch->branch_id ? 'selected' : ''}}>
                            {{$branch->branch_name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @else
    <div class="col-lg-4">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Select Branch:</label>
            <div class="col-lg-6">
                <select class="form-control erp-form-control-sm moveIndex kt-select2" id="new_branch_id" name="" disabled>
                    
                    @foreach($user_branches as $branch)
                        <option value="{{$branch->branch_id}}" {{$data['current']->branch_id == $branch->branch_id ? 'selected' : ''}}>
                            {{$branch->branch_name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <input type="hidden" id="" name="new_branch_id" value="{{ $data['current']->branch_id }}">
@endif
@section('customJS')
@parent {{-- This preserves any other scripts on the page --}}
<script>
    $(document).ready(function() {
        $('#new_branch_id').on('change', function() {
            var voucherType = "{{ $type }}";
            var branchId = $(this).val();

            // Only run the AJAX if both fields have a selected value
            if (voucherType && branchId) {
                $.ajax({
                    url: "{{ route('voucher.get-code') }}",
                    type: "GET",
                    data: {
                        voucher_type: voucherType,
                        branch_id: branchId
                    },
                    success: function(response) {
                        // Replace the value of the input field with id="target"
                        console.log("Voucher code fetched successfully:", response.code);
                        $('#voucher_no_div').text(response.code);
                    },
                    error: function(xhr) {
                        console.error("Something went wrong fetching the voucher code:", xhr.responseText);
                    }
                });
            } else {
                // Clear the target field if one of the dropdowns is reset to empty
                $('#voucher_no_div').text('');
            }
        });
    });
</script>
@endsection