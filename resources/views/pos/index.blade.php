@php
    // use App\CentralLogics\Helpers;
    // use App\Models\BusinessSetting;
    // use App\Models\Order;
    $subcategories = [];
    $products = [];
    $keyword = '';
@endphp
@extends('layouts.app')

@section('title', __('messages.pos'))

@section('content')

    <style>
        .category-scroll-container {
            overflow-x: auto;
            white-space: nowrap;
            padding: 10px 0;
        }

        .category-scroll {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .category-item {
            display: inline-block;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }

        .category-item.selected {
            padding: 5px;
            border-radius: 10px;
            background-color: #F8923B;
            color: #fff;
            box-shadow: 0 4px 10px rgba(64, 169, 255, 0.5);
        }

        .category-item.selected:hover {
            color: #fff;
        }

        .category-icon img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 5px;
        }

        .category-name {
            font-size: 12px;
            text-overflow: ellipsis;
            overflow: hidden;
            word-wrap: break-word;
        }

        .category-item:not(.selected):hover {
            color: #F8923B;
        }

        .subcategory-item:not(.selected):hover {
            color: #F8923B;
        }

        .numeric-keypad-container {
            max-width: 200px;
            text-align: center;
        }

        .keypad-buttons .btn {
            width: 40px;
            height: 40px;
            margin: 5px;
            font-size: 18px;
        }

        .keypad-container h6 {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>

    <div id="pos-div" class="content container-fluid" style="background-color: white;">
        {{-- @php($restaurant_data = Helpers::get_restaurant_data()) --}}
        <div class="d-flex flex-wrap">
            <div class="order--pos-left">
                <!-- Subcategories (Vertical Scroll Attached to Card) -->

                {{-- @if ($subcategories->isNotEmpty()) --}}
                <style>
                    /* Subcategory Scroll Styles */
                    .main-content {
                        margin-left: 80px;
                    }

                    [dir="rtl"] .main-content {
                        margin-left: 0;
                        margin-right: 80px;
                    }

                    .subcategory-scroll-container {
                        position: fixed;
                        top: 5;
                        left: 0;
                        height: 88vh;
                        width: 80px;
                        border-radius: 5px;
                        background-color: #334257;
                        overflow-y: auto;
                        padding: 5px;
                        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
                        z-index: 1000;
                    }

                    [dir="rtl"] .subcategory-scroll-container {
                        left: auto;
                        right: 0;
                        text-align: right;
                    }

                    .subcategory-header {
                        font-size: 12px;
                        font-weight: bold;
                        text-align: center;
                        margin-bottom: 20px;
                        color: white;
                    }

                    .subcategory-list {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 10px;
                    }

                    .subcategory-item {
                        text-decoration: none;
                        display: block;
                        text-align: center;
                        color: white;
                    }

                    .subcategory-circle {
                        width: 70px;
                        height: 70px;
                        border-radius: 50%;
                        background-color: #edf3f9;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 11px;
                        font-weight: bold;
                        color: #6c757d;
                        transition: background-color 0.3s, transform 0.3s;
                    }

                    .subcategory-circle:hover {
                        background-color: #40c4ff;
                        color: black;
                        transform: scale(1.1);
                    }

                    .subcategory-item.selected {
                        padding: 5px;
                        border-radius: 10px;
                        color: white;
                        background-color: #F8923B;
                        transform: scale(1.1);
                    }

                    .subcategory-name {
                        text-align: center;
                        padding: 5px;
                        word-wrap: break-word;
                    }

                    .mobile-scroll {
                        display: none;
                    }

                    @media (max-width: 1025px) {
                        .subcategory-scroll-container {
                            width: 0px;
                            display: none;
                        }

                        .mobile-scroll {
                            display: block;
                        }

                        .main-content {
                            margin-left: 0px;
                        }

                        .main-content {
                            margin-right: 0px;
                        }

                        .subcategory-scroll-container {
                            width: 0px;
                        }

                        .subcategory-list {
                            display: flex;
                            flex-direction: row;
                            align-items: center;
                            gap: 10px;
                        }

                        .category-name {
                            color: black;
                        }
                    }
                </style>

                <div class="subcategory-scroll-container">
                    <h6 class="subcategory-header">
                        {{ __('Sub_Categories') }}
                    </h6>
                    <div class="subcategory-list">
                        {{-- @foreach ($subcategories as $subCategory)
                            <a href="{{ url()->current() }}?category_id={{ $subCategory->id }}"
                                class="subcategory-item {{ request()->get('category_id') == $subCategory->id ? 'selected' : '' }}">
                                <div class="category-icon">
                                    <img src="{{ $subCategory['image_full_url'] }}" alt="{{ $subCategory->name }}">
                                </div>
                                <div class="category-name">{{ $subCategory->name }}</div>
                            </a>
                        @endforeach --}}
                        @include('pos._subcategory_list', ['subcategories' => $subcategories])
                    </div>
                </div>
                {{-- @endif --}}

                <div class="card main-content">
                    <div class="card-header bg-light border-0">
                        <div class="col-sm-4">
                            <h5 class="card-title">
                                <span>
                                    {{ __('Food Section') }}
                                </span>
                            </h5>
                        </div>
                        <div class="col-sm-8">
                            <form id="search-form" class="header-item w-100 mw-100">
                                <!-- Search -->
                                <div class="input-group input-group-merge input-group-flush w-100">
                                    <div class="input-group-prepend pl-2">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    {{-- <input id="datatableSearch" type="search" value="{{ $keyword ?? '' }}" name="search"
                                        class="form-control flex-grow-1 pl-5 border rounded h--45x"
                                        placeholder="{{ __('messages.Ex : Search Food Name') }}"
                                        aria-label="{{ __('messages.search_here') }}"> --}}
                                    <input id="search-keyword" type="search" value="{{ $keyword ?? '' }}" name="keyword"
                                        class="form-control flex-grow-1 pl-5 border rounded h--45x"
                                        placeholder="{{ __('messages.Ex : Search Food Name') }}"
                                        aria-label="{{ __('messages.search_here') }}">
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center" id="items">
                        <div class="row g-2 mb-4">
                            {{-- <div class="col-sm-6">
                                <div class="input-group">
                                    <select name="category" id="category"
                                            class="form-control js-select2-custom set-filter"
                                            data-url="{{ url()->full() }}" data-filter="category_id"
                                            title="{{ __('messages.select_category') }}">
                                        <option value="">{{ __('messages.all_categories') }}</option>
                                        @foreach ($categories as $item)
                                            <option
                                                value="{{ $item->id }}" {{ $category == $item->id ? 'selected' : '' }}>
                                                {{ Str::limit($item->name, 20, '...') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-sm-12">
                                <div class="category-scroll-container">
                                    <div class="category-scroll">
                                        <a href="javascript:void(0);" class="category-item" data-category="">
                                            <div class="category-icon">
                                                <img src="{{ asset('assets/images/category/2024-11-20-673de06ce3aa7.png') }}"
                                                    alt="All Products">
                                            </div>
                                            <div class="category-name">
                                                {{ __('messages.all_menu') }}
                                            </div>
                                        </a>
                                        {{-- @foreach ($categories as $item) --}}
                                        <a href="javascript:void(0);" class="category-item " data-category="">
                                            <div class="category-icon">
                                                <img src="{{ asset('assets/images/category/2024-11-20-673de06ce3aa7.png') }}"
                                                    alt="{{ 'item' }}">
                                            </div>
                                            <div class="category-name">
                                                {{ Str::limit('Burger', 20, '...') }}
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="category-item " data-category="">
                                            <div class="category-icon">
                                                <img src="{{ asset('assets/images/category/2024-11-20-673de06ce3aa7.png') }}"
                                                    alt="{{ 'item' }}">
                                            </div>
                                            <div class="category-name">
                                                {{ Str::limit('Pizza', 20, '...') }}
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="category-item " data-category="">
                                            <div class="category-icon">
                                                <img src="{{ asset('assets/images/category/2024-11-20-673de06ce3aa7.png') }}"
                                                    alt="{{ 'item' }}">
                                            </div>
                                            <div class="category-name">
                                                {{ Str::limit('Coffee & Drinks', 20, '...') }}
                                            </div>
                                        </a>
                                        {{-- @endforeach --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mobile-scroll">
                                <div class="category-scroll-container">
                                    <div class="subcategory-list">
                                        @include('pos._subcategory_list', [
                                            'subcategories' => $subcategories,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="product-list">
                            @include('pos._product_list', ['products' => $products])
                        </div>
                    </div>

                    {{-- <div class="card-footer">
                        {!! $products->withQueryString()->links() !!}
                    </div> --}}
                </div>
            </div>
            <div class="order--pos-right">
                <div class="card">
                    <div class="card-header bg-light border-0 m-1">
                        <h5 class="card-title">
                            <span>
                                {{ __('Billing Section') }}
                            </span>
                        </h5>
                    </div>
                    <div class="w-100">
                        <div class="d-flex flex-wrap flex-row p-2 add--customer-btn">
                            <label for='customer'></label>
                            <select id='customer' name="customer_id"
                                data-placeholder="{{ __('messages.walk_in_customer') }}"
                                class="js-data-example-ajax form-control"></select>
                            <button class="btn btn--primary" data-toggle="modal"
                                data-target="#add-customer">{{ __('Add New Customer') }}</button>
                        </div>
                        {{-- @if (($restaurant_data->restaurant_model == 'commission' && $restaurant_data->self_delivery_system == 1) || ($restaurant_data->restaurant_model == 'subscription' && isset($restaurant_data->restaurant_sub) && $restaurant_data->restaurant_sub->self_delivery == 1)) --}}
                        <div class="pos--delivery-options">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">
                                    <span class="card-title-icon">
                                        <i class="tio-user"></i>
                                    </span>
                                    <span>{{ __('Delivery_Information') }}</span>
                                </h5>
                                <span class="delivery--edit-icon text-primary" id="delivery_address" data-toggle="modal"
                                    data-target="#paymentModal"><i class="tio-edit"></i></span>
                            </div>
                            <div class="pos--delivery-options-info d-flex flex-wrap" id="del-add">
                                @include('pos._address')
                            </div>
                        </div>
                        {{-- @endif --}}
                    </div>

                    <div class='w-100' id="cart">
                        @include('pos._cart')
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="quick-view" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" id="quick-view-modal">

                </div>
            </div>
        </div>
        {{-- @php($order = Order::find(session('last_order'))) --}}
        {{-- @if ($order)
            <div class="modal fade" id="print-invoice" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('messages.print_invoice') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pt-0 row ff-emoji">

                            <div class="col-12" id="printableArea">
                                @include('new_invoice')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endif --}}


        <!-- Static Delivery Address Modal -->
        <div class="modal fade" id="delivery-address">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light border-bottom py-3">
                        <h3 class="modal-title flex-grow-1 text-center">{{ __('Delivery Options') }}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="contact_person_name"
                                        class="input-label">{{ __('Contact person name') }}</label>
                                    <input id="contact_person_name" type="text" class="form-control"
                                        name="contact_person_name" value=""
                                        placeholder="{{ __('messages.Ex :') }} Jhone">
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_person_number"
                                        class="input-label">{{ __('Contact Number') }}</label>
                                    <input id="contact_person_number" type="text" class="form-control"
                                        name="contact_person_number" value=""
                                        placeholder="{{ __('messages.Ex :') }} +3264124565">
                                </div>
                                <div class="col-md-4">
                                    <label for="road" class="input-label">{{ __('Road') }}</label>
                                    <input id="road" type="text" class="form-control" name="road"
                                        value="" placeholder="{{ __('messages.Ex :') }} 4th">
                                </div>
                                <div class="col-md-4">
                                    <label for="house" class="input-label">{{ __('House') }}</label>
                                    <input id="house" type="text" class="form-control" name="house"
                                        value="" placeholder="{{ __('messages.Ex :') }} 45/C">
                                </div>
                                <div class="col-md-4">
                                    <label for="floor" class="input-label">{{ __('Floor') }}</label>
                                    <input id="floor" type="text" class="form-control" name="floor"
                                        value="" placeholder="{{ __('messages.Ex :') }} 1A">
                                </div>

                                <div class="col-md-12">
                                    <label for="address" class="input-label">{{ __('Address') }}</label>
                                    <textarea id="address" name="address" class="form-control" cols="30" rows="3"
                                        placeholder="{{ __('messages.Ex :') }} address"></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3 h-200px" id="map"></div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button class="btn btn-sm btn--primary w-100" type="submit">
                                    {{ __('Update Delivery address') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Static Delivery Address Modal -->

        <!-- Add Customer Modal -->
        <div class="modal fade" id="add-customer" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light py-3">
                        <h4 class="modal-title">{{ __('add_new_customer') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="product_form">
                            @csrf
                            <div class="row pl-2">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="f_name" class="input-label">{{ __('first_name') }} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input id="f_name" type="text" name="f_name" class="form-control"
                                            value="{{ old('f_name') }}" placeholder="{{ __('first_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="l_name" class="input-label">{{ __('last_name') }} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input id="l_name" type="text" name="l_name" class="form-control"
                                            value="{{ old('l_name') }}" placeholder="{{ __('last_name') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="email" class="input-label">{{ __('email') }}<span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input id="email" type="email" name="email" class="form-control"
                                            value="{{ old('email') }}" placeholder="{{ __('Ex_:_ex@example.com') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="phone" class="input-label">{{ __('phone') }}
                                            ({{ __('with_country_code') }})<span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input id="phone" type="tel" name="phone" class="form-control"
                                            value="{{ old('phone') }}" placeholder="{{ __('phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ __('reset') }}</button>
                                <button type="submit" id="submit_new_customer"
                                    class="btn btn--primary">{{ __('save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    {{-- <script
        src="https://maps.googleapis.com/maps/api/js?key={{ BusinessSetting::where('key', 'map_api_key')->first()?->value }}&libraries=places&callback=initMap&v=3.49">
    </script> --}}
    <script src="{{ public_path('assets/admin/js/view-pages/pos.js') }}"></script>
@endpush
