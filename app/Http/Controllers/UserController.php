<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile()
    {
        $user = Auth::user()->load(['creator', 'updater']); // Load relationships
        return view('users.profile', compact('user'));
    }

    public function index()
    {
        $users = User::with(['creator', 'updater'])->get(); // Load relationships
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'status'     => 'active',

            // VERY IMPORTANT → now fields will never be null
            // 'created_by' => Auth::id(),

        ]);

        return back()->with('success', 'User created successfully.');
    }

       // Toggle user status
    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->updated_by = Auth::id();
        $user->save();

        return back()->with('success', 'User status updated successfully.');
    }
}
