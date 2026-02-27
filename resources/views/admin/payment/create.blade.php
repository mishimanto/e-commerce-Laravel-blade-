@extends('layouts.admin')

@section('title', 'Create Payment Method')

@section('content')
<div class="">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Payment Method</h1>
            <!-- <p class="mt-2 text-sm text-gray-700">Add a new payment method to your store.</p> -->
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.payment.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="mt-8">
        <form action="{{ route('admin.payment.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Basic details about the payment method.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Code <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <p class="mt-2 text-sm text-gray-500">Unique identifier (e.g., bkash, nagad)</p>
                                @error('code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Icon</label>
                                <div class="mt-1 flex items-center space-x-4">
                                    <div class="icon-preview hidden">
                                        <img src="" alt="Preview" class="h-16 w-16 object-cover rounded-lg">
                                    </div>
                                    <input type="file" name="icon" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Upload payment method icon (JPEG, PNG, SVG)</p>
                                @error('icon') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                                <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('type') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                    <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="offline" {{ old('type') == 'offline' ? 'selected' : '' }}>Offline</option>
                                    <option value="online" {{ old('type') == 'online' ? 'selected' : '' }}>Online</option>
                                </select>
                                @error('type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('sort_order') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('sort_order') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label class="font-medium text-gray-700">Active</label>
                                        <p class="text-gray-500">Enable this payment method for customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Fee Configuration</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Set up any fees associated with this payment method.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Fixed Fee</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="fixed_fee" value="{{ old('fixed_fee', 0) }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('fixed_fee') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('fixed_fee') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Percentage Fee (%)</label>
                                <input type="number" name="percentage_fee" value="{{ old('percentage_fee', 0) }}" step="0.01" min="0" max="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('percentage_fee') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('percentage_fee') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Minimum Fee</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="minimum_fee" value="{{ old('minimum_fee') }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('minimum_fee') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('minimum_fee') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Maximum Fee</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="maximum_fee" value="{{ old('maximum_fee') }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('maximum_fee') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('maximum_fee') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Order Restrictions</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Set minimum and maximum order amounts for this payment method.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Minimum Order Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount') }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('minimum_order_amount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('minimum_order_amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Maximum Order Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="maximum_order_amount" value="{{ old('maximum_order_amount') }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('maximum_order_amount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('maximum_order_amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Payment Instructions</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Step-by-step instructions for customers.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="space-y-4" id="instructions-container">
                            <div class="instruction-item">
                                <textarea name="instructions[]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="2" placeholder="Step 1..."></textarea>
                            </div>
                            <div id="more-instructions"></div>
                            <button type="button" onclick="addInstruction()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add More Instructions
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.payment.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let instructionCount = 1;

function addInstruction() {
    const container = document.getElementById('more-instructions');
    const html = `
        <div class="instruction-item mt-4 relative">
            <textarea name="instructions[]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="2" placeholder="Step ${instructionCount + 1}..."></textarea>
            <button type="button" onclick="removeInstruction(this)" class="absolute top-2 right-2 text-red-600 hover:text-red-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    instructionCount++;
}

function removeInstruction(button) {
    button.closest('.instruction-item').remove();
}

// Image preview
document.querySelector('input[name="icon"]').addEventListener('change', function(e) {
    const preview = document.querySelector('.icon-preview');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview) {
                preview.classList.remove('hidden');
                preview.querySelector('img').src = e.target.result;
            } else {
                const html = `
                    <div class="icon-preview">
                        <img src="${e.target.result}" alt="Preview" class="h-16 w-16 object-cover rounded-lg">
                    </div>
                `;
                this.parentNode.insertAdjacentHTML('afterbegin', html);
            }
        }.bind(this);
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endpush
@endsection