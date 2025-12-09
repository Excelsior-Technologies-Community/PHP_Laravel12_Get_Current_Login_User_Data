<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">

    <!-- Navigation -->
    <nav class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex space-x-4">
                    <!-- <a href="{{ route('dashboard') }}" class="text-gray-800 font-bold px-3 py-2 rounded hover:bg-gray-100">Dashboard</a> -->
                    <a href="{{ route('users.index') }}" class="text-gray-800 px-3 py-2 rounded hover:bg-gray-100">Users</a>
                    <a href="{{ route('profile') }}" class="text-gray-800 px-3 py-2 rounded hover:bg-gray-100">Profile</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-800 px-3 py-2 rounded hover:bg-gray-100">Logout</button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-800 px-3 py-2 rounded hover:bg-gray-100">Login</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-grow flex items-center justify-center px-4">
        @yield('content')
    </main>

</body>
</html>
