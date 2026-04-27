@if((isset($staging_data) && $staging_data['has_staging']) || (isset($staging_activity_only) && isset($staging_activity) && $staging_activity->count() > 0))
    @php
        // if (!isset($data)) {
        //     $data = [];
        // }
        if (isset($data) && is_object($data)) {
            $data = $data->toArray();
        } elseif (!isset($data)) {
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
        document.addEventListener('click', function(e) {
            var el = e.target && e.target.closest && e.target.closest('.stg-action-disabled');
            if (!el) return;
            e.preventDefault();
            var msg = el.getAttribute('data-stg-not-allowed') || 'Not allowed';
            if (window.toastr && typeof window.toastr.error === 'function') {
                window.toastr.error(msg);
            } else {
                alert(msg);
            }
        }, true);

        function formHasStagingActions(form) {
            return form && form.querySelector && form.querySelector('.staging-action-btn');
        }

        function markDirtyIfStagingForm(form) {
            if (formHasStagingActions(form)) {
                form.setAttribute('data-staging-dirty', '1');
            }
        }

        function markStagingFormDirty(ev) {
            var t = ev.target;
            if (!t || !t.closest) return;
            if (ev.type === 'keyup' && t.tagName && !/^(INPUT|TEXTAREA|SELECT)$/i.test(t.tagName)) return;
            var form = t.closest('form');
            markDirtyIfStagingForm(form);
        }

        document.addEventListener('change', markStagingFormDirty, true);
        document.addEventListener('change', markStagingFormDirty, false);
        document.addEventListener('input', markStagingFormDirty, true);
        document.addEventListener('keyup', markStagingFormDirty, true);

        window.addEventListener('load', function() {
            if (!window.jQuery) return;
            var $ = window.jQuery;
            function formFromSelect(el) {
                if (!el) return null;
                return el.form || (el.closest && el.closest('form'));
            }
            $(document).on('change', 'select', function() {
                markDirtyIfStagingForm(formFromSelect(this));
            });
            $(document).on('select2:select select2:clear select2:unselect', 'select', function() {
                markDirtyIfStagingForm(formFromSelect(this));
            });
        });

        document.addEventListener('mousedown', function(ev) {
            var btn = ev.target && ev.target.closest && ev.target.closest('.staging-action-btn');
            if (!btn || !btn.form) return;
            var code = (btn.getAttribute('data-staging-action-code') || '').toLowerCase();
            btn.form.setAttribute('data-staging-last-action-code', code);
            var actionId = btn.value || btn.getAttribute('data-staging-action-id') || '';
            btn.form.setAttribute('data-staging-last-action-id', actionId);
            var hidActionId = btn.form.querySelector('#staging_current_actions_id');
            if (hidActionId) {
                hidActionId.value = actionId;
            }
            var hidCode = btn.form.querySelector('#staging_action_code');
            if (hidCode) {
                hidCode.value = btn.getAttribute('data-staging-action-code') || '';
            }
        }, true);
    })();
    </script>
@endif
