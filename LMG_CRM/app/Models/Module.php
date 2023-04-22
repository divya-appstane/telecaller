<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = "module";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function modulePermissions() {
        return $this->hasMany('App\Models\Permission');
    }

}
