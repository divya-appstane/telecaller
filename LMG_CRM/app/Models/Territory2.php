<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Territory2 extends Model
{
    use HasFactory;
    protected $table = "tblterritory2";
    protected $primaryKey = "tid";

    public $timestamps = false;


    /**
     * Get the user that owns the Territory2
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getEmployeeDetails()
    {
        return $this->hasOne(LMGEmployee::class, 'empusrid', 'agentcode');
    }
}
