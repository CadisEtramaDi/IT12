@extends('layouts.app')

@section('title', 'Create Booking - Minjee Ballon')

@section('content')
<div class="min-h-screen relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/balloon-background.jpg') }}');">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/50 pointer-events-none"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        @include('components.customer-navbar')
        
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2 drop-shadow-lg">Create Booking</h1>
            <p class="text-white/90 drop-shadow-md">Fill out the form below to book your event</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8">
            <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name *
                        </label>
                        <input
                            type="text"
                            id="firstName"
                            name="first_name"
                            required
                            value="{{ old('first_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                            placeholder="Enter your first name"
                        />
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name *
                        </label>
                        <input
                            type="text"
                            id="lastName"
                            name="last_name"
                            required
                            value="{{ old('last_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                            placeholder="Enter your last name"
                        />
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="contactNumber" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Number *
                        </label>
                        <input
                            type="tel"
                            id="contactNumber"
                            name="contact_number"
                            required
                            value="{{ old('contact_number') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                            placeholder="+1 (555) 000-0000"
                        />
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address *
                        </label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            required
                            value="{{ old('address') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                            placeholder="Your address"
                        />
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address *
                    </label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        required
                        value="{{ old('address') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                        placeholder="Your complete address"
                    />
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="eventDate" class="block text-sm font-medium text-gray-700 mb-2">
                            Event Date *
                        </label>
                        <input
                            type="date"
                            id="eventDate"
                            name="event_date"
                            required
                            value="{{ old('event_date', request('date')) }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                        />
                        @error('event_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="eventTime" class="block text-sm font-medium text-gray-700 mb-2">
                            Event Time *
                        </label>
                        <input
                            type="time"
                            id="eventTime"
                            name="event_time"
                            required
                            value="{{ old('event_time') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all"
                        />
                        @error('event_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="eventDetails" class="block text-sm font-medium text-gray-700 mb-2">
                        Event Details *
                    </label>
                    <textarea
                        id="eventDetails"
                        name="event_details"
                        rows="4"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent transition-all resize-none"
                        placeholder="Describe your event: type (wedding, birthday, corporate, etc.), number of guests, specific requirements, balloon decoration preferences, etc."
                    >{{ old('event_details') }}</textarea>
                    @error('event_details')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full px-6 py-4 bg-[#0EA5E9] text-white rounded-lg font-semibold hover:bg-[#0284C7] transition-colors shadow-md hover:shadow-lg"
                >
                    Submit Booking
                </button>
            </form>
        </div>
    </div>
    </div>
</div>
@endsection

<!-- Success Modal (Outside main content for proper overlay) -->
@section('modals')
<div id="successModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all scale-95 hover:scale-100">
        <div class="text-center space-y-4">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Booking Successful!</h2>
            <p class="text-gray-600">
                Your booking has been submitted successfully. We will contact you shortly to confirm your booking.
            </p>
            <button
                onclick="closeModal()"
                class="w-full px-6 py-3 bg-[#0EA5E9] text-white rounded-lg font-semibold hover:bg-[#0284C7] transition-colors shadow-md"
            >
                Close
            </button>
        </div>
    </div>
</div>

<script>
    // Check for success message from server
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showModal();
        });
    @endif

    function showModal() {
        document.getElementById('successModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }

    function closeModal() {
        document.getElementById('successModal').classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Close modal when clicking outside
    document.getElementById('successModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
