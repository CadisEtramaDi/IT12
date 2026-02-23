@extends('layouts.app')

@section('title', 'My Bookings - Minjee Ballon')

@section('content')
<div class="min-h-screen relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/balloon-background.jpg') }}');">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/50 pointer-events-none"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        @include('components.customer-navbar')
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2 drop-shadow-lg">My Bookings</h1>
            <p class="text-white/90 drop-shadow-md">View and manage your event bookings</p>
        </div>

        <!-- Bookings List -->
        @if($bookings->count() > 0)
            <div class="space-y-6">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        Booked on {{ $booking->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    Booking #{{ $booking->bookingID }}
                                </h3>
                                
                                <div class="grid sm:grid-cols-2 gap-4 text-gray-600">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Name</p>
                                        <p class="font-semibold">{{ $booking->customer->fname }} {{ $booking->customer->lname }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Event Date & Time</p>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->eventdate)->format('F d, Y') }} at {{ date('g:i A', strtotime($booking->eventtime)) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Contact</p>
                                        <p>{{ $booking->customer->phonenumber }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Address</p>
                                        <p>{{ $booking->customer->address }}</p>
                                    </div>
                                    @if($booking->totalamount > 0)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Total Amount</p>
                                            <p class="font-semibold text-lg">${{ number_format($booking->totalamount, 2) }}</p>
                                        </div>
                                    @endif
                                    @if($booking->eventdetails)
                                        <div class="sm:col-span-2">
                                            <p class="text-sm font-medium text-gray-500">Event Details</p>
                                            <p>{{ $booking->eventdetails }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $bookings->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Bookings Yet</h2>
                <p class="text-gray-600 mb-6">You haven't made any bookings yet. Start creating your first event!</p>
                <a 
                    href="/create-booking" 
                    class="inline-block px-8 py-3 bg-[#0EA5E9] text-white rounded-lg font-semibold hover:bg-[#0284C7] transition-colors shadow-md"
                >
                    Create Your First Booking
                </a>
            </div>
        @endif
    </div>
    </div>
</div>
@endsection
