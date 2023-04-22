<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use HasFactory;
    protected $table = "tbl_module_permissions";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
