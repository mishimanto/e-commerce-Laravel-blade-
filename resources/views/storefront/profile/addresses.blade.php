@extends('layouts.user')

@section('title', 'My Addresses - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-6 sm:py-8 lg:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                        <a href="{{ route('profile.dashboard') }}" class="hover:text-indigo-600 transition-colors">
                            Dashboard
                        </a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-gray-900 font-medium">Addresses</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Addresses</h1>
                    <!-- <p class="mt-2 text-sm text-gray-600">Manage your shipping and billing addresses</p> -->
                </div>
                
                <!-- Add New Address Button -->
                <button onclick="openAddressModal()" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Address
                </button>
            </div>
        </div>

        <!-- Addresses Grid -->
        @if($addresses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($addresses as $address)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 relative">
                        <!-- Default Badge -->
                        @if($address->is_default)
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Default
                                </span>
                            </div>
                        @endif

                        <!-- Address Type Icon -->
                        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($address->type == 'billing')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        @endif
                                    </svg>
                                </div>
                                <span class="ml-3 font-medium text-gray-900">
                                    {{ ucfirst($address->type) }} Address
                                </span>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <div class="p-4 sm:p-6">
                            <div class="space-y-2 text-sm">
                                <p class="font-semibold text-gray-900">{{ $address->name }}</p>
                                <p class="text-gray-600">{{ $address->phone }}</p>
                                <p class="text-gray-600">{{ $address->address }}</p>
                                <p class="text-gray-600">
                                    {{ $address->city }}, {{ $address->state }} {{ $address->zip }}
                                </p>
                                <p class="text-gray-600">{{ $address->country }}</p>
                                
                                @if($address->landmark)
                                    <p class="text-gray-500 text-xs mt-2">
                                        Landmark: {{ $address->landmark }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
                            @if(!$address->is_default)
                                <form action="{{ route('profile.addresses.default', $address) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                            title="Set as default">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Set Default
                                    </button>
                                </form>
                            @endif

                            <button onclick="editAddress({{ $address->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>

                            <form action="{{ route('profile.addresses.delete', $address) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this address?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 sm:p-12 lg:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-indigo-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">No addresses saved</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">You haven't added any addresses yet. Add your first address to make checkout faster!</p>
                    <button onclick="openAddressModal()" 
                            class="inline-flex items-center justify-center px-5 sm:px-6 py-2.5 sm:py-3 bg-indigo-600 text-white text-sm sm:text-base font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Address
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div id="addressModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="addressForm" method="POST" action="{{ route('profile.addresses.store') }}">
                @csrf
                <input type="hidden" name="address_id" id="address_id">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900 mb-4" id="modal-title">
                                Add New Address
                            </h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Address Type -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                                    <div class="flex items-center space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="type" value="shipping" class="form-radio text-indigo-600" checked>
                                            <span class="ml-2 text-sm text-gray-700">Shipping</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="type" value="billing" class="form-radio text-indigo-600">
                                            <span class="ml-2 text-sm text-gray-700">Billing</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                    <input type="text" name="name" id="name" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                    <input type="text" name="phone" id="phone" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Address Line -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line *</label>
                                    <input type="text" name="address" id="address" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Landmark -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Landmark (Optional)</label>
                                    <input type="text" name="landmark" id="landmark"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- City -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                    <input type="text" name="city" id="city" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- State -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                    <input type="text" name="state" id="state" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- ZIP Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code *</label>
                                    <input type="text" name="zip" id="zip" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Country -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                    <input type="text" name="country" id="country" required value="Bangladesh"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <!-- Set as Default -->
                                <div class="col-span-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_default" value="1" class="form-checkbox text-indigo-600 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Set as default address</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save Address
                    </button>
                    <button type="button" onclick="closeAddressModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAddressModal() {
    document.getElementById('addressModal').classList.remove('hidden');
    document.getElementById('addressForm').reset();
    document.getElementById('address_id').value = '';
    document.getElementById('modal-title').textContent = 'Add New Address';
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
}

function editAddress(addressId) {
    // Fetch address data via AJAX and populate form
    fetch(`/profile/addresses/${addressId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('address_id').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('phone').value = data.phone;
            document.getElementById('address').value = data.address;
            document.getElementById('landmark').value = data.landmark || '';
            document.getElementById('city').value = data.city;
            document.getElementById('state').value = data.state;
            document.getElementById('zip').value = data.zip;
            document.getElementById('country').value = data.country;
            
            // Set address type radio
            document.querySelector(`input[name="type"][value="${data.type}"]`).checked = true;
            
            // Set default checkbox
            document.querySelector('input[name="is_default"]').checked = data.is_default == 1;
            
            document.getElementById('modal-title').textContent = 'Edit Address';
            document.getElementById('addressModal').classList.remove('hidden');
        });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addressModal');
    if (event.target == modal) {
        closeAddressModal();
    }
}

// Handle form submission for edit
document.getElementById('addressForm').addEventListener('submit', function(e) {
    const addressId = document.getElementById('address_id').value;
    if (addressId) {
        e.preventDefault();
        this.action = `{{ url('profile/addresses') }}/${addressId}`;
        this.method = 'POST';
        
        // Add PUT method spoofing
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_method';
        input.value = 'PUT';
        this.appendChild(input);
        
        this.submit();
    }
});
</script>
@endpush
@endsection