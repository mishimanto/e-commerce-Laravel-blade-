@extends('layouts.admin')

@section('title', 'Manage Couriers')

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Courier Management</h1>
        <a href="{{ route('admin.couriers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Courier
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($couriers as $courier)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $courier->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($courier->logo)
                                <img src="{{ asset('storage/' . $courier->logo) }}" alt="{{ $courier->name }}" class="h-8 w-8 mr-3">
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $courier->name }}</div>
                                <div class="text-xs text-gray-500">{{ $courier->description ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $courier->code }}</td>
                    <td class="px-6 py-4">
                        @if($courier->status)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.couriers.edit', $courier) }}" class="text-blue-600 hover:text-blue-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this courier?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No couriers found. 
                        <a href="{{ route('admin.couriers.create') }}" class="text-blue-600 hover:underline">Add your first courier</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $couriers->links() }}
    </div>
</div>
@endsection