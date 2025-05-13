<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-600 flex items-center justify-center min-h-screen px-4">
  <div class="bg-white w-full max-w-sm md:max-w-md lg:max-w-lg px-6 py-8 rounded-lg shadow-md">
    <h2 class="text-center text-xl md:text-2xl font-semibold text-gray-700 mb-6">Halaman Login</h2>
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())  <!-- Checks if there are any errors -->
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <ul>
            @foreach ($errors->all() as $error)  <!-- Loops through all errors -->
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('loginError'))  <!-- Display login error if any -->
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('loginError') }}</span>
    </div>
@endif

    <form action="{{ route('login.authenticate') }}" method="POST" class="space-y-5">
        @csrf
      <div>
        <label for="username" class="block text-gray-700 text-sm mb-1">Username</label>
        <input id="username" name="username" type="text" class="w-full px-4 py-2 border rounded bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukan Username..." required>
        @error('username')
        <div class="text-red-500 text-sm">{{ $message }}</div>
      @enderror
      </div>
      <div>
        <label for="password" class="block text-gray-700 text-sm mb-1">Password</label>
        <div class="relative">
          <input id="password" name="password" type="password" class="w-full px-4 py-2 border rounded bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukan Password..." required>
          <button type="button" onclick="togglePassword()" class="absolute right-3 top-2.5 text-gray-500 text-sm">👁️</button>
        </div>
        @error('password')
          <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror
      </div>
      <button type="submit" class="w-full bg-green-600 text-white font-semibold py-2 rounded hover:bg-green-700 transition">Masuk</button>
    </form>
  </div>

  <script>
    function togglePassword() {
      const password = document.getElementById('password');
      password.type = password.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>
