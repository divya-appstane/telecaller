<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionAction extends Model
{
    use HasFactory;
    protected $table = "permission_actions";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function permissionActions() {
        return $this->hasMany('App\Models\Permission','action_id');
    }
}
