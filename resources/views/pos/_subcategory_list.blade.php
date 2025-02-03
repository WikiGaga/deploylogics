
{{-- @foreach ($subcategories as $subCategory) --}}
    <a href="javascript:void(0);"
       class="subcategory-item"
       data-subcategory="">
        <div class="category-icon">
            <img src="{{ asset('assets/images/category/2024-11-20-673de06ce3aa7.png') }}" alt="{{ 'sub-category' }}">
        </div>
        <div class="category-name">{{ 'Italian' }}</div>
    </a>
    <a href="javascript:void(0);"
       class="subcategory-item"
       data-subcategory="">
        <div class="category-icon">
            <img src="{{ asset('assets/images/category/2024-11-20-673de06ce3aa7.png') }}" alt="{{ 'sub-category' }}">
        </div>
        <div class="category-name">{{ 'Arabic' }}</div>
    </a>
{{-- @endforeach --}}
