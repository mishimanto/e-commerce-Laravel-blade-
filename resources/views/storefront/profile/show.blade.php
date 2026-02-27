@extends('layouts.app')

@section('title', 'Profile Settings - ' . config('app.name'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Profile Settings</h1>

        <div class="grid md:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-center mb-4">
                        <div class="relative inline-block">
                            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                         alt="{{ auth()->user()->name }}"
                                         class="w-24 h-24 rounded-full object-cover">
                                @else
                                    <span class="text-3xl font-bold text-blue-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <button onclick="document.getElementById('avatar-input').click()" 
                                    class="absolute bottom-0 right-0 bg-blue-600 text-white w-8 h-8 rounded-full hover:bg-blue-700">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                            <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="hidden">
                                @csrf
                                <input type="file" id="avatar-input" name="avatar" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                            </form>
                        </div>
                        <h3 class="font-bold">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>

                    <nav class="space-y-1">
                        <a href="{{ route('profile.dashboard') }}" 
                           class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="{{ route('profile.orders') }}" 
                           class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-shopping-bag mr-2"></i> My Orders
                        </a>
                        <a href="{{ route('profile.wishlist') }}" 
                           class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-heart mr-2"></i> Wishlist
                        </a>
                        <a href="{{ route('profile.addresses') }}" 
                           class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-map-marker-alt mr-2"></i> Addresses
                        </a>
                        <a href="{{ route('profile.settings') }}" 
                           class="block px-4 py-2 rounded-lg bg-blue-600 text-white">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Profile Settings Form --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6">Profile Information</h2>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', auth()->user()->name) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', auth()->user()->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" 
                                   name="phone" 
                                   value="{{ old('phone', auth()->user()->phone) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t pt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-bold mb-6">Change Password</h2>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Current Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" 
                                   name="current_password" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" 
                                   name="new_password" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('new_password') border-red-500 @enderror">
                            @error('new_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" 
                                   name="new_password_confirmation" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <div class="border-t pt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Default Address --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Default Address</h2>
                        <button onclick="openAddressModal()" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-plus mr-1"></i> Add New Address
                        </button>
                    </div>

                    @if(auth()->user()->addresses->isNotEmpty())
                        <div class="space-y-4">
                            @foreach(auth()->user()->addresses as $address)
                                <div class="border rounded-lg p-4 {{ $address->is_default ? 'border-blue-500 bg-blue-50' : '' }}">
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="font-medium">{{ $address->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->address }}</p>
                                            <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->zip }}</p>
                                            <p class="text-sm text-gray-600">Phone: {{ $address->phone }}</p>
                                            @if($address->is_default)
                                                <span class="inline-block mt-2 text-xs bg-blue-600 text-white px-2 py-1 rounded">Default</span>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            @if(!$address->is_default)
                                                <button onclick="setDefaultAddress({{ $address->id }})" 
                                                        class="text-blue-600 hover:text-blue-700 text-sm">
                                                    Set as Default
                                                </button>
                                            @endif
                                            <button onclick="editAddress({{ $address->id }})" 
                                                    class="text-gray-600 hover:text-gray-700">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteAddress({{ $address->id }})" 
                                                    class="text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No addresses saved yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Address Modal --}}
    <div x-data="{ show: false, addressId: null }" 
         x-show="show" 
         @keydown.escape.window="show = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="show = false"></div>
            
            <div class="relative bg-white rounded-lg w-full max-w-2xl p-6">
                <h3 class="text-xl font-bold mb-4" x-text="addressId ? 'Edit Address' : 'Add New Address'"></h3>
                
                <form id="address-form" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="address-method" value="POST">
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="name" id="address-name" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" name="phone" id="address-phone" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <input type="text" name="address" id="address-address" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <input type="text" name="city" id="address-city" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State/Division *</label>
                            <input type="text" name="state" id="address-state" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                            <input type="text" name="zip" id="address-zip" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                            <select name="country" id="address-country" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                <option value="Bangladesh">Bangladesh</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_default" id="address-default" value="1"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="address-default" class="ml-2 text-sm text-gray-700">Set as default address</label>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="show = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Save Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function openAddressModal() {
    document.getElementById('address-form').reset();
    document.getElementById('address-method').value = 'POST';
    document.getElementById('address-form').action = '{{ route("profile.addresses.store") }}';
    Alpine.store('modal').show = true;
}

function editAddress(id) {
    fetch(`/profile/addresses/${id}`)
        .then(response => response.json())
        .then(address => {
            document.getElementById('address-name').value = address.name;
            document.getElementById('address-phone').value = address.phone;
            document.getElementById('address-address').value = address.address;
            document.getElementById('address-city').value = address.city;
            document.getElementById('address-state').value = address.state;
            document.getElementById('address-zip').value = address.zip;
            document.getElementById('address-country').value = address.country;
            document.getElementById('address-default').checked = address.is_default;
            
            document.getElementById('address-method').value = 'PUT';
            document.getElementById('address-form').action = `/profile/addresses/${id}`;
            
            Alpine.store('modal').addressId = id;
            Alpine.store('modal').show = true;
        });
}

function setDefaultAddress(id) {
    fetch(`/profile/addresses/${id}/default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(() => {
        window.location.reload();
    });
}

function deleteAddress(id) {
    if (confirm('Delete this address?')) {
        fetch(`/profile/addresses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            window.location.reload();
        });
    }
}
</script>
@endpush