<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_Setting extends Model
{
    use HasFactory;

    protected $table = 'mst__settings';
    protected $fillable = [
        'company_logo',
        'company_name',
        'company_address',
        'company_location',
        'company_email',
        'contact_number_1',
        'contact_number_2',
        'gst_number',
        'company_website_link'
    ];

}
