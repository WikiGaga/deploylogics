

<div class="kt-portlet__head" style="min-height: 50px;">
    <div class="kt-chat__head ">
        @if($userInfo->cnt_is_group == 1)
            <div class="kt-chat__left text-left">
                <!--end:: Aside Mobile Toggle-->
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flaticon2-gear"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-left dropdown-menu-md">
                        <!--begin::Nav-->
                        <ul class="kt-nav">
                            <li class="kt-nav__item">
                                <a href="javascript:void(0);" target="blank" class="kt-nav__link show_group__contacts" data-id="{{ $userInfo->phone_no }}">
                                    <i class="kt-nav__link-icon flaticon-users"></i>
                                    <span class="kt-nav__link-text">Contacts In Group</span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Nav-->
                    </div>
                </div>
            </div>
        @endif
        <div class="kt-chat__center">
            <div class="kt-chat__label text-right">
                <a href="javascript:void(0);" class="kt-chat__title">{{ $userInfo->cnt_name ?? 'Unknown Name' }}</a>
            </div>
        </div>
    </div>
</div>
<div class="kt-portlet__body">
    <div class="kt-scroll kt-scroll--pull ps ps--active-y" id="chatMessagesContainer" data-mobile-height="300" style="height: 415px; overflow: hidden;">
        <div class="kt-chat__messages">
            @if(isset($userChat) && count($userChat) > 0)
                @foreach($userChat as $message)
                    @if($message->message_type == 'template')
                        <div class="kt-chat__message kt-chat__message--success text-center">
                            <div class="kt-chat__text kt-bg-light-dark px-3 py-2" style="font-size: 12px;">
                                {{ $message->message }}
                            </div>
                        </div>
                    @else
                        <div class="kt-chat__message @if($message->is_sent == 1) kt-chat__message--right @else kt-chat__message--left @endif">
                            <div class="kt-chat__user d-flex align-items-center @if($message->is_sent == 1) justify-content-end @endif">
                                @if($message->is_sent == 1)
                                    <a href="javascript:void(0);" class="kt-chat__username">You</a>
                                @else
                                    <a href="javascript:void(0);" class="kt-chat__username">{{ $message->contact->cnt_name }}</a>
                                @endif
                            </div>
                            <div class="kt-chat__text @if($message->is_sent == 1) kt-bg-light-brand @else kt-bg-light-success @endif ">
                                {{ $message->message }}
                            </div>
                            <span class="kt-chat__datetime py-2 d-block">{{ \App\Http\Controllers\Controller::timeAgo(strtotime($message->receive_at)) }}</span>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 415px; right: -2px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 164px;"></div></div></div>
</div>
<div class="kt-portlet__foot">
    <div class="chat__input_area">
        <div class="kt-chat__editor">
            <textarea style="height: 50px;border: 1px solid #ecedf3 !important;" placeholder="Type here..."></textarea>
        </div>
        <div class="kt-chat__toolbar">
            <div class="kt_chat__tools">
                <a href="#" data-toggle="modal" data-target="#attachmentPopup">
                    <i class="flaticon2-photograph"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#attachmentPopup">
                    <i class="flaticon2-document"></i>
                </a>
                <a href="#" data-toggle="modal" data-target="#locationPopup">
                    <i class="flaticon2-map"></i>
                </a>
                <div class="btn-group dropup d-none">
                    <a type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flaticon2-map mr-0"></i>
                    </a>
                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -165px, 0px); top: 0px; left: 0px; will-change: transform;">
                        @if(isset($branches) && count($branches) > 0)
                            @foreach($branches as $branch)
                                <a class="dropdown-item send_selected__location" data-phone="{{ $userInfo->phone_no }}" data-sentto="@if($userInfo->cnt_is_group == 1) group @else single @endif" href="javascript:void(0);" data-lat="{{ $branch->branch_latitude }}" data-lng="{{ $branch->branch_longitude }}">Send {{ $branch->branch_short_name }} Location</a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="kt_chat__actions">
                <button type="button" class="btn btn-brand btn-md btn-upper btn-bold send_reply__message" data-sentto="@if($userInfo->cnt_is_group == 1) group @else single @endif" data-id="{{ auth()->user()->id }}" data-name="{{ auth()->user()->name }}" data-phone="{{ $userInfo->phone_no }}">Send</button>
            </div>
        </div>
    </div>
</div>
    
