@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="text-center mb-8">
  <h2 class="text-2xl font-bold text-green-700">Welcome back, <span id="username"></span>!</h2>
  <p class="text-gray-600">You are logged in as <span id="role"></span>.</p>
</div>

<div class="grid md:grid-cols-3 gap-6">
  <div class="bg-white shadow-md rounded-2xl p-6 text-center hover:shadow-xl transition">
    <h2 class="text-xl font-bold text-green-700">Total Fruits</h2>
    <p id="totalFruits" class="text-3xl font-semibold mt-2 text-gray-700">0</p>
  </div>
  <div class="bg-white shadow-md rounded-2xl p-6 text-center hover:shadow-xl transition">
    <h2 class="text-xl font-bold text-green-700">Today's Sales</h2>
    <p class="text-3xl font-semibold mt-2 text-gray-700">₱4,350</p>
  </div>
  <div class="bg-white shadow-md rounded-2xl p-6 text-center hover:shadow-xl transition">
    <h2 class="text-xl font-bold text-green-700">Pending Orders</h2>
    <p class="text-3xl font-semibold mt-2 text-gray-700">8</p>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // User info
  const user = JSON.parse(localStorage.getItem('loggedInUser'));
  if(!user) window.location.href = '/';
  else {
    document.getElementById('username').textContent = `${user.fname} ${user.lname}`;
    document.getElementById('role').textContent = user.role;
    document.getElementById('userInfo').textContent = user.email;
  }

  document.getElementById('logoutBtn').addEventListener('click', () => {
    localStorage.removeItem('loggedInUser');
    window.location.href = '/';
  });

  // Fetch inventory from localStorage
  function updateTotalFruits() {
    const inventory = JSON.parse(localStorage.getItem('inventory')) || [];
    const totalFruits = inventory.reduce((sum, product) => {
      // Count only products in "Fruit" category
      return product.category.toLowerCase() === 'fruit' ? sum + product.quantity : sum;
    }, 0);
    document.getElementById('totalFruits').textContent = totalFruits;
  }

  // Initial update
  updateTotalFruits();

  // Optional: Update every 2 seconds to reflect changes if inventory page is open in another tab
  setInterval(updateTotalFruits, 2000);
</script>
@endsection
