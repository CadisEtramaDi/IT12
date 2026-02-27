<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Show all payments
    public function index()
    {
        $payments = Payment::with('booking.customer')
            ->orderBy('paymentdate', 'desc')
            ->paginate(20);
        
        return view('admin.payments.index', compact('payments'));
    }

    // Show payment form for a booking
    public function create($bookingId)
    {
        $booking = Booking::with('customer')->findOrFail($bookingId);
        
        // Check if booking is approved
        if (strtolower($booking->status) !== 'confirmed') {
            return redirect()->back()->with('error', 'Booking must be approved before payment.');
        }
        
        // Calculate remaining balance
        $totalPaid = $booking->payments()->sum('amountpaid');
        $remainingBalance = $booking->totalAmount - $totalPaid;
        
        return view('admin.payments.create', compact('booking', 'totalPaid', 'remainingBalance'));
    }

    // Store payment
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        $validated = $request->validate([
            'amountpaid' => 'required|numeric|min:0|max:' . $booking->totalAmount,
            'paymentdate' => 'required|date',
            'paymentmethod' => 'required|in:cash,gcash',
            'status' => 'required|in:completed,pending,failed',
        ]);

        $validated['bookingID'] = $bookingId;

        Payment::create($validated);

        // Check if booking is fully paid
        $totalPaid = $booking->payments()->sum('amountpaid');
        if ($totalPaid >= $booking->totalAmount) {
            $booking->update(['status' => 'Completed']);
        }

        return redirect()->route('admin.bookings.show', $bookingId)
            ->with('success', 'Payment recorded successfully!');
    }

    // Show payment details
    public function show($id)
    {
        $payment = Payment::with('booking.customer')->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    // Generate sales report
    public function salesReport(Request $request)
    {
        $query = Payment::with('booking.customer')
            ->where('status', 'completed');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('paymentdate', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('paymentdate', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('paymentmethod', $request->payment_method);
        }

        $payments = $query->orderBy('paymentdate', 'desc')->get();
        
        $totalSales = $payments->sum('amountpaid');
        $totalTransactions = $payments->count();

        // Group by payment method
        $salesByMethod = $payments->groupBy('paymentmethod')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amountpaid')
            ];
        });

        return view('admin.reports.sales', compact(
            'payments',
            'totalSales',
            'totalTransactions',
            'salesByMethod'
        ));
    }
}
