<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $table = 'revenue';
    protected $primaryKey = 'revenueID';

    protected $fillable = [
        'reportDate',
        'totalBookings',
        'grossRevenue',
        'reportType',
    ];

    protected $casts = [
        'reportDate' => 'date',
        'grossRevenue' => 'decimal:2',
    ];

    /**
     * Get the payments included in this revenue report.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'revenueID', 'revenueID');
    }
}
