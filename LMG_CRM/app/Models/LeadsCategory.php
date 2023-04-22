<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadsCategory extends Model
{
    use HasFactory;
    protected $table = 'tbl_leads_cat';
    protected $primaryKey = 'lid';
    public $timestamps = false;

    public function getCategoryDetails() {
        return $this->belongsTo(Category::class, 'cat_id', 'cid');
    }
}
