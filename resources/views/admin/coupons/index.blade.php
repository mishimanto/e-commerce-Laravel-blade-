@extends('layouts.admin')

@section('title', 'Manage Coupons')

@section('content')
<div class="">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Coupons</h1>
        <a href="{{ route('admin.coupons.create') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Coupon
        </a>
    </div>

    <!-- Filters -->
    <div class="w-full max-w-full mb-6 bg-white rounded-lg shadow">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.coupons.index') }}">
                <!-- Mobile Layout (Stacked) -->
                <div class="block space-y-4 md:hidden">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md" 
                                placeholder="Coupon code or name..." 
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="block w-full mt-1 pl-3 pr-10 py-2 border border-gray-300 rounded-md">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" class="block w-full mt-1 pl-3 pr-10 py-2 border border-gray-300 rounded-md">
                            <option value="">All</option>
                            @foreach(App\Models\Coupon::TYPES as $value => $label)
                                <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
                            Filter
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-center">
                            Reset
                        </a>
                    </div>
                </div>

                <!-- Desktop Layout (Grid) -->
                <div class="hidden md:grid md:grid-cols-4 md:gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md" 
                                placeholder="Search..." 
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="block w-full mt-1 pl-3 pr-10 py-2 border border-gray-300 rounded-md">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" class="block w-full mt-1 pl-3 pr-10 py-2 border border-gray-300 rounded-md">
                            <option value="">All</option>
                            @foreach(App\Models\Coupon::TYPES as $value => $label)
                                <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
                            Filter
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-center">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $coupon->code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">{{ $coupon->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($coupon->type == 'fixed')
                                    <span class="px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">Fixed</span>
                                @elseif($coupon->type == 'percentage')
                                    <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Percentage</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">Free Shipping</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">
                                    @if($coupon->type == 'percentage')
                                        {{ $coupon->value }}%
                                    @elseif($coupon->type == 'fixed')
                                        ৳{{ number_format($coupon->value, 2) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">
                                    {{ $coupon->total_used ?? 0 }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">
                                    <div>From: {{ $coupon->starts_at->format('M d, Y') }}</div>
                                    <div>To: {{ $coupon->expires_at->format('M d, Y') }}</div>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $now = now();
                                    $isExpired = $coupon->expires_at < $now;
                                    $isActive = $coupon->is_active && $coupon->starts_at <= $now && !$isExpired;
                                @endphp
                                
                                @if($isActive)
                                    <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Active</span>
                                @elseif($isExpired)
                                    <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Expired</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            onclick="toggleStatus({{ $coupon->id }})"
                                            class="{{ !$isExpired && $coupon->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}"
                                            title="{{ !$isExpired && $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if(!$isExpired && $coupon->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <button type="button" 
                                            onclick="duplicateCoupon({{ $coupon->id }})"
                                            class="text-gray-600 hover:text-gray-900" title="Duplicate">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure?')" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <p class="text-gray-500">No coupons found.</p>
                                <a href="{{ route('admin.coupons.create') }}" class="text-indigo-600 hover:text-indigo-900">
                                    Create your first coupon
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $coupons->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(couponId) {
    fetch(`/admin/coupons/${couponId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function duplicateCoupon(couponId) {
    if (confirm('Duplicate this coupon?')) {
        fetch(`/admin/coupons/${couponId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(() => {
            location.reload();
        });
    }
}
</script>
@endpush
@endsection