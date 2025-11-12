<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') | CRM FruitStand</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 font-sans flex h-screen">
  <!-- Sidebar -->
  @include('partials.sidebar')

  <!-- Main Content -->
  <main class="flex-1 flex flex-col">
    @include('partials.navbar')
    <section class="p-8 overflow-y-auto flex-1">
      @yield('content')
    </section>
  </main>

  @yield('scripts')
</body>
</html>
