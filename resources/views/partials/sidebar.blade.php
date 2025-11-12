<!-- resources/views/partials/sidebar.blade.php -->
<aside class="w-64 bg-green-700 text-white flex flex-col shadow-lg">
    <div class="p-6 text-center border-b border-green-600">
        <h1 class="text-2xl font-bold">🍍 CRM FruitStand</h1>
    </div>

    <nav class="flex-1 p-4">
      <ul class="space-y-2">
    <li><a href="{{ url('/dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🏠 Dashboard</a></li>
    <li><a href="{{ url('/customers') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">👥 Customers</a></li>
    <li><a href="{{ url('/suppliers') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🚚 Suppliers</a></li>
    <li><a href="{{ url('/suppliers/transactions') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">📦 Supplier Transactions</a></li>
    <li><a href="{{ url('/suppliers/payments') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">💵 Supplier Payments</a></li>
    <li><a href="{{ url('/inventory') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🍎 Inventory</a></li>
    <li><a href="{{ url('/settings') }}" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">⚙️ Settings</a></li>
</ul>
    </nav>

    <div class="p-4 border-t border-green-600">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg font-semibold transition">
                Logout
            </button>
        </form>
    </div>
</aside>
<!-- End of Sidebar -->
