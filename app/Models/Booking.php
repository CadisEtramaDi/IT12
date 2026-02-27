<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'bookingID';

    protected $fillable = [
        'customerID',
        'eventDATE',
        'eventLocation',
        'timeStart',
        'timeEND',
        'status',
        'totalAmount',
    ];

    protected $casts = [
        'eventDATE' => 'date',
        'totalAmount' => 'decimal:2',
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

    /**
     * Get the booking items for this booking.
     */
    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class, 'bookingID', 'bookingID');
    }

    /**
     * Get all inventory items for this booking.
     */
    public function items()
    {
        return $this->belongsToMany(Inventory::class, 'booking_items', 'bookingID', 'itemID')
                    ->withPivot('quantity', 'subtotal')
                    ->withTimestamps();
    }
}
