<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumSetup extends Model
{
    public function curriculumLevels()
    {
        return $this->hasMany(CurriculumLevel::class, 'curriculum_setup_id', 'id');
    }
}
