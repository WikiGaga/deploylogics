{{-- @php use App\CentralLogics\Helpers; @endphp
@php($restaurant_data = Helpers::get_restaurant_data()) --}}
{{-- @if (!$products->isEmpty()) --}}
    <div class="row g-3 mb-auto">
        <div class="col-sm-12">
            <div class="row g-3">
                {{-- @foreach     ($products as $product) --}}
                    <div class="order--item-box item-box col-auto">
                        @include('vendor-views.pos._single_product', [
                            // 'product' => $product,
                            // 'restaurant_data' => $restaurant_data,
                        ])
                    </div>
                {{-- @endforeach --}}
            </div>
        </div>
    </div>
{{-- @else --}}
    {{-- <div class="my-auto">
        <div class="search--no-found">
            <img src="{{asset('assets/images/category/2024-11-20-673de06ce3aa7.png')}}" alt="img">
            <p>
                {{ __('To get required search result First select zone & then restaurant to search category wise food or search manually to find food under that restaurant') }}
            </p>
        </div>
    </div> --}}
{{-- @endif --}}
