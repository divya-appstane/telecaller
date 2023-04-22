<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LMGEmployee;

class Designation extends Model
{
    use HasFactory;
    protected $table = "tbldesignation";
    protected $primaryKey = "designation_id";
    public $timestamps = false;

    public function getDesignationWiseEmp() {
        return $this->hasMany(LMGEmployee::class, 'designation', 'designation_id')->whereIn('tblemployee.offinfo', [0,2])->orderBy('tblemployee.offinfo', 'DESC');
    }

    public function getDesignationWiseActiveEmp() {
        return $this->hasMany(LMGEmployee::class, 'designation', 'designation_id')->where('tblemployee.offinfo', 2)->orderBy('tblemployee.offinfo', 'DESC');
    }

    public function rolePermissions() {
        return $this->hasMany('App\Models\RolePermission');
    }

    

}
