<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('admin.login');
    }

    // Process login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Authenticate against users table
        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'admin_logged_in' => true,
                'user_id' => $user->userID,
                'username' => $user->username,
                'role' => $user->role,
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . ucfirst($user->username) . '!');
        }

        return back()->with('error', 'Invalid credentials. Please try again.');
    }

    // Logout
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }

    // Dashboard
    public function dashboard()
    {
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'Pending')->count();
        $confirmedBookings = Booking::where('status', 'Confirmed')->count();
        $cancelledBookings = Booking::where('status', 'Cancelled')->count();
        $paidBookings = Booking::where('status', 'Completed')->count();
        $recentBookings = Booking::with('customer')->latest()->take(10)->get();
        
        // Sales statistics
        $totalSales = \App\Models\Payment::where('status', 'completed')->sum('amountpaid');
        $monthSales = \App\Models\Payment::where('status', 'completed')
            ->whereMonth('paymentdate', now()->month)
            ->sum('amountpaid');

        return view('admin.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'cancelledBookings',
            'paidBookings',
            'recentBookings',
            'totalSales',
            'monthSales'
        ));
    }

    // List all bookings
    public function bookingsIndex(Request $request)
    {
        $query = Booking::with('customer');

        // Filter by status
        if ($request->filled('status')) {
            $status = $this->normalizeBookingStatus($request->status);
            if ($status) {
                $query->where('status', $status);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('phonenumber', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest()->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    // Show single booking
    public function bookingsShow($id)
    {
        $booking = Booking::with(['customer', 'payments'])->findOrFail($id);
        $totalPaid = $booking->payments()->sum('amountpaid');
        $remainingBalance = $booking->totalAmount - $totalPaid;
        
        return view('admin.bookings.show', compact('booking', 'totalPaid', 'remainingBalance'));
    }

    // Update booking status (approve/reject)
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,paid,Pending,Confirmed,Cancelled,Completed',
            'totalAmount' => 'nullable|numeric|min:0',
        ]);

        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;
        $newStatus = $this->normalizeBookingStatus($request->status) ?? $booking->status;
        $booking->status = $newStatus;
        
        // Update total amount if provided (when approving)
        if ($request->filled('totalAmount')) {
            $booking->totalAmount = $request->totalAmount;
        }
        
        // Restore inventory quantities if booking is being cancelled
        if ($newStatus === 'Cancelled' && $oldStatus !== 'Cancelled') {
            $bookingItems = BookingItem::where('bookingID', $id)->get();
            foreach ($bookingItems as $bookingItem) {
                $inventoryItem = Inventory::find($bookingItem->itemID);
                if ($inventoryItem) {
                    $inventoryItem->quantityAvailable += $bookingItem->quantity;
                    $inventoryItem->save();
                }
            }
        }
        
        $booking->save();

        $message = 'Booking status updated successfully!';
        if ($booking->status === 'Confirmed') {
            $message = 'Booking approved! You can now process payment.';
        } elseif ($booking->status === 'Cancelled') {
            $message = 'Booking cancelled and inventory quantities restored.';
        }

        return redirect()->route('admin.bookings.show', $id)
            ->with('success', $message);
    }

    // Delete booking
    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Restore inventory quantities before deleting
        $bookingItems = BookingItem::where('bookingID', $id)->get();
        foreach ($bookingItems as $bookingItem) {
            $inventoryItem = Inventory::find($bookingItem->itemID);
            if ($inventoryItem) {
                $inventoryItem->quantityAvailable += $bookingItem->quantity;
                $inventoryItem->save();
            }
        }
        
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }

    // List all customers
    public function customersIndex(Request $request)
    {
        $query = \App\Models\Customer::with('bookings');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('phonenumber', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
        }

        $customers = $query->latest()->paginate(20);

        // Calculate statistics
        $totalCustomers = \App\Models\Customer::count();
        $activeBookings = Booking::where('status', '!=', 'Cancelled')->count();
        $pendingPayments = \App\Models\Payment::where('status', '!=', 'completed')->count();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'activeBookings',
            'pendingPayments'
        ));
    }

    // Show single customer
    public function customersShow($id)
    {
        $customer = \App\Models\Customer::with('bookings.payments')->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    // Show inventory create form
    public function inventoryCreate()
    {
        return view('admin.inventory.create');
    }

    // Store new inventory item
    public function inventoryStore(Request $request)
    {
        $request->validate([
            'itemName' => 'required|string|max:100',
            'category' => 'required|in:Tables,Dining Wares,Catering Equipment,Entertainment',
            'quantityAvailable' => 'required|integer|min:0',
            'rentalPrice' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Damaged,Unavailable',
        ]);

        $item = Inventory::create($request->only([
            'itemName',
            'category',
            'quantityAvailable',
            'rentalPrice',
            'status',
        ]));

        return redirect()->route('admin.inventory.show', $item->itemID)
            ->with('success', 'Inventory item created successfully!');
    }

    // List all inventory items
    public function inventoryIndex(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('itemName', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        $items = $query->orderBy('itemName')->paginate(20);

        $totalItems = Inventory::count();
        $availableItems = Inventory::where('status', 'Available')->count();
        $lowStockItems = Inventory::where('quantityAvailable', '<=', 5)->count();
        $unavailableItems = Inventory::where('status', 'Unavailable')->count();

        return view('admin.inventory.index', compact(
            'items',
            'totalItems',
            'availableItems',
            'lowStockItems',
            'unavailableItems'
        ));
    }

    // Show single inventory item
    public function inventoryShow($id)
    {
        $item = Inventory::with('bookingItems.booking.customer')->findOrFail($id);
        $totalRented = $item->bookingItems->sum('quantity');
        $totalRevenue = $item->bookingItems->sum('subtotal');

        return view('admin.inventory.show', compact('item', 'totalRented', 'totalRevenue'));
    }

    // Show inventory edit form
    public function inventoryEdit($id)
    {
        $item = Inventory::findOrFail($id);
        return view('admin.inventory.edit', compact('item'));
    }

    // Update inventory item
    public function inventoryUpdate(Request $request, $id)
    {
        $request->validate([
            'itemName' => 'required|string|max:100',
            'category' => 'required|in:Tables,Dining Wares,Catering Equipment,Entertainment',
            'quantityAvailable' => 'required|integer|min:0',
            'rentalPrice' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Damaged,Unavailable',
        ]);

        $item = Inventory::findOrFail($id);
        $item->update($request->only([
            'itemName',
            'category',
            'quantityAvailable',
            'rentalPrice',
            'status',
        ]));

        return redirect()->route('admin.inventory.show', $item->itemID)
            ->with('success', 'Inventory item updated successfully!');
    }

    // Delete inventory item
    public function inventoryDelete($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully!');
    }

    // Update inventory item status
    public function inventoryUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Available,Damaged,Unavailable',
        ]);

        $item = Inventory::findOrFail($id);
        $item->status = $request->status;
        $item->save();

        $messages = [
            'Available' => 'Item marked as available.',
            'Damaged' => 'Item marked as damaged.',
            'Unavailable' => 'Item marked as out of stock.',
        ];

        return back()->with('success', $messages[$request->status]);
    }

    // Mark item damage with specific quantity
    public function inventoryMarkDamage(Request $request, $id)
    {
        $request->validate([
            'damageQuantity' => 'required|integer|min:1',
        ]);

        $item = Inventory::findOrFail($id);
        $damageQty = (int) $request->damageQuantity;

        // Get the quantity that can be marked as damaged
        $availableForDamage = $item->quantityAvailable - $item->quantityDamaged;

        if ($damageQty > $availableForDamage) {
            return back()->withErrors([
                'damageQuantity' => "Cannot mark {$damageQty} items as damaged. Only {$availableForDamage} items available for damage marking."
            ])->withInput();
        }

        $item->quantityDamaged += $damageQty;
        $item->save();

        return back()->with('success', "{$damageQty} item(s) marked as damaged.");
    }

    // Restore damaged items
    public function inventoryRestoreDamage(Request $request, $id)
    {
        $request->validate([
            'restoreQuantity' => 'required|integer|min:1',
        ]);

        $item = Inventory::findOrFail($id);
        $restoreQty = (int) $request->restoreQuantity;

        if ($restoreQty > $item->quantityDamaged) {
            return back()->withErrors([
                'restoreQuantity' => "Cannot restore {$restoreQty} items. Only {$item->quantityDamaged} damaged item(s) available."
            ])->withInput();
        }

        $item->quantityDamaged -= $restoreQty;
        $item->save();

        return back()->with('success', "{$restoreQty} item(s) restored to available inventory.");
    }

    // Show create booking form
    public function bookingsCreate()
    {
        $customers = \App\Models\Customer::orderBy('fname')->get();
        // Only show items that are Available and have quantity available (not damaged)
        $inventoryItems = Inventory::where('status', 'Available')
                                   ->orderBy('itemName')
                                   ->get()
                                   ->filter(function($item) {
                                       return ($item->quantityAvailable - $item->quantityDamaged) > 0;
                                   })
                                   ->values();

        return view('admin.bookings.create', compact('customers', 'inventoryItems'));
    }

    // Store new booking
    public function bookingsStore(Request $request)
    {
        $request->validate([
            'customerID' => 'required',
            'eventDATE' => 'required|date',
            'timeStart' => 'required|date_format:H:i',
            'timeEND' => 'required|date_format:H:i|after:timeStart',
            'eventLocation' => 'required|string',
            'totalAmount' => 'required|numeric|min:0',
            'status' => 'required|in:Pending,Confirmed,Cancelled,Completed',
            'items' => 'nullable|array',
            'items.*' => 'nullable|integer|min:0',
            // New customer fields
            'fname' => 'required_if:customerID,new|string',
            'lname' => 'required_if:customerID,new|string',
            'phonenumber' => 'required_if:customerID,new|string',
            'address' => 'nullable|string',
        ]);

        // Handle new customer creation
        if ($request->customerID === 'new') {
            $customer = \App\Models\Customer::create([
                'userID' => session('user_id'), // Track which user registered this customer
                'fname' => $request->fname,
                'lname' => $request->lname,
                'phonenumber' => $request->phonenumber,
                'address' => $request->address ?? '',
            ]);
            $customerID = $customer->customerID;
        } else {
            $customerID = $request->customerID;
        }

        // Create the booking
        $booking = Booking::create([
            'customerID' => $customerID,
            'eventDATE' => $request->eventDATE,
            'eventLocation' => $request->eventLocation,
            'timeStart' => $request->timeStart,
            'timeEND' => $request->timeEND,
            'totalAmount' => $request->totalAmount,
            'status' => $request->status,
        ]);

        $items = $request->input('items', []);
        if (!empty($items)) {
            $itemIds = array_keys($items);
            $inventoryLookup = Inventory::whereIn('itemID', $itemIds)->get()->keyBy('itemID');

            // First, validate that all items have sufficient quantity and are not damaged
            foreach ($items as $itemId => $quantity) {
                $qty = (int) $quantity;
                if ($qty <= 0) {
                    continue;
                }

                $inventoryItem = $inventoryLookup->get($itemId);
                if (!$inventoryItem) {
                    continue;
                }

                // Check if item is damaged
                if ($inventoryItem->status === 'Damaged') {
                    return back()->withErrors([
                        'items' => "Cannot book {$inventoryItem->itemName} because it is marked as damaged."
                    ])->withInput();
                }

                // Check if enough quantity is available
                if ($inventoryItem->quantityAvailable < $qty) {
                    return back()->withErrors([
                        'items' => "Insufficient quantity for {$inventoryItem->itemName}. Available: {$inventoryItem->quantityAvailable}, Requested: {$qty}"
                    ])->withInput();
                }
            }

            // Now create booking items and reduce inventory quantities
            foreach ($items as $itemId => $quantity) {
                $qty = (int) $quantity;
                if ($qty <= 0) {
                    continue;
                }

                $inventoryItem = $inventoryLookup->get($itemId);
                if (!$inventoryItem) {
                    continue;
                }

                $subtotal = $inventoryItem->rentalPrice * $qty;

                BookingItem::create([
                    'bookingID' => $booking->bookingID,
                    'itemID' => $inventoryItem->itemID,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);

                // Reduce inventory quantity
                $inventoryItem->quantityAvailable -= $qty;
                $inventoryItem->save();
            }
        }

        return redirect()->route('admin.bookings.show', $booking->bookingID)
            ->with('success', 'Booking created successfully!');
    }

    // Check availability
    public function checkAvailability(Request $request)
    {
        $bookings = null;

        if ($request->filled('date')) {
            $date = $request->date;
            
            $query = Booking::with('customer')
                ->whereDate('eventDATE', $date)
                ->where('status', '!=', 'Cancelled');

            if ($request->filled('start_time') && $request->filled('end_time')) {
                // Additional time-based filtering can be added here if needed
            }

            $bookings = $query->get();
        }

        return view('admin.availability.check', compact('bookings'));
    }

    private function normalizeBookingStatus(string $status): ?string
    {
        $value = strtolower($status);

        return match ($value) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'completed', 'paid' => 'Completed',
            default => null,
        };
    }
}
