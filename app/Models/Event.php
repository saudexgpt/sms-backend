<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'school_id',
        'title',
        'description',
        'start',
        'end'
    ];
    //
    public function fetchEvent($school_id, $id = null)
    {
        $start_of_month = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $data = Event::where('school_id', $school_id)->where('end', '>=', $start_of_month)->get();
        if ($id) {
            $data = Event::where(['id' => $id, 'school_id' => $school_id])->where('end', '>=', $start_of_month)->get();
        }


        $events = [];
        foreach ($data as $record) {
            array_push($events, array(
                'id' => $record->id,
                'title' => $record->title,
                'description' => $record->description,
                // 'url' => '/routine/update/',
                'start' => $record->start,
                'end' => $record->end,
                //'dow' => $record->day
            ));
        }
        return response()->json([
            // 'message' => 'Routine created successfully',
            'events' => $events
        ]);
    }
}
