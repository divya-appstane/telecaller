<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Territory2;
use App\Traits\HasPermissionsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LMGEmployee extends Authenticatable 
{
    use HasFactory, HasPermissionsTrait;
    protected $table = "tblemployee";
    protected $primaryKey = "emp_id";

    protected $guard = "front";

    public $timestamps = false;

    public function getEmployeeTerritory() {
        return $this->hasMany(Territory2::class, 'agentcode', 'empusrid');
    }
}
