<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class oder extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['customerID','shippingID','order_status','order_code'];
    protected $table = 'oder';
    protected $primaryKey = 'orderID';
}
