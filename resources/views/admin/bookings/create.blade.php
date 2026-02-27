@extends('layouts.admin')

@section('title', 'Create New Booking')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Booking</h1>
        <p class="text-gray-600">Register a new customer booking</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors font-medium shadow-sm border border-gray-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back
    </a>
</div>

<div class="bg-white rounded-xl shadow-md p-8 max-w-3xl">
    <form id="bookingForm" action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="customerID" value="new">

        <!-- Customer Section -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="fname" required placeholder="John" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('fname') border-red-500 @enderror"
                           value="{{ old('fname') }}">
                    @error('fname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" name="lname" required placeholder="Doe" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('lname') border-red-500 @enderror"
                           value="{{ old('lname') }}">
                    @error('lname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" name="phonenumber" required placeholder="+63 9xx-xxx-xxxx" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('phonenumber') border-red-500 @enderror"
                           value="{{ old('phonenumber') }}">
                    @error('phonenumber')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" placeholder="Street address" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent"
                           value="{{ old('address') }}">
                </div>
            </div>
        </div>

        <!-- Booking Details Section -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                    <input type="date" name="eventDATE" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('eventDATE') border-red-500 @enderror"
                           value="{{ old('eventDATE') }}">
                    @error('eventDATE')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                    <input type="time" name="timeStart" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('timeStart') border-red-500 @enderror"
                           value="{{ old('timeStart') }}">
                    @error('timeStart')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                    <input type="time" name="timeEND" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('timeEND') border-red-500 @enderror"
                           value="{{ old('timeEND') }}">
                    @error('timeEND')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Location *</label>
                <textarea name="eventLocation" required rows="3" placeholder="Venue or event location..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('eventLocation') border-red-500 @enderror">{{ old('eventLocation') }}</textarea>
                @error('eventLocation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Inventory Items Section -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory Items</h3>

            @if($inventoryItems->count() > 0)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-gray-600">Item</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-gray-600">Category</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-gray-600">Available for Booking</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-gray-600">Price</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-gray-600">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($inventoryItems as $item)
                                @php
                                    $availableQty = $item->quantityAvailable - $item->quantityDamaged;
                                @endphp
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-gray-900">{{ $item->itemName }}</div>
                                        <div class="text-xs text-gray-500">#{{ $item->itemID }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $item->category }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $availableQty }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900 font-semibold">₱{{ number_format($item->rentalPrice, 2) }}</td>
                                    <td class="py-3 px-4">
                                        <input type="number"
                                               name="items[{{ $item->itemID }}]"
                                               min="0"
                                               max="{{ $availableQty }}"
                                               placeholder="0"
                                               value="{{ old('items.' . $item->itemID) }}"
                                               data-price="{{ $item->rentalPrice }}"
                                               class="quantity-input w-24 px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-500 mt-2">Enter quantities for the items you want to include in this booking.</p>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
                    No inventory items available yet. Add items in Inventory first.
                </div>
            @endif
        </div>

        <!-- Payment Section -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount & Status</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount (₱) *</label>
                    <input type="number" id="totalAmount" name="totalAmount" required min="0" step="0.01" placeholder="0.00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('totalAmount') border-red-500 @enderror"
                           value="{{ old('totalAmount') }}" readonly>
                    @error('totalAmount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0EA5E9] focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ old('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.bookings.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                Cancel
            </a>
            <button type="button" onclick="openConfirmationModal()" class="px-6 py-2 bg-[#0EA5E9] text-white rounded-lg hover:bg-sky-600 transition-colors font-medium">
                Create Booking
            </button>
        </div>
    </form>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4">
        <div class="bg-gradient-to-r from-[#0EA5E9] to-sky-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-bold text-white">Confirm Booking</h3>
        </div>
        
        <div class="px-6 py-4">
            <p class="text-gray-700 mb-4">Are you sure you want to create this booking?</p>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded p-4 mb-4">
                <p class="text-sm text-gray-700">
                    <span class="font-semibold">Total Amount:</span> 
                    <span class="text-blue-600 font-bold">₱<span id="modalTotalAmount">0.00</span></span>
                </p>
                <p class="text-sm text-gray-700 mt-2">
                    <span class="font-semibold">Status:</span> 
                    <span id="modalStatus">Pending</span>
                </p>
            </div>

            <p class="text-xs text-gray-500">This action will create the booking and allocate the selected inventory items.</p>
        </div>
        
        <div class="flex gap-3 px-6 py-4 bg-gray-50 rounded-b-lg">
            <button type="button" onclick="closeConfirmationModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                Cancel
            </button>
            <button type="button" onclick="submitBookingForm(event)" class="flex-1 px-4 py-2 bg-[#0EA5E9] text-white rounded-lg hover:bg-sky-600 transition-colors font-medium">
                Confirm Booking
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const totalAmountField = document.getElementById('totalAmount');

        function calculateTotal() {
            let total = 0;
            
            quantityInputs.forEach(input => {
                const quantity = parseInt(input.value) || 0;
                const price = parseFloat(input.dataset.price) || 0;
                total += quantity * price;
            });

            totalAmountField.value = total.toFixed(2);
            document.getElementById('modalTotalAmount').textContent = total.toFixed(2);
        }

        // Add event listener to all quantity inputs
        quantityInputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        // Update modal status when status select changes
        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                document.getElementById('modalStatus').textContent = this.value;
            });
        }

        // Calculate initial total if there are old values
        calculateTotal();
    });

    function openConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.remove('hidden');
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.add('hidden');
    }

    function submitBookingForm(event) {
        const form = document.getElementById('bookingForm');
        
        if (!form) {
            alert('Form not found. Please refresh the page and try again.');
            return;
        }

        // Disable confirm button to prevent double submission
        const confirmBtn = event.target;
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Creating...';

        // Create FormData object which properly handles multipart/form-data and includes CSRF token
        const formData = new FormData(form);

        // Submit the form
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Handle redirects
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            
            return response.text();
        })
        .then(html => {
            // If we got here and it's HTML, we likely have an error
            if (html && (html.includes('<!DOCTYPE') || html.includes('<html'))) {
                // Check if there are validation errors
                if (html.includes('error') || html.includes('Error')) {
                    alert('An error occurred. Please check the form and try again.');
                }
                document.body.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the booking: ' + error.message);
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Booking';
        });
    }

    // Close modal when clicking outside
    document.getElementById('confirmationModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeConfirmationModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeConfirmationModal();
        }
    });
</script>
@endsection
