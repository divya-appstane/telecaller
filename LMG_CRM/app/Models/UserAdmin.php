<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasPermissionsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAdmin extends Authenticatable 
{
    use HasFactory, HasPermissionsTrait;
    protected $table = "tbladmin";
    protected $primaryKey = "ad_id";


    public $timestamps = false;
}
