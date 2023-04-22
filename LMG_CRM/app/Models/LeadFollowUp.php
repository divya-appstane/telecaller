<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFollowUp extends Model
{
    use HasFactory;
    protected $table = "tbl_lead_followup";
    protected $primaryKey = "followup_id";

    public $timestamps = false;

    public function getLeadPreviousStatus()
    {
        return $this->belongsTo(LeadsStatus::class, 'previous_status', 'id')->select('id','status_name');
    }

    public function getLeadCurrentStatus()
    {
        return $this->belongsTo(LeadsStatus::class, 'current_status', 'id')->select('id','status_name');
    }
}
