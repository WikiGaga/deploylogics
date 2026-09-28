@extends('layouts.layout')
@section('title', 'Food')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : '';
        if ($case == 'new') {
            $code = $data['document_code'];
        }
        if ($case == 'edit') {
            $id = $data['current']->id;
            $code = $data['current']->id;
            $name = $data['current']->name;
            $description = $data['current']->description;
            $price = $data['current']->price;
            $discount = $data['current']->discount;
            $discount_type = $data['current']->discount_type ?: 'percent';
            $category_id = $data['current']->category_id;
            $veg = $data['current']->veg;
            $status = $data['current']->status;
            $visibility = $data['current']->visibility ?: 'on';
            $is_halal = $data['current']->is_halal;
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
        <form id="food_form" class="kt-form" method="post"
            action="{{ action('Inventory\FoodController@store', isset($id) ? $id : '') }}">
            @csrf
            <input type="hidden" value="{{ $form_type }}" id="form_type">
            <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                        @include('elements.page_header', ['page_data' => $data['page_data']])
                    </div>
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-4">
                                <div class="erp-page--title">{{ $code ?? '' }}</div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{ $name ?? '' }}"
                                    class="form-control erp-form-control-sm medium_text" maxlength="191">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Description:</label>
                            <div class="col-lg-6">
                                <textarea name="description" rows="3" class="form-control erp-form-control-sm">{{ $description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Category:</label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select name="category_id" class="form-control erp-form-control-sm kt-select2">
                                        <option value="">Select</option>
                                        @foreach ($data['categories'] as $category)
                                            <option value="{{ $category->id }}"
                                                {{ isset($category_id) && (string) $category_id === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Price: <span class="required">*</span></label>
                            <div class="col-lg-3">
                                <input type="number" step="0.001" min="0.01" name="price"
                                    value="{{ $price ?? '' }}" class="form-control erp-form-control-sm">
                            </div>
                            <label class="col-lg-2 erp-col-form-label text-right">Discount:</label>
                            <div class="col-lg-2">
                                <input type="number" step="0.001" min="0" name="discount"
                                    value="{{ $discount ?? 0 }}" class="form-control erp-form-control-sm">
                            </div>
                            <div class="col-lg-2">
                                @php $selectedDiscountType = $discount_type ?? 'percent'; @endphp
                                <select name="discount_type" class="form-control erp-form-control-sm">
                                    <option value="percent" {{ $selectedDiscountType === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="amount" {{ $selectedDiscountType === 'amount' ? 'selected' : '' }}>Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Food Type:</label>
                            <div class="col-lg-3">
                                @php $selectedVeg = isset($veg) ? (string) $veg : '0'; @endphp
                                <select name="veg" class="form-control erp-form-control-sm">
                                    <option value="0" {{ $selectedVeg === '0' ? 'selected' : '' }}>Non Veg</option>
                                    <option value="1" {{ $selectedVeg === '1' ? 'selected' : '' }}>Veg</option>
                                </select>
                            </div>
                            <label class="col-lg-2 erp-col-form-label text-right">Visibility:</label>
                            <div class="col-lg-2">
                                @php $selectedVisibility = $visibility ?? 'on'; @endphp
                                <select name="visibility" class="form-control erp-form-control-sm">
                                    <option value="on" {{ $selectedVisibility === 'on' ? 'selected' : '' }}>On</option>
                                    <option value="off" {{ $selectedVisibility === 'off' ? 'selected' : '' }}>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Active:</label>
                            <div class="col-lg-3">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @php $entryStatus = isset($status) ? (string) $status : '1'; @endphp
                                        <input type="checkbox" name="status" value="1"
                                            {{ $entryStatus === '1' ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                            <label class="col-lg-2 erp-col-form-label text-right">Halal:</label>
                            <div class="col-lg-3">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @php $halalStatus = isset($is_halal) ? (string) $is_halal : '1'; @endphp
                                        <input type="checkbox" name="is_halal" value="1"
                                            {{ $halalStatus === '1' ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endpermission
@endsection

@section('pageJS')
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/food/form.js') }}" type="text/javascript"></script>
@endsection
