@extends('layouts.app')

@section('title', 'Compare Products - ' . config('app.name'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Compare Products</h1>

        @if($compareItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 w-48">Product</th>
                            @foreach($compareItems as $item)
                                <th class="px-6 py-4 text-center min-w-[200px] relative group">
                                    <button onclick="removeFromCompare({{ $item->product->id }})" 
                                            class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full hover:bg-red-600 opacity-0 group-hover:opacity-100 transition">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <img src="{{ $item->product->images->first()->url ?? asset('images/no-image.jpg') }}" 
                                         alt="{{ $item->product->name }}"
                                         class="h-32 mx-auto object-contain mb-2">
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Price --}}
                        <tr class="border-b">
                            <td class="px-6 py-4 font-medium">Price</td>
                            @foreach($compareItems as $item)
                                <td class="px-6 py-4 text-center">
                                    @if($item->product->sale_price)
                                        <span class="font-bold text-blue-600">৳{{ number_format($item->product->sale_price) }}</span>
                                        <span class="text-sm text-gray-500 line-through block">৳{{ number_format($item->product->base_price) }}</span>
                                    @else
                                        <span class="font-bold text-blue-600">৳{{ number_format($item->product->base_price) }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Rating --}}
                        <tr class="border-b">
                            <td class="px-6 py-4 font-medium">Rating</td>
                            @foreach($compareItems as $item)
                                <td class="px-6 py-4 text-center">
                                    @php $rating = $item->product->reviews->avg('rating') ?? 0; @endphp
                                    <div class="flex justify-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating)
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500">({{ $item->product->reviews->count() }} reviews)</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Availability --}}
                        <tr class="border-b">
                            <td class="px-6 py-4 font-medium">Availability</td>
                            @foreach($compareItems as $item)
                                <td class="px-6 py-4 text-center">
                                    @if($item->product->stock > 0)
                                        <span class="text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i> In Stock
                                        </span>
                                        @if($item->product->stock < 5)
                                            <span class="text-xs text-orange-500 block">Only {{ $item->product->stock }} left</span>
                                        @endif
                                    @else
                                        <span class="text-red-600">
                                            <i class="fas fa-times-circle mr-1"></i> Out of Stock
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Brand --}}
                        <tr class="border-b">
                            <td class="px-6 py-4 font-medium">Brand</td>
                            @foreach($compareItems as $item)
                                <td class="px-6 py-4 text-center">
                                    {{ $item->product->brand->name ?? 'Generic' }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Warranty --}}
                        <tr class="border-b">
                            <td class="px-6 py-4 font-medium">Warranty</td>
                            @foreach($compareItems as $item)
                                <td class="px-6 py-4 text-center">
                                    {{ $item->product->warranty ?? '1 Year' }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Specifications --}}
                        @php
                            $specs = $compareItems->first()->product->specifications ? json_decode($compareItems->first()->product->specifications, true) : [];
                        @endphp
                        
                        @foreach(array_keys($specs) as $spec)
                            <tr class="border-b">
                                <td class="px-6 py-4 font-medium">{{ $spec }}</td>
                                @foreach($compareItems as $item)
                                    @php $itemSpecs = json_decode($item->product->specifications, true) ?? []; @endphp
                                    <td class="px-6 py-4 text-center">
                                        {{ $itemSpecs[$spec] ?? 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- Add to Cart --}}
                        <tr>
                            <td class="px-6 py-4 font-medium">Action</td>
                            @foreach($compareItems as $item)
                                <td class="px-6 py-4 text-center">
                                    @if($item->product->stock > 0)
                                        <button onclick="addToCart({{ $item->product->id }})" 
                                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                                            <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                                        </button>
                                    @else
                                        <button disabled 
                                                class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed text-sm">
                                            Out of Stock
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Clear Compare Button --}}
            <div class="text-center mt-8">
                <button onclick="clearCompare()" 
                        class="px-6 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition">
                    <i class="fas fa-trash mr-1"></i> Clear Comparison
                </button>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-700 mb-2">No products to compare</h2>
                <p class="text-gray-500 mb-6">Add products to compare their specifications side by side</p>
                <a href="{{ route('product.index') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Browse Products
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
async function removeFromCompare(productId) {
    try {
        const response = await fetch(`/compare/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Failed to remove from compare:', error);
    }
}

async function clearCompare() {
    if (confirm('Clear comparison list?')) {
        try {
            const response = await fetch('{{ route("compare.clear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Failed to clear compare:', error);
        }
    }
}

async function addToCart(productId) {
    const response = await fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    });
    
    const data = await response.json();
    if (data.success) {
        window.dispatchEvent(new CustomEvent('cart-updated'));
        alert('Product added to cart!');
    }
}
</script>
@endpush