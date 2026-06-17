<a href="{{ route('notifications.read', $notification->id) }}" class="kt-notification__item{{ is_null($notification->read_at) ? ' kt-notification__item--unread' : '' }}">
    <div class="kt-notification__item-icon">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
                <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1"/>
                <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1"/>
            </g>
        </svg>
    </div>
    <div class="kt-notification__item-details">
        <div class="kt-notification__item-title">
            {{ $notification->data['title'] ?? '-' }}
        </div>
        @if (!empty($showMessage) && !empty($notification->data['message']))
            <div class="kt-notification__item-desc">
                {{ $notification->data['message'] }}
            </div>
        @endif
        <div class="kt-notification__item-time">
            {{ $notification->created_at->diffForHumans() }}
        </div>
    </div>
</a>
