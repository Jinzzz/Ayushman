<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Consultation_Booking_Invoice extends Model
{
    use HasFactory;
    protected $table ='trn_consultation_booking_invoices';
    protected $fillable = [
        'booking_id',
        'branch_id',
        'booking_invoice_number',
        'invoice_date',
        'booking_date',
        'paid_amount',
        'payment_type',
        'deposit_to',
        'amount',
        'reference_no',
        'is_paid',
        'created_by',
    ];
}
