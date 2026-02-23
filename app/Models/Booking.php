<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'bookingID';

    protected $fillable = [
        'customerID',
        'eventdate',
        'eventtime',
        'eventdetails',
        'status',
        'totalamount',
    ];

    protected $casts = [
        'eventdate' => 'date',
        'totalamount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');
    }

    /**
     * Get the payments for the booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'bookingID', 'bookingID');
    }
}
