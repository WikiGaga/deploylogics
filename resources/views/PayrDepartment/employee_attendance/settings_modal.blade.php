<div class="modal-header">
    <h5 class="modal-title" id="attendanceSettingsTitle">Attendance Settings</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form id="attendance_settings_form">
    @csrf
    <div class="modal-body">
        <div class="form-group">
            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                <input type="checkbox" name="offline_attendance" value="1" {{ isset($settings) && (int) $settings->offline_attendance === 1 ? 'checked' : '' }}>
                Enable offline attendance
                <span></span>
            </label>
        </div>
        <div class="form-group">
            <label for="attendance_time_zone">Time zone</label>
            <select class="form-control" id="attendance_time_zone" name="time_zone" required>
                @php $selectedTimezone = $settings->time_zone ?? config('app.timezone', 'UTC'); @endphp
                @foreach($timezones as $timezone)
                    <option value="{{ $timezone }}" {{ $selectedTimezone === $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </div>
</form>
