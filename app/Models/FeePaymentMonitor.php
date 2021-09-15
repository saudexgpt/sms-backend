<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePaymentMonitor extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() {
        return $this->belongsTo(School::class);
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

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function level() {
        return $this->belongsTo(Level::class);
    }

    public function schoolFeePayment() 
    {
        return $this->hasMany(SchoolFeePayment::class);
    }

    public function paymentMonitorData( $school_id, $term_id, $sess_id, $student,$school_fee)
    {
        $payment_data['school_id'] = $school_id;
        $payment_data['student'] = $student;//$this->getStudent();
        
        $payment_data['school_fee'] = $school_fee->fee;

        
        //$payment_data['accommodation_fee_id'] = NULL;
        $payment_data['discount'] = 0;
        $payment_data['amount_due'] = 0;//$amount_due;

        $payment_data['amount_paid'] = 0;
        $payment_data['balance'] = 0;
        $payment_data['sess_id'] = $sess_id;//$this->getSession()->id;
        $payment_data['term_id'] = $term_id;//$this->getSemester()->id;
        

        return $payment_data;
    }
    /**
    *This creates a new entry for payment monitor if entry does not exit
    * @param array $data. This contains elements to be entered in the db
    * @return boolean True if successful and False if failed
    */
    public function createPaymentMonitor($data, $level_id, $action="insert")
    {
      
        $amount_paid = $data['amount_paid'];
        
        $school_id = $data['school_id'];
        $sess_id = $data['sess_id'];
        //$semester_id = $data['semester_id'];
        $term_id = $data['term_id'];
        
        $student = $data['student'];

        $student_id = $student->id;

        $student_id = $student->id;
        $discount = $data['discount'];
        
        $total_fee = $data['school_fee'];// + $debt; 
        
        $amount_due = $total_fee - $discount;

        
        $balance = $amount_due - $amount_paid;

        
        $payment_monitor = FeePaymentMonitor::where(['school_id'=>$school_id,'student_id'=>$student_id, 'sess_id'=>$sess_id, 'term_id'=>$term_id])->first();

        if(!$payment_monitor){
        //update
            $payment_monitor = new FeePaymentMonitor();
            
        }else{
            return true;
        }
        $payment_monitor->school_id = $school_id;
        $payment_monitor->student_id = $student_id;
        $payment_monitor->total_fee = $total_fee;
        $payment_monitor->amount_due = $amount_due;
        $payment_monitor->amount_paid = $amount_paid;
        $payment_monitor->balance = $balance;
        $payment_monitor->sess_id = $sess_id;
        $payment_monitor->term_id = $term_id;
        $payment_monitor->level_id = $level_id;
        $payment_monitor->discount = $discount;


        if($payment_monitor->save()){
            return true;
        }
        return false;
        
    }

    /**
    *This updates the payment monitor table for all school fees paid
    *@param Array $successful_payment_logs this hold value of all successful payments
    *@param $student_id = student id of the concerned student
    *@return payment status for the selection
    */
    public function updatePaymentMonitor($fee_payment_monitor)
    {
        if ($fee_payment_monitor) {
            //check for payments made for this session and term and update the payment monitor table
           
            $paid_fees = SchoolFeePayment::where(['fee_payment_monitor_id'=>$fee_payment_monitor->id, 'remitted'=>'0'])->get();

            if ($paid_fees) {
                
                foreach ($paid_fees as $paid_fee) {
                    $amount_paid = $paid_fee->amount;

                    $fee_payment_monitor->amount_paid +=  $amount_paid;
        
                    $fee_payment_monitor->balance -=  $amount_paid;

                    if($fee_payment_monitor->save()){

                        $paid_fee->remitted = '1';
                        $paid_fee->save();

                        
                    }

                }
            }
            
        }
        return $fee_payment_monitor;
        
    }
}
