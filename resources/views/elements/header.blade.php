<style>
    .lang-switcher {
        display: flex;
        align-items: center;
        margin-right: 10px;
    }

    .lang-switcher select {
        border: 1px solid #e0e0e0;
        background: #fff;
        padding: 6px 24px 6px 10px;
        font-size: 12px;
        font-weight: 500;
        color: #555;
        border-radius: 4px;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%23666' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        transition: all 0.2s ease;
    }

    .lang-switcher select:hover {
        border-color: #5d78ff;
    }

    .lang-switcher select:focus {
        outline: none;
    }

    /* RTL Toggle Switch Styles */
    .rtl-switcher {
        display: flex;
        align-items: center;
        margin-right: 12px;
    }

    .rtl-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        gap: 6px;
    }

    .rtl-label {
        font-size: 11px;
        font-weight: 600;
        color: #666;
        letter-spacing: 0.5px;
    }

    .rtl-toggle input {
        display: none;
    }

    .rtl-slider {
        position: relative;
        width: 36px;
        height: 18px;
        background: #ccc;
        border-radius: 18px;
        transition: all 0.3s ease;
    }

    .rtl-slider:before {
        content: '';
        position: absolute;
        width: 14px;
        height: 14px;
        background: #fff;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .rtl-toggle input:checked+.rtl-slider {
        background: #5d78ff;
    }

    .rtl-toggle input:checked+.rtl-slider:before {
        transform: translateX(18px);
        border-color: #5d78ff;
        box-shadow: 0 0 0 2px rgba(93, 120, 255, 0.15);
    }
    .center-center{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

</style>

<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

    <!-- begin:: Header Menu -->

    <!-- Uncomment this to display the close button of the panel
<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
-->
    <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
        <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
            <ul class="kt-menu__nav ">
                <li class="kt-menu__item kt-menu__item--open kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel kt-menu__item--active kt-menu__item--open-dropdown header_change_zindex"
                    data-ktmenu-submenu-toggle="click" aria-haspopup="true">
                    <a href="javascript:;" id="Favourites" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-text">
                            <i class="la la-heart" style="color: #f44336;font-size: 15px; margin-right: 3px;"></i>
                            <span>{{ __('message.smart_product') }}</span>
                        </span>
                    </a>
                    <!--<div id="fav_menu" class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true">
                                <a href="{{ action('Purchase\ProductTreeController@index') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Chart of Product</span>
                                </a>
                            </li>
                        </ul>
                    </div>-->
                </li>
                <li class="kt-menu__item kt-menu__item--open kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel kt-menu__item--active kt-menu__item--open-dropdown header_change_zindex"
                    data-ktmenu-submenu-toggle="click" aria-haspopup="true">
                    <a href="javascript:;" id="SmartProduct" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-text">
                            <i class="la la-heart" style="color: #f44336;font-size: 15px; margin-right: 3px;"></i>
                            <span>{{ __('message.smart_product') }}</span>
                        </span>
                    </a>
                    <div id="smart_product_menu"
                        class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                        <ul class="kt-menu__subnav">
                            <!--<li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Purchase\ProductSmartController@viewAlternateBarcode') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Alternate Barcode</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Purchase\ProductSmartController@viewProductItemTax') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Product Item Tax</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Purchase\ProductSmartController@viewProductShelfStock') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Product Shelf Stock</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ url('/listing/smart-product/product-discount-setup') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Product Discount Setup</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Purchase\ProductSmartController@viewSupplierWiseProductDetail') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Vendor Wise Products</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Purchase\ProductSmartController@viewProductTPAnalysis') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">TP Analysis</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="/change-rate/form" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Update Product Price</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Purchase\ReOrderStockController@create') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Re-Order Stock Analysis</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{ action('Sales\PaymentModeController@index') }}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Payment Mode Update</span>
                                </a>
                            </li>-->
                            @permission('294-view')
                                <!--<li class="kt-menu__item" aria-haspopup="true">
                                    <a href="{{ action('Purchase\ProductMergedController@create') }}" class="kt-menu__link ">
                                        <span class="kt-menu__link-text">Product Merged</span>
                                    </a>
                                </li>-->
                            @endpermission
                            <li class="kt-menu__item search_product_dtl" aria-haspopup="true">
                                <a href="javascript:;" class="kt-menu__link " data-id="">
                                    <span class="kt-menu__link-text">Product Price Inquiry</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- end:: Header Menu -->

    <!-- begin:: Header Topbar -->
    <div class="kt-header__topbar">

        @php
            $languages = \App\Models\Languages::all();
            $locale = app()->getLocale();
            // dd($locale);
            // $selectedlanguage = \App\Models\Languages::where('code',$locale)->value('id');
        @endphp
        <div class="lang-switcher">
            <form action="{{ route('change.language') }}" method="POST">
                @csrf
                <select name="language" onchange="this.form.submit()">
                    @foreach ($languages as $language)
                        <option value="{{ $language->code }}" {{ $locale === $language->code ? 'selected' : '' }}>
                            {{ $language->code === 'ar' ? 'عربي' : $language->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- RTL Toggle Switch -->
        <div class="rtl-switcher">
            <label class="rtl-toggle" title="{{ __('message.toggle_rtl') }}">
                <span class="rtl-label">RTL</span>
                <input type="checkbox" id="rtlToggle" onchange="toggleRTL(this.checked)">
                <span class="rtl-slider"></span>
            </label>
        </div>

        <div class="kt-header__topbar-item dropdown">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px"
                aria-expanded="false">
                <span class="kt-header__topbar-icon kt-pulse kt-pulse--brand">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"/>
                            <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"/>
                        </g>
                    </svg>
                    <span class="kt-pulse__ring"></span>
                </span>
                {{-- <span class="kt-badge kt-badge--dot kt-badge--notify kt-badge--sm kt-badge--brand">
                    {{ Auth::user()->unreadNotifications->count() }}
                </span> --}}
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg"
                style="">
                <div class="d-flex align-center justify-space-between p-2 kt-head kt-head--skin-dark kt-head--fit-x kt-head--fit-b"
                style="justify-content: space-between;align-items: center;padding-bottom: 8px !important;border-bottom: 1px solid #e6e6e6;">
                    <h3 class="kt-head__title" style="color:#000 !important;">
                        {{ __('Notifications') }}
                    </h3>
                    <div>
                        <button type="button" class="btn btn-success btn-sm btn-font-sm">Mark All Read</button>
                        <button type="button" class="btn btn-success btn-sm btn-font-sm" onclick="subscribeUserToPush()">View All</button>
                    </div>
                </div>
                <div class="tab-pane active show" id="topbar_notifications_notifications" role="tabpanel">
                        <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll ps ps--active-y"
                            data-scroll="true" data-height="300" data-mobile-height="200"
                            style="height: 300px; overflow: hidden;">
                            @forelse (Auth::user()->notifications as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="kt-notification__item">
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
                                        <div class="kt-notification__item-time">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>   
                            @empty
                                <div class="center-center text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48px" height="48px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M13,5.06189375 C12.6724058,5.02104333 12.3386603,5 12,5 C11.6613397,5 11.3275942,5.02104333 11,5.06189375 L11,4 L10,4 C9.44771525,4 9,3.55228475 9,3 C9,2.44771525 9.44771525,2 10,2 L14,2 C14.5522847,2 15,2.44771525 15,3 C15,3.55228475 14.5522847,4 14,4 L13,4 L13,5.06189375 Z" fill="#000000"/>
                                            <path d="M16.7099142,6.53272645 L17.5355339,5.70710678 C17.9260582,5.31658249 18.5592232,5.31658249 18.9497475,5.70710678 C19.3402718,6.09763107 19.3402718,6.73079605 18.9497475,7.12132034 L18.1671361,7.90393167 C17.7407802,7.38854954 17.251061,6.92750259 16.7099142,6.53272645 Z" fill="#000000"/>
                                            <path d="M11.9630156,7.5 L12.0369844,7.5 C12.2982526,7.5 12.5154733,7.70115317 12.5355117,7.96165175 L12.9585886,13.4616518 C12.9797677,13.7369807 12.7737386,13.9773481 12.4984096,13.9985272 C12.4856504,13.9995087 12.4728582,14 12.4600614,14 L11.5399386,14 C11.2637963,14 11.0399386,13.7761424 11.0399386,13.5 C11.0399386,13.4872031 11.0404299,13.4744109 11.0414114,13.4616518 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                    <p>
                                        No notifications
                                    </p>
                                </div>
                                
                            @endforelse
                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                            </div>
                            <div class="ps__rail-y" style="top: 0px; right: 0px; height: 300px;">
                                <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 107px;"></div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <!--begin: Switcher -->
        <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown" id="kt_quick_search_toggle">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                <span class="kt-header__topbar-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                        height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z"
                                fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                            <path
                                d="M3.5,9 L20.5,9 C21.0522847,9 21.5,9.44771525 21.5,10 C21.5,10.132026 21.4738562,10.2627452 21.4230769,10.3846154 L17.7692308,19.1538462 C17.3034221,20.271787 16.2111026,21 15,21 L9,21 C7.78889745,21 6.6965779,20.271787 6.23076923,19.1538462 L2.57692308,10.3846154 C2.36450587,9.87481408 2.60558331,9.28934029 3.11538462,9.07692308 C3.23725479,9.02614384 3.36797398,9 3.5,9 Z M12,17 C13.1045695,17 14,16.1045695 14,15 C14,13.8954305 13.1045695,13 12,13 C10.8954305,13 10,13.8954305 10,15 C10,16.1045695 10.8954305,17 12,17 Z"
                                fill="#000000"></path>
                        </g>
                    </svg>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
                <div class="kt-quick-search kt-quick-search--dropdown kt-quick-search--result-compact "
                    id="kt_switch_branch_dropdown">
                    @php
                        $branches = App\Library\Utilities::getAllBranches();
                        $currentBranch = $branchid = auth()->user()->branch_id;
                    @endphp
                    @foreach ($branches as $branch)
                        <a class="input-group branch-item @if ($currentBranch == $branch->branch_id) active @endif "
                            data-id="{{ $branch->branch_id }}" href="#" style="padding:10px;">
                            @if ($currentBranch == $branch->branch_id)
                                <i class="fa fa-check icon-sm text-success"
                                    style="align-self: center;margin-right: 5px;color: #fff !important;font-size:12px;"></i>
                            @endif
                            {{ $branch->branch_name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <!--end: Switcher -->

        <!--begin: Search -->
        <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown" id="kt_quick_search_toggle">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                <span class="kt-header__topbar-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                        height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path
                                d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path
                                d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
                <div class="kt-quick-search kt-quick-search--dropdown kt-quick-search--result-compact"
                    id="kt_quick_search_dropdown">
                    <form method="get" class="kt-quick-search__form">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i
                                        class="flaticon2-search-1"></i></span></div>
                            <input type="text" class="form-control kt-quick-search__input"
                                placeholder="Search...">
                            <div class="input-group-append"><span class="input-group-text"><i
                                        class="la la-close kt-quick-search__close"></i></span></div>
                        </div>
                    </form>
                    <div class="kt-quick-search__wrapper kt-scroll" data-scroll="true" data-height="325"
                        data-mobile-height="200">
                    </div>
                </div>
            </div>
        </div>
        <!--end: Search -->



        <!--begin: User Bar -->
        <div class="kt-header__topbar-item kt-header__topbar-item--user">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                <div class="kt-header__topbar-user user_header_change_zindex">
                    <span class="kt-header__topbar-welcome kt-hidden-mobile">{{ __('message.hi') }},</span>
                    <span class="kt-header__topbar-username kt-hidden-mobile">{{ Auth::user()->name }}</span>
                    <img class="kt-hidden" alt="Pic" src="/assets/media/users/300_25.jpg" />

                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    <span
                        class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
            <div
                class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

                <!--begin: Head -->
                <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
                    style="background-image: url(/assets/media/misc/bg-1.jpg)">
                    <div class="kt-user-card__avatar">
                        <img class="kt-hidden" alt="Pic" src="/assets/media/users/300_25.jpg" />

                        <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                        <span
                            class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="kt-user-card__name">
                        {{ Auth::user()->name }}<br>
                        {{ Auth::user()->email }}
                    </div>
                </div>
                <div class="kt-notification">
                    <a href="{{ action('Setting\PasswordController@create') }}" class="kt-notification__item">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-calendar-3 kt-font-success"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title kt-font-bold">
                                Change Password
                            </div>
                            <div class="kt-notification__item-time">
                            </div>
                        </div>
                    </a>
                    <a href="{{ action('Setting\PasswordController@createPos') }}" class="kt-notification__item">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-calendar-3 kt-font-success"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title kt-font-bold">
                                Change POS Password
                            </div>
                            <div class="kt-notification__item-time">
                            </div>
                        </div>
                    </a>
                    {{-- <a href="{{ action('HomeController@branchChange') }}" class="kt-notification__item">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-calendar-3 kt-font-success"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title kt-font-bold">
                                Switch Branch
                            </div>
                            <div class="kt-notification__item-time">
                            </div>
                        </div>
                    </a> --}}
                    <div class="kt-notification__custom kt-space-between">
                        <a href="{{ route('logout') }}" target="_blank"
                            class="btn btn-label btn-label-brand btn-sm btn-bold"
                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Sign
                            Out</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
                <!--end: Head -->
            </div>
        </div>

        <!--end: User Bar -->
    </div>

    <!-- end:: Header Topbar -->
</div>
<script>
async function subscribeUserToPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.log('Push not supported');
        return;
    }

    const registration = await navigator.serviceWorker.register('/service-worker.js');

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        console.log('Permission denied');
        return;
    }

    console.log('After:', permission);

    if (permission !== 'granted') {
        alert('Notifications are blocked. Please enable them in browser settings.');
        return;
    }

    alert('Notifications enabled');

    const vapidPublicKey = "{{ Config('constants.vapid_public_key') }}";

    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
    });

    await fetch('/push/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(subscription)
    });

    console.log('Subscribed:', subscription);
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
}
</script>