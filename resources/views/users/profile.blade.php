@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">
        <h2 class="text-3xl font-bold mb-6 text-gray-900">My Profile</h2>

        <div class="space-y-3 text-left">
            <p><span class="font-semibold">Name:</span> {{ $user->name }}</p>
            <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
            <p>
                <span class="font-semibold">Status:</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </p>
            <p><span class="font-semibold">Created By:</span> {{ $user->created_by ?? 'N/A' }}</p>
            <p><span class="font-semibold">Updated By:</span> {{ $user->updated_by ?? 'N/A' }}</p>
            <p><span class="font-semibold">Created At:</span> {{ $user->created_at }}</p>
            <p><span class="font-semibold">Updated At:</span> {{ $user->updated_at }}</p>
        </div>
    </div>
</div>
@endsection
