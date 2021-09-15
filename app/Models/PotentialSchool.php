<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PotentialSchool extends Model
{
    //use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];
   
    public function lga() {
        return $this->belongsTo(LocalGovernmentArea::class, 'lga_id', 'id');
    }

   
    public function getFolderKey($id)
    {
        $folder_key = $this->findOrFail($id);
        return $folder_key->folder_key;
    }

    public function registerSchool($request)
    {
        
         
    }

}
