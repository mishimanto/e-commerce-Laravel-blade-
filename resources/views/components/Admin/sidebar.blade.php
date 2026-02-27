@props(['pendingOrders' => 0])

<aside class="w-64 bg-gray-800 text-white fixed h-full overflow-y-auto" x-data="{ sidebarOpen: true }">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-16 bg-gray-900">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white">
            {{ setting('store_name', 'Admin Panel') }}
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="mt-6">
        {{-- Dashboard --}}
        <x-admin.menu-item 
            icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" 
            title="Dashboard" 
            route="admin.dashboard" 
        />

        {{-- Categories Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" 
            title="Categories"
            :active="request()->routeIs('admin.categories.*')"
            :items="[
                ['title' => 'All Categories', 'route' => 'admin.categories.index'],
                ['title' => 'Add New', 'route' => 'admin.categories.create'],
            ]"
        />

        {{-- Brands Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" 
            title="Brands"
            :active="request()->routeIs('admin.brands.*')"
            :items="[
                ['title' => 'All Brands', 'route' => 'admin.brands.index'],
                ['title' => 'Add New', 'route' => 'admin.brands.create'],
            ]"
        />

        {{-- Attributes Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M4 7h16M4 12h16M4 17h16" 
            title="Attributes"
            :active="request()->routeIs('admin.attributes.*')"
            :items="[
                ['title' => 'All Attributes', 'route' => 'admin.attributes.index'],
                ['title' => 'Add New', 'route' => 'admin.attributes.create'],
            ]"
        />

        {{-- Products Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" 
            title="Products"
            :active="request()->routeIs('admin.products.*')"
            :items="[
                ['title' => 'All Products', 'route' => 'admin.products.index'],
                ['title' => 'Add New', 'route' => 'admin.products.create'],
            ]"
        />

        {{-- Orders Single Link --}}
        <x-admin.menu-item 
            icon="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" 
            title="Orders"
            route="admin.orders.index"
            :active="request()->routeIs('admin.orders.*')"
            :badge="$pendingOrders > 0 ? $pendingOrders : null"
        />

        {{-- Inventory Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" 
            title="Inventory"
            :active="request()->routeIs('admin.inventory.*')"
            :items="[
                ['title' => 'Stock Overview', 'route' => 'admin.inventory.index'],
                ['title' => 'Low Stock Alert', 'route' => 'admin.inventory.index', 'params' => ['stock_status' => 'low_stock']],
                ['title' => 'Out of Stock', 'route' => 'admin.inventory.index', 'params' => ['stock_status' => 'out_of_stock']],
            ]"
        />

        {{-- Users Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" 
            title="Users"
            :active="request()->routeIs('admin.users.*')"
            :items="[
                ['title' => 'All Users', 'route' => 'admin.users.index'],
            ]"
        />

        {{-- Staff Management - Only for Super Admin --}}
        @if(auth()->user() && auth()->user()->isSuperAdmin())
            <x-admin.menu-item 
                icon="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" 
                title="Staff Management" 
                route="admin.staff.index" 
                :badge="\App\Models\User::whereHas('roles', function($q) { $q->whereIn('role_id', [2,3]); })->count()"
            />
        @endif

        {{-- Contact Messages --}}
        <x-admin.menu-item 
            icon="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" 
            title="Messages" 
            route="admin.contact-messages.index"
            :badge="\App\Models\ContactMessage::where('is_read', false)->count()" 
        />

        {{-- Marketing Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" 
            title="Marketing"
            :active="request()->routeIs('admin.coupons.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.newsletter.*') || request()->routeIs('admin.newsletter.*')"
            :items="[
                ['title' => 'Coupons', 'route' => 'admin.coupons.index'],
                ['title' => 'Banners', 'route' => 'admin.banners.index'],
                ['title' => 'Newsletter', 'route' => 'admin.newsletter.index'],
                ['title' => 'Campaigns', 'route' => 'admin.newsletter.campaigns.index'],
            ]"
        />

        {{-- Reports Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" 
            title="Reports"
            :active="request()->routeIs('admin.reports.*')"
            :items="[
                ['title' => 'Sales Report', 'route' => 'admin.reports.sales'],
                ['title' => 'Inventory Report', 'route' => 'admin.reports.inventory'],
                ['title' => 'Customer Report', 'route' => 'admin.reports.customers'],
            ]"
        />

        {{-- Settings Dropdown --}}
        <x-admin.menu-dropdown 
            icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" 
            title="Settings"
            :active="request()->routeIs('admin.settings.*')"
            :items="[
                ['title' => 'General', 'route' => 'admin.settings.index', 'params' => ['group' => 'general']],
                ['title' => 'Shipping Methods', 'route' => 'admin.shipping.index'],
                ['title' => 'Payment Methods', 'route' => 'admin.payment.index'],
            ]"
        />

        {{-- Courier Management --}}
        <x-admin.menu-item 
            icon="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" 
            title="Couriers" 
            route="admin.couriers.index" 
        />
    </nav>
</aside>