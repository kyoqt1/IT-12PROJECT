<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM / D🍊NDON FruitStand | Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom gradient button */
    .btn-gradient {
      background: linear-gradient(90deg, #34d399, #3b82f6);
    }
    .btn-gradient:hover {
      background: linear-gradient(90deg, #3b82f6, #34d399);
    }

    /* Floating fruits animation */
    .fruit {
      position: absolute;
      animation: float 6s ease-in-out infinite;
      font-size: 2rem;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-15px) rotate(15deg); }
    }
  </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-yellow-100 to-green-100 min-h-screen flex items-center justify-center font-sans">

  <!-- Center Container -->
  <div class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row w-[90%] max-w-5xl transition-all duration-500 transform hover:scale-[1.01]">

    <!-- LEFT SIDE: Branding Section -->
    <div class="bg-green-600 text-white flex flex-col justify-center items-center w-full md:w-1/2 p-12 relative overflow-hidden">
      <img src="https://cdn-icons-png.flaticon.com/512/415/415682.png" alt="Fruit Logo" class="w-32 h-32 mb-5 drop-shadow-lg animate-bounce">
      <h1 class="text-4xl font-extrabold mb-3 text-center">CRM / D<span class="text-yellow-400">🍊</span>NDON FruitStand</h1>
      <p class="text-green-100 text-center text-sm max-w-xs">Manage your fruits, sales, and customers efficiently — all in one platform 🫛🍊</p>
      
      <!-- Floating Fruits -->
      <div class="fruit" style="top:10%; left:5%;">🫛</div>
      <div class="fruit" style="top:20%; right:10%;">🍊</div>
      <div class="fruit" style="bottom:15%; left:15%;">🫛</div>
      <div class="fruit" style="bottom:10%; right:5%;">🍊</div>
    </div>

    <!-- RIGHT SIDE: Login Form -->
    <div class="w-full md:w-1/2 p-12 flex flex-col justify-center relative">
      <h2 class="text-3xl font-bold text-green-700 mb-2 text-center">Welcome Back!</h2>
      <p class="text-gray-500 text-center mb-8">Login with your email and password</p>

      <form id="loginForm" class="space-y-6">
        <div>
          <label class="block mb-1 font-medium text-gray-600">Email</label>
          <input type="email" id="email" required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-400 shadow-sm hover:shadow-md transition duration-200"
            placeholder="Enter your email">
        </div>

        <div>
          <label class="block mb-1 font-medium text-gray-600">Password</label>
          <input type="password" id="password" required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-400 shadow-sm hover:shadow-md transition duration-200"
            placeholder="Enter your password">
        </div>

        <button type="submit"
          class="w-full text-white font-semibold py-3 rounded-lg btn-gradient shadow-lg hover:shadow-xl transition duration-300">
          Login
        </button>

        <p id="error" class="text-center text-red-500 mt-3 hidden">Invalid email or password. Try again.</p>
      </form>

      <p class="text-center text-gray-400 text-sm mt-10">© 2025 CRM / D🍊NDON FruitStand</p>
    </div>

  </div>

  <script>
    // Temporary Users Data
    const users = [
      { user_id: 1, fname: 'Cris', lname: 'Lee', email: 'cris@fruitstand.com', password: 'admin123', role: 'Admin' },
      { user_id: 2, fname: 'Judith', lname: 'Perez', email: 'judith@fruitstand.com', password: 'staff123', role: 'Staff' },
      { user_id: 3, fname: 'James', lname: 'Reyes', email: 'james@fruitstand.com', password: 'seller123', role: 'Seller' }
    ];

    // Handle Login
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value.trim();

      const user = users.find(u => u.email === email && u.password === password);

      if (user) {
        localStorage.setItem('loggedInUser', JSON.stringify(user));
        window.location.href = '/dashboard';
      } else {
        document.getElementById('error').classList.remove('hidden');
      }
    });
  </script>

</body>
</html>
