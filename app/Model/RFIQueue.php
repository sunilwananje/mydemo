<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RFIQueue extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table='rfi_queue';
}
