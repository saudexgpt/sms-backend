<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentsOfferingSubject extends Model
{
    //
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() {
        return $this->belongsTo(School::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectTeacher() {
        return $this->belongsTo(ClassTeacher::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session() {
        return $this->belongsTo(SSession::class, 'sess_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term() {
        return $this->belongsTo(Term::class);
    }

}
