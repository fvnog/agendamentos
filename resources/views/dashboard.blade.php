<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __("Welcome!") }}</h3>
                    <p>{{ __("You're logged in!") }}</p>
                </div>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total Users</h4>
                    <p class="mt-2 text-3xl font-bold text-gray-900">124</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Appointments Today</h4>
                    <p class="mt-2 text-3xl font-bold text-gray-900">15</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Pending Payments</h4>
                    <p class="mt-2 text-3xl font-bold text-gray-900">4</p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('appointments.index') }}" class="text-indigo-600 hover:underline">
                                View All Appointments
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('users.index') }}" class="text-indigo-600 hover:underline">
                                Manage Users
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
