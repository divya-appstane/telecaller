<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    protected $table = "tbldesignation_permissions";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
    
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}

