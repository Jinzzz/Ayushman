<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trn_Billing_Invoice extends Model
{
    use HasFactory;
    protected $table = 'trn_billing_invoices';
    protected $fillable = [
        'booking_id',
        'patient_id',
        'booking_invoice_number',
        'invoice_date',
        'booking_date',
        'patient_name',
        'patient_contact',
        'paid_amount',
        'due_amount',
        'is_paid',
        'created_by'
    ];
}
