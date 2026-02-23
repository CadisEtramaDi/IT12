<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return view('bookings.create');
    }

    public function myBookings()
    {
        // For now, get all bookings. In a real app with authentication,
        // you would filter by the logged-in user
        $bookings = Booking::with('customer')->orderBy('created_at', 'desc')->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    public function checkAvailability(Request $request)
    {
        $bookings = collect();
        
        if ($request->has('date')) {
            $bookings = Booking::with('customer')
                ->where('eventdate', $request->date)
                ->orderBy('eventtime', 'asc')
                ->get();
        }
        
        return view('availability.check', compact('bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required',
            'event_details' => 'required|string|max:1000',
        ]);

        // Create customer first
        $customer = Customer::create([
            'fname' => $validated['first_name'],
            'lname' => $validated['last_name'],
            'phonenumber' => $validated['contact_number'],
            'address' => $validated['address'],
        ]);

        // Create booking linked to customer
        Booking::create([
            'customerID' => $customer->customerID,
            'eventdate' => $validated['event_date'],
            'eventtime' => $validated['event_time'],
            'eventdetails' => $validated['event_details'],
            'status' => 'pending',
            'totalamount' => 0.00, // Will be set by admin when approving
        ]);

        return redirect()->route('bookings.create')->with('success', 'Booking created successfully!');
    }
}
