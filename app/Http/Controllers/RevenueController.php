<?php

namespace App\Http\Controllers;

use App\Models\Revenue;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * Display revenue reports.
     */
    public function index(Request $request)
    {
        $query = Revenue::query();

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('reportDate', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('reportDate', '<=', $request->end_date);
        }

        // Filter by report type
        if ($request->filled('report_type')) {
            $query->where('reportType', $request->report_type);
        }

        $revenues = $query->latest('reportDate')->paginate(20);
        
        // Calculate summary statistics
        $totalRevenue = Revenue::sum('grossRevenue');
        $monthlyRevenue = Revenue::whereMonth('reportDate', now()->month)
                                 ->whereYear('reportDate', now()->year)
                                 ->sum('grossRevenue');
        $totalReports = Revenue::count();

        return view('admin.revenue.index', compact('revenues', 'totalRevenue', 'monthlyRevenue', 'totalReports'));
    }

    /**
     * Show single revenue report.
     */
    public function show($id)
    {
        $revenue = Revenue::with('payments.booking.customer')->findOrFail($id);
        
        return view('admin.revenue.show', compact('revenue'));
    }

    /**
     * Generate daily revenue report for a specific date.
     */
    public function generateDaily(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
        ]);

        $reportDate = Carbon::parse($request->report_date);

        // Check if report already exists
        $existingReport = Revenue::whereDate('reportDate', $reportDate)->where('reportType', 'Daily')->first();
        
        if ($existingReport) {
            return back()->with('info', 'Revenue report for this date already exists.');
        }

        // Get all completed payments for this date
        $payments = Payment::where('status', 'completed')
                          ->whereDate('paymentdate', $reportDate)
                          ->get();

        if ($payments->isEmpty()) {
            return back()->with('info', 'No completed payments found for this date.');
        }

        // Calculate totals
        $grossRevenue = $payments->sum('amountpaid');
        $totalBookings = $payments->pluck('bookingID')->unique()->count();

        // Create revenue report
        $revenue = Revenue::create([
            'reportDate' => $reportDate,
            'totalBookings' => $totalBookings,
            'grossRevenue' => $grossRevenue,
            'reportType' => 'Daily',
        ]);

        // Associate payments with this revenue report
        Payment::where('status', 'completed')
               ->whereDate('paymentdate', $reportDate)
               ->update(['revenueID' => $revenue->revenueID]);

        return redirect()->route('admin.revenue.show', $revenue->revenueID)
                        ->with('success', 'Daily revenue report generated successfully!');
    }

    /**
     * Generate monthly revenue report.
     */
    public function generateMonthly(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = Carbon::parse($request->month . '-01');
        $reportDate = $month->endOfMonth();

        // Check if report already exists
        $existingReport = Revenue::whereYear('reportDate', $month->year)
                                 ->whereMonth('reportDate', $month->month)
                                 ->where('reportType', 'Monthly')
                                 ->first();
        
        if ($existingReport) {
            return back()->with('info', 'Monthly revenue report already exists.');
        }

        // Get all completed payments for this month
        $payments = Payment::where('status', 'completed')
                          ->whereYear('paymentdate', $month->year)
                          ->whereMonth('paymentdate', $month->month)
                          ->get();

        if ($payments->isEmpty()) {
            return back()->with('info', 'No completed payments found for this month.');
        }

        // Calculate totals
        $grossRevenue = $payments->sum('amountpaid');
        $totalBookings = $payments->pluck('bookingID')->unique()->count();

        // Create revenue report
        $revenue = Revenue::create([
            'reportDate' => $reportDate,
            'totalBookings' => $totalBookings,
            'grossRevenue' => $grossRevenue,
            'reportType' => 'Monthly',
        ]);

        // Associate payments with this revenue report
        Payment::where('status', 'completed')
               ->whereYear('paymentdate', $month->year)
               ->whereMonth('paymentdate', $month->month)
               ->update(['revenueID' => $revenue->revenueID]);

        return redirect()->route('admin.revenue.show', $revenue->revenueID)
                        ->with('success', 'Monthly revenue report generated successfully!');
    }

    /**
     * Auto-aggregate payments into daily revenue reports.
     * This can be run as a scheduled task.
     */
    public function autoAggregate()
    {
        // Get all dates with completed payments that don't have revenue reports yet
        $datesWithPayments = Payment::where('status', 'completed')
                                   ->whereNull('revenueID')
                                   ->selectRaw('DATE(paymentdate) as payment_date')
                                   ->groupBy('payment_date')
                                   ->get();

        $generatedCount = 0;

        foreach ($datesWithPayments as $dateRecord) {
            $reportDate = Carbon::parse($dateRecord->payment_date);

            // Get payments for this date
            $payments = Payment::where('status', 'completed')
                              ->whereDate('paymentdate', $reportDate)
                              ->whereNull('revenueID')
                              ->get();

            if ($payments->isEmpty()) {
                continue;
            }

            // Calculate totals
            $grossRevenue = $payments->sum('amountpaid');
            $totalBookings = $payments->pluck('bookingID')->unique()->count();

            // Create revenue report
            $revenue = Revenue::create([
                'reportDate' => $reportDate,
                'totalBookings' => $totalBookings,
                'grossRevenue' => $grossRevenue,
                'reportType' => 'Daily',
            ]);

            // Associate payments with this revenue report
            Payment::where('status', 'completed')
                   ->whereDate('paymentdate', $reportDate)
                   ->whereNull('revenueID')
                   ->update(['revenueID' => $revenue->revenueID]);

            $generatedCount++;
        }

        return back()->with('success', "Generated {$generatedCount} revenue report(s) successfully!");
    }
}
