<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRMFeedbackMaster extends Model
{
    use HasFactory;
    protected $table = "crm_feedback_master";
    protected $primaryKey = "feedback_id ";
    public $timestamps = false;
}
