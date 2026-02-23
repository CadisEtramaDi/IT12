@extends('layouts.app')

@section('title', 'Check Availability - Minjee Ballon')

@section('content')
<div class="min-h-screen relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/balloon-background.jpg') }}');">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/50 pointer-events-none"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        @include('components.customer-navbar')
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="text-center mb-12">
            <div class="inline-block mb-4 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                <span class="text-sm font-semibold text-white">📅 Real-time Availability</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4 drop-shadow-lg">Check Availability</h1>
            <p class="text-lg text-white/90 max-w-2xl mx-auto drop-shadow-md">
                See if your preferred date is available for booking. We'll show you all bookings for the selected date.
            </p>
        </div>

        <!-- Availability Checker Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <form method="GET" action="{{ route('availability.check') }}" class="space-y-6">
                <div>
                    <label for="check_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Date *
                    </label>
                    <input
                        type="date"
                        id="check_date"
                        name="date"
                        required
                        value="{{ request('date') }}"
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all text-lg"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full px-6 py-4 bg-gradient-to-r from-[#0EA5E9] to-sky-500 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all duration-300"
                >
                    Check Availability →
                </button>
            </form>
        </div>

        <!-- Results Section -->
        @if(request('date'))
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        Results for {{ \Carbon\Carbon::parse(request('date'))->format('F d, Y') }}
                    </h2>
                </div>

                @if($bookings->count() > 0)
                    <!-- Availability Status -->
                    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-yellow-800">Limited Availability</p>
                                <p class="text-sm text-yellow-700">{{ $bookings->count() }} booking(s) already scheduled for this date</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings List -->
                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-700 mb-3">Existing Bookings:</h3>
                        @foreach($bookings as $booking)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-sky-300 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $booking->status === 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-700">
                                                🕐 {{ date('g:i A', strtotime($booking->eventtime)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            Booked by: <span class="font-medium text-gray-900">{{ $booking->customer->fname }} {{ $booking->customer->lname }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Contact: {{ $booking->customer->phonenumber }}
                                        </p>
                                        @if($booking->eventdetails)
                                            <p class="text-sm text-gray-500 mt-2 italic">{{ Str::limit($booking->eventdetails, 100) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 p-4 bg-sky-50 rounded-lg">
                        <p class="text-sm text-gray-700">
                            <strong>Note:</strong> While there are existing bookings on this date, we may still be able to accommodate your event. 
                            Please <a href="/contact" class="text-[#0EA5E9] font-semibold hover:underline">contact us</a> or 
                            <a href="/create-booking" class="text-[#0EA5E9] font-semibold hover:underline">proceed with booking</a> to discuss your requirements.
                        </p>
                    </div>
                @else
                    <!-- Available Status -->
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Date is Available!</h3>
                        <p class="text-gray-600 mb-6">
                            Great news! We have no bookings scheduled for {{ \Carbon\Carbon::parse(request('date'))->format('F d, Y') }}.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a 
                                href="/create-booking?date={{ request('date') }}" 
                                class="inline-block px-8 py-3 bg-gradient-to-r from-[#0EA5E9] to-sky-500 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all duration-300"
                            >
                                Book This Date Now
                            </a>
                            <a 
                                href="/contact" 
                                class="inline-block px-8 py-3 bg-white text-[#0EA5E9] border-2 border-[#0EA5E9] rounded-lg font-semibold hover:bg-sky-50 transition-all duration-300"
                            >
                                Contact Us First
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Instructions -->
            <div class="bg-gradient-to-r from-sky-50 to-purple-50 rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">How to Check Availability</h3>
                <div class="space-y-3 text-gray-700">
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-8 h-8 bg-[#0EA5E9] text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">1</span>
                        <p><strong>Select a Date:</strong> Choose your preferred event date from the calendar above.</p>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-8 h-8 bg-[#0EA5E9] text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">2</span>
                        <p><strong>View Results:</strong> We'll show you all existing bookings for that date with their scheduled times.</p>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-8 h-8 bg-[#0EA5E9] text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">3</span>
                        <p><strong>Book or Contact:</strong> If available, proceed with booking or contact us with questions.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid sm:grid-cols-3 gap-6 mt-8">
                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-[#0EA5E9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-1">Instant Results</h4>
                    <p class="text-sm text-gray-600">Get availability info immediately</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-1">Real-time Data</h4>
                    <p class="text-sm text-gray-600">Always up-to-date information</p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-1">Plan Ahead</h4>
                    <p class="text-sm text-gray-600">Check multiple dates easily</p>
                </div>
            </div>
        @endif
    </div>
    </div>
</div>
@endsection
