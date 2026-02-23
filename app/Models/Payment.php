<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'paymentID';

    protected $fillable = [
        'bookingID',
        'amountpaid',
        'paymentdate',
        'paymentmethod',
        'status',
    ];

    protected $casts = [
        'paymentdate' => 'date',
        'amountpaid' => 'decimal:2',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingID', 'bookingID');
    }
}
