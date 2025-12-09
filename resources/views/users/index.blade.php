@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-white p-6">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-6 text-center">
            <h2 class="text-3xl font-bold text-gray-900">All Users</h2>
        </div>

        <!-- Table -->
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-800 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-800 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-800 uppercase">Email</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-800 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-4 py-2 text-gray-700 font-medium">{{ $user->id }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="bg-white border-t border-gray-200 p-3 text-center text-sm text-gray-600">
            Total Users: {{ $users->count() }}
        </div>
    </div>
</div>
@endsection
