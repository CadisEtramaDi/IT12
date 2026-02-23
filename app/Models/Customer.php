<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customerID';

    protected $fillable = [
        'fname',
        'lname',
        'phonenumber',
        'address',
    ];

    /**
     * Get the bookings for the customer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customerID', 'customerID');
    }
}
