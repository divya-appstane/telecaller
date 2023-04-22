<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpFeedback extends Model
{
    use HasFactory;
    protected $table = "tbl_followup_feedbacks";
    protected $primaryKey = "id";
    public $timestamps = false;
}
