<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

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

        // Simple authentication (you can enhance this with database users)
        if ($request->username === 'admin' && $request->password === 'admin123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
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
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $paidBookings = Booking::where('status', 'paid')->count();
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
            $query->where('status', $request->status);
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
        $remainingBalance = $booking->totalamount - $totalPaid;
        
        return view('admin.bookings.show', compact('booking', 'totalPaid', 'remainingBalance'));
    }

    // Update booking status (approve/reject)
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,paid',
            'totalamount' => 'nullable|numeric|min:0',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        
        // Update total amount if provided (when approving)
        if ($request->filled('totalamount')) {
            $booking->totalamount = $request->totalamount;
        }
        
        $booking->save();

        $message = 'Booking status updated successfully!';
        if ($request->status === 'confirmed') {
            $message = 'Booking approved! You can now process payment.';
        }

        return redirect()->route('admin.bookings.show', $id)
            ->with('success', $message);
    }

    // Delete booking
    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }
}
