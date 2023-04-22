<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeadsStatus;


class UploadLeads extends Model
{
    use HasFactory;
    protected $table = 'tbl_upload_leads';
    protected $primaryKey = 'id';

    public $timestamps = false;

    public function getLeadStatus()
    {
        return $this->belongsTo(LeadsStatus::class, 'lead_status', 'id')->select('id','status_name');
    }

    public function getLeadCategory()
    {
        return $this->hasMany(LeadsCategory::class, 'upload_lead_id')->with('getCategoryDetails');
    }

    
}
