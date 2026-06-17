@extends('layouts.layout')
@section('title', 'Notifications')

@section('pageCSS')
    <style>
        .notifications-filter .nav-link {
            color: #5d5d5d;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }
        .notifications-filter .nav-link.active {
            color: #0abb87;
            border-bottom: 2px solid #0abb87;
        }
        .kt-notification__item--unread {
            background-color: #f6faff;
        }
        .kt-notification__item--unread .kt-notification__item-title {
            font-weight: 600;
        }
        .kt-notification__item-desc {
            font-size: 0.9rem;
            color: #6c7293;
            margin-top: 2px;
            line-height: 1.4;
        }
        .notifications-empty {
            text-align: center;
            padding: 40px 20px;
            color: #6c7293;
        }
    </style>
@endsection

@section('content')
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('Notifications') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm btn-font-sm">Mark All Read</button>
                    </form>
                </div>
            </div>
            <div class="kt-portlet__body">
                <ul class="nav notifications-filter border-bottom mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('notifications.index', ['filter' => 'all']) }}">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('notifications.index', ['filter' => 'unread']) }}">Unread</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === 'read' ? 'active' : '' }}" href="{{ route('notifications.index', ['filter' => 'read']) }}">Read</a>
                    </li>
                </ul>

                <div class="kt-notification">
                    @forelse ($notifications as $notification)
                        @include('notifications._item', ['notification' => $notification, 'showMessage' => true])
                    @empty
                        <div class="notifications-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48px" height="48px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z" fill="#000000" opacity="0.3"/>
                                    <path d="M13,5.06189375 C12.6724058,5.02104333 12.3386603,5 12,5 C11.6613397,5 11.3275942,5.02104333 11,5.06189375 L11,4 L10,4 C9.44771525,4 9,3.55228475 9,3 C9,2.44771525 9.44771525,2 10,2 L14,2 C14.5522847,2 15,2.44771525 15,3 C15,3.55228475 14.5522847,4 14,4 L13,4 L13,5.06189375 Z" fill="#000000"/>
                                </g>
                            </svg>
                            <p class="mt-3 mb-0">No notifications</p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="mt-4">
                        {{ $notifications->appends(['filter' => $filter])->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
