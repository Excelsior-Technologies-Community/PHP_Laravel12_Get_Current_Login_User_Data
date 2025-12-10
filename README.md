# PHP_Laravel12_Get_Current_Login_User_Data
---
This *README.md* provides a *complete Laravel project structure* with *all code, **all comments, and **step‑by‑step explanation*.
It includes:
---
*  Registration & Login (Laravel Breeze)
*  Store sessions in database
*  Get current logged‑in user
*  Show profile page
*  Show all users page
*  Auto‑set created_by & updated_by
*  Migrations + Models + Controllers + Routes + Views

Everything is made *as simple as possible* for beginners.

---

#  1. Project Setup — Commands

Run these in your terminal:

# 1. Create Laravel project
```

composer create-project laravel/laravel PHP_Laravel12_Get_Current_Login_User_Data "12.*"
cd PHP_Laravel12_Get_Current_Login_User_Data
```

# 2. Install Laravel Breeze (for login/register UI)
```

composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
```

# 3. Create session table
```

php artisan session:table
```

# 4. Run migrations
```

php artisan migrate
```

# 5. Start server
```
php artisan serve
```


Visit:
```
[http://localhost:8000](http://localhost:8000)
```

---

#  2. .env Configuration

Make sure your .env file contains:
```


DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_user_demo
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
```


*SESSION_DRIVER=database* means Laravel will store session data inside the sessions table.

---

#  3. Migrations (Database Tables)

The migration below creates *users, **password_resets, and **sessions* tables.

### database/migrations/xxxx_xx_xx_create_user_system.php

```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS TABLE
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // User full name
            $table->string('email')->unique();          // Email must be unique
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                 // Hashed password

            // New fields
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();                      // Adds deleted_at
        });

        // PASSWORD RESETS
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // SESSIONS TABLE
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
    }
};

```



#  4. User Model

### app/Models/User.php

```

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    // Mass assignable fields
    protected $fillable = [
        'name', 'email', 'password', 'status', 'created_by', 'updated_by'
    ];

    // Hidden fields
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Automatically fill created_by / updated_by
    protected static function booted()
    {
        static::creating(function ($user) {
            if (Auth::check()) {
                $user->created_by = Auth::id();
            }
        });

        static::updating(function ($user) {
            if (Auth::check()) {
                $user->updated_by = Auth::id();
            }
        });
    }
}


```


#  5. Controllers

## 5.1 UserController

### app/Http/Controllers/UserController.php

```

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');   // Protect all routes
    }

    // Show all users
    public function index()
    {
        $users = User::all();        // Fetch all users
        return view('users.index', compact('users'));
    }

    // Show logged-in user's profile
    public function profile()
    {
        $user = Auth::user();        // Get current logged-in user
        return view('users.profile', compact('user'));
    }

    // Create a new user (Admin only)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'User created successfully.');
    }
}


```


## 5.2 Registration Controller

### app/Http/Controllers/Auth/RegisteredUserController.php

```

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // Show register form
    public function create(): View
    {
        return view('auth.register');
    }

    // Store new user
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Fire registered event
        event(new Registered($user));

        // Log in the user
        Auth::login($user);

        // Update created_by after login so it's not null
        $user->update([
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect(route('users.index'));
    }
}


```


#  6. Routes

### routes/web.php

```

<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

// Auth routes (login/register/logout)
require __DIR__ . '/auth.php';


```


#  7. Blade Layout

### resources/views/layouts/app.blade.php

```

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<nav class="bg-white shadow mb-8">
    <div class="max-w-7xl mx-auto px-4 flex justify-between h-16 items-center">
        <div class="flex space-x-4">
            <a href="{{ route('users.index') }}" class="px-3 py-2 hover:bg-gray-100">Users</a>
            <a href="{{ route('profile') }}" class="px-3 py-2 hover:bg-gray-100">Profile</a>
        </div>

        <div class="flex items-center space-x-4">
            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="px-3 py-2 hover:bg-gray-100">Logout</button>
            </form>
            @else
            <a href="{{ route('login') }}" class="px-3 py-2 hover:bg-gray-100">Login</a>
            @endauth
        </div>
    </div>
</nav>

<main class="flex-grow flex items-center justify-center px-4">
    @yield('content')
</main>

</body>
</html>


```


#  8. Profile Page

### resources/views/users/profile.blade.php

```

@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">
        <h2 class="text-3xl font-bold mb-6">My Profile</h2>

        <div class="space-y-3 text-left">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Status:</strong>
                <span class="px-3 py-1 rounded-full text-xs
                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </p>
            <p><strong>Created By:</strong> {{ $user->created_by ?? 'N/A' }}</p>
            <p><strong>Updated By:</strong> {{ $user->updated_by ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $user->created_at }}</p>
            <p><strong>Updated At:</strong> {{ $user->updated_at }}</p>
        </div>
    </div>
</div>
@endsection

```


#  9. Users List Page

### resources/views/users/index.blade.php

```

@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-white p-6">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-lg">

        <div class="p-6 border-b text-center">
            <h2 class="text-3xl font-bold">All Users</h2>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $user->id }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t text-center text-sm text-gray-600">
            Total Users: {{ $users->count() }}
        </div>
    </div>
</div>
@endsection

```

#  10. How to Get Logged-in User

You can get the current user using:

### In Controller

```

$user = Auth::user();
```


### In Blade

```
{{ Auth::user()->name }}

```
### Only ID

```
Auth::id();

```
---

#  All Done!

Your entire Laravel system is ready with:
```
* Registration & Login
* Database Sessions
* User Listing
* User Profile
* Automatic created_by & updated_by
* Clean UI with TailwindCSS
```
