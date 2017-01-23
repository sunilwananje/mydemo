<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCodeDb extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'partner_code_db';
}
