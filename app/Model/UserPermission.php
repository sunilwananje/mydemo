<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Model\Permission;
use Validator,Schema;

class UserPermission extends Model
{
	protected $table = 'mst_permission_users';
    public $timestamps  = false;
}