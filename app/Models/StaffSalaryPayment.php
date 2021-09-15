<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSalaryPayment extends Model
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
    public function staff() {
        return $this->belongsTo(Staff::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountant() {
        return $this->belongsTo(Staff::class, 'recorded_by', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staffLevel() {
        return $this->belongsTo(StaffLevel::class);
    }

}
