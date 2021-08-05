<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumLevel extends Model
{
    public function curriculumSetup()
    {
        return $this->belongsTo(CurriculumSetup::class, 'curriculum_setup_id', 'id');
    }
}
