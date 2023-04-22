<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaMaster extends Model
{
    use HasFactory;
    protected $table = "tbl_area_master";
    protected $primaryKey = "id";
    public $timestamps = false;

    // protected $appends = ['surrounding_areas'];

    // public function parent() {
    //     return $this->belongsTo(AreaMaster::class)->whereIn('id', $this->surrounding_area_id);
    // }

    public function getSurroundingAreasAttribute()
    {
        $areas = AreaMaster::whereIn('id', [1])->get();
        dd($areas->toArray());
        return AreaMaster::whereIn('id', [1])->get();
    }
}