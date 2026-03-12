@if((isset($staging_data) && $staging_data['has_staging']) || (isset($staging_activity_only) && isset($staging_activity) && $staging_activity->count() > 0))
    @php
        if (!isset($data)) {
            $data = [];
        }
        if (isset($staging_data) && $staging_data['has_staging']) {
            $data['stg'] = $staging_data;
            $data['flow_dtls'] = $staging_flow_dtls ?? null;
            $data['lastStag'] = $staging_last_flow ?? null;
            $data['firstStag'] = $staging_first_flow ?? null;
        }
        $data['menu_dtl_id'] = $staging_menu_dtl_id ?? $data['menu_dtl_id'] ?? null;
        $data['perPrefix'] = $data['menu_dtl_id'] ?? null;

        if (isset($staging_activity)) {
            if (isset($data['stg'])) {
                $data['stg']['activity'] = $staging_activity;
            }
            $current_stg_activities = $staging_activity;
        }

        $id = $staging_form_id ?? $data['id'] ?? null;
    @endphp

    @include('staging_activity.bottom_part')

    <script>
    (function() {
        $(document).on('click', '.stg-action-disabled', function(e) {
            e.preventDefault();
            var msg = $(this).data('stg-not-allowed') || 'Not allowed';
            if (window.toastr && typeof window.toastr.error === 'function') {
                window.toastr.error(msg);
            } else {
                alert(msg);
            }
            return false;
        });
    })();
    </script>
@endif
