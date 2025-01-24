<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

    <!-- begin:: Header Menu -->

    <!-- Uncomment this to display the close button of the panel
<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
-->
    <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
        <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
            <ul class="kt-menu__nav ">
                <li class="kt-menu__item kt-menu__item--open kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel kt-menu__item--active kt-menu__item--open-dropdown header_change_zindex" data-ktmenu-submenu-toggle="click" aria-haspopup="true">
                    <a href="javascript:;" id="Favourites" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-text">
                            <i class="la la-heart" style="color: #f44336;font-size: 15px; margin-right: 3px;"></i> <span>Favourites</span>
                        </span>
                    </a>
                    <!--<div id="fav_menu" class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true">
                                <a href="{{action('Purchase\ProductTreeController@index')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Chart of Product</span>
                                </a>
                            </li>
                        </ul>
                    </div>-->
                </li>
                <li class="kt-menu__item kt-menu__item--open kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel kt-menu__item--active kt-menu__item--open-dropdown header_change_zindex" data-ktmenu-submenu-toggle="click" aria-haspopup="true">
                    <a href="javascript:;" id="SmartProduct" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-text">
                            <i class="la la-heart" style="color: #f44336;font-size: 15px; margin-right: 3px;"></i> <span>{{ __('message.smart_product') }}</span>
                        </span>
                    </a>
                    <div id="smart_product_menu" class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                        <ul class="kt-menu__subnav">
                            <!--<li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ProductSmartController@viewAlternateBarcode')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Alternate Barcode</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ProductSmartController@viewProductItemTax')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Product Item Tax</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ProductSmartController@viewProductShelfStock')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Product Shelf Stock</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{url('/listing/smart-product/product-discount-setup')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Product Discount Setup</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ProductSmartController@viewSupplierWiseProductDetail')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Vendor Wise Products</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ProductSmartController@viewProductTPAnalysis')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">TP Analysis</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="/change-rate/form" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Update Product Price</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ReOrderStockController@create')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Re-Order Stock Analysis</span>
                                </a>
                            </li>
                            <li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Sales\PaymentModeController@index')}}" class="kt-menu__link ">
                                    <span class="kt-menu__link-text">Payment Mode Update</span>
                                </a>
                            </li>-->
                            @permission('294-view')
                            <!--<li class="kt-menu__item" aria-haspopup="true">
                                <a href="{{action('Purchase\ProductMergedController@create')}}" class="kt-menu__link ">
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
        @endphp
        <div class="language-selector">
            <form action="{{ route('change.language') }}" method="POST">
                @csrf
                <select name="language" id="language" class="form-control" onchange="this.form.submit()">
                    @foreach($languages as $language)
                        <option value="{{ $language->code }}"
                            {{ app()->getLocale() === $language->code ? 'selected' : '' }}>
                            {{ $language->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!--begin: Switcher -->
        <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown" id="kt_quick_search_toggle">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                <span class="kt-header__topbar-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                            <path d="M3.5,9 L20.5,9 C21.0522847,9 21.5,9.44771525 21.5,10 C21.5,10.132026 21.4738562,10.2627452 21.4230769,10.3846154 L17.7692308,19.1538462 C17.3034221,20.271787 16.2111026,21 15,21 L9,21 C7.78889745,21 6.6965779,20.271787 6.23076923,19.1538462 L2.57692308,10.3846154 C2.36450587,9.87481408 2.60558331,9.28934029 3.11538462,9.07692308 C3.23725479,9.02614384 3.36797398,9 3.5,9 Z M12,17 C13.1045695,17 14,16.1045695 14,15 C14,13.8954305 13.1045695,13 12,13 C10.8954305,13 10,13.8954305 10,15 C10,16.1045695 10.8954305,17 12,17 Z" fill="#000000"></path>
                        </g>
                    </svg>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
                <div class="kt-quick-search kt-quick-search--dropdown kt-quick-search--result-compact " id="kt_switch_branch_dropdown">
                    @php
                        $branches = App\Library\Utilities::getAllBranches();
                        $currentBranch = $branchid = auth()->user()->branch_id;
                    @endphp
                    @foreach($branches as $branch)
                        <a class="input-group branch-item @if($currentBranch == $branch->branch_id) active @endif " data-id="{{ $branch->branch_id }}" href="#" style="padding:10px;">
                            @if($currentBranch == $branch->branch_id)
                                <i class="fa fa-check icon-sm text-success" style="align-self: center;margin-right: 5px;color: #fff !important;font-size:12px;"></i>
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
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
                <div class="kt-quick-search kt-quick-search--dropdown kt-quick-search--result-compact" id="kt_quick_search_dropdown">
                    <form method="get" class="kt-quick-search__form">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="flaticon2-search-1"></i></span></div>
                            <input type="text" class="form-control kt-quick-search__input" placeholder="Search...">
                            <div class="input-group-append"><span class="input-group-text"><i class="la la-close kt-quick-search__close"></i></span></div>
                        </div>
                    </form>
                    <div class="kt-quick-search__wrapper kt-scroll" data-scroll="true" data-height="325" data-mobile-height="200">
                    </div>
                </div>
            </div>
        </div>
        <!--end: Search -->



        <!--begin: User Bar -->
        <div class="kt-header__topbar-item kt-header__topbar-item--user">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                <div class="kt-header__topbar-user user_header_change_zindex">
                    <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
                    <span class="kt-header__topbar-username kt-hidden-mobile">{{ Auth::user()->name }}</span>
                    <img class="kt-hidden" alt="Pic" src="/assets/media/users/300_25.jpg" />

                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

                <!--begin: Head -->
                <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(/assets/media/misc/bg-1.jpg)">
                    <div class="kt-user-card__avatar">
                        <img class="kt-hidden" alt="Pic" src="/assets/media/users/300_25.jpg" />

                        <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                        <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">{{ substr(Auth::user()->name, 0, 1) }}</span>
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
                        <a href="{{ route('logout') }}" target="_blank" class="btn btn-label btn-label-brand btn-sm btn-bold" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Sign Out</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
