<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Therapy_Booking_Invoice_Payment extends Model
{
        use HasFactory;
    protected $table ='trn__therapy__booking__invoice__payments';
    protected $fillable = [
        'therapy_booking_invoice_id',
        'paid_amount',
        'payment_mode',
        'deposit_to',
        'reference_no',
    ];
}
