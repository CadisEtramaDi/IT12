<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $table = 'booking_items';
    protected $primaryKey = 'bookingItemID';

    protected $fillable = [
        'bookingID',
        'itemID',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the booking that owns this booking item.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingID', 'bookingID');
    }

    /**
     * Get the inventory item for this booking item.
     */
    public function item()
    {
        return $this->belongsTo(Inventory::class, 'itemID', 'itemID');
    }
}
