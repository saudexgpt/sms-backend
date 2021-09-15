<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Event;
use App\News;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(News $news, Event $event)
    {
        $school_id = $this->getSchool()->id;
        $events = $event->fetchEvent($school_id);
        $school_id = $this->getSchool()->id;
        $news = $news->where('school_id', $school_id)->orderBy('id', 'DESC')->get();
        //$events = $events->where('school_id', $school_id)->orderBy('id','DESC')->get();
        return $this->render('core::events.index', compact('news', 'events'));
    }
    public function events()
    {
        return $this->render('core::events.calendar');
    }

    public function addEvent(Request $request)
    {
        $school_id = $this->getSchool()->id;
        $event = Event::where(['school_id' => $school_id, 'title' => $request->title, 'start' => $request->start_date])->first();


        if (!$event) {
            $event = new Event();
        }
        $event->title = $request->title;
        $event->description = str_replace("'", "`", $request->description);
        $event->targeted_audience = implode('~', $request->targeted_audience);
        $event->start = date('Y-m-d', strtotime($request->start_date));
        $event->end = date('Y-m-d', strtotime($request->end_date));

        if ($event->start < todayDate()) {
            return response()->json([
                'message' => 'wrong_start_date',
                'url' => ''
            ]);
        }
        if ($event->start > $event->end) {
            return response()->json([
                'message' => 'wrong_end_date',
                'url' => ''
            ]);
        }

        $event->school_id = $school_id;
        $event->save();

        $event_details = $event->fetchEvent($school_id, $event->id);

        return $events = $event_details->getData()->events;
    }
    public function upcomingEvents(Event $events)
    {
        $start_of_month = date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
        $user = $this->getUser();
        $school_id = $this->getSchool()->id;
        $notifications = Event::where('school_id', $school_id)
            ->where('end', '>=', $start_of_month)
            ->where(function ($q) use ($user) {
                return $q->where('targeted_audience', 'like', $user->role)
                    ->orWhere('targeted_audience', 'like', $user->role . '~%')
                    ->orWhere('targeted_audience', 'like', '%~' . $user->role . '~%')
                    ->orWhere('targeted_audience', 'like', '%~' . $user->role);
            })
            ->orderBy('id', 'DESC')->get();
        if ($notifications != '[]') {
            foreach ($notifications as $notification) {

                $seen_by_array  = explode('~', $notification->seen_by);

                $notification->seen_by_array = $seen_by_array;
            }
        }
        return $this->render('core::events.upcoming_events', compact('notifications', 'user'));
    }
    public function deleteEvent($id)
    {
        $school_id = $this->getSchool()->id;
        $event = Event::find($id);
        $event->delete();
        return $routines = $event->fetchEvent($school_id);
    }

    public function updateEvent()
    {
        $inputs = request()->all();
        $id = $inputs['id'];
        //$day = date('l', strtotime($inputs['day']));
        $start = $inputs['start'];
        $end = $inputs['end'];

        //$day = schoolDaysStr($day);//from helpers fetch day number from day str


        $event = Event::find($id);
        $event->update([
            'start' => $start,
            'end'   => $end,
            //'day'   => $day
        ]);

        /*$class_teacher_id =  $routine->class_teacher_id;
        return $routines = $this->fetchRoutine($class_teacher_id);*/
    }

    public function eventCalendar(Event $event_obj)
    {
        $school_id = $this->getSchool()->id;
        $events = $event_obj->fetchEvent($school_id);

        return $this->render('core::events.calendar', compact('events'));
    }

    public function show(Event $event, $id, Request $request)
    {
        //
        $reader =  $request->reader;
        $notification = $event->find($id);

        $readers = $notification->seen_by;

        if ($readers != NULL && $readers != "") {
            $uniq_readers = explode('~', $readers . '~' . $reader);
            $uniq_readers = implode('~', array_unique($uniq_readers));
            $notification->seen_by = $uniq_readers;
        } else {
            $notification->seen_by = $reader;
        }
        $notification->save();
        return $this->render('core::events.show', compact('notification'));
    }
}
