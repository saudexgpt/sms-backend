<?php

namespace Modules\Core\Http\Controllers;

use App\ClassTeacher;
use App\Http\Requests\MessageRequest;
use App\Message;
use App\Student;
use App\Staff;
use App\Guardian;
use App\GuardianStudent;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use App\StudentsInClass;
use App\SubjectTeacher;

class MessagesController extends Controller
{

    public function index()
    {
        return $this->inbox();
    }

    public function navbarNotification()
    {
        $user_id = $this->getUser()->id;
        $messages = Message::where(function ($query) use ($user_id) {
            return $query->where('recipient', $user_id)
                ->orWhere('copied_to', 'LIKE', '%~' . $user_id . '~%');
        })->orderBy('created_at', 'DESC')->get();



        $count = $messages->count();
        if (!$messages->isEmpty()) {
            foreach ($messages as $message) {
                if ($message->original_message_id != NULL) {
                    $message->original_message = Message::find($message->original_message_id);
                }
                $read_by_array = explode('~', $message->read_by);
                $message->read = 'not_read';
                if (in_array($user_id, $read_by_array)) {
                    $message->read = 'read';
                    $count--;
                }
            }
        }

        return view('core::messages.navbar_notification', compact('messages', 'user_id', 'count'));
    }

    public function extraOptions()
    {
        $user = $this->getUser();
        $options = array(
            '' => 'Select Category', 'student' => 'Student', 'parent' => 'Parent', 'staff' => 'School Staff'
        );
        if ($user->hasRole('parent')) {
            $options = array(
                '' => 'Select Category',  'staff' => 'School Staff'
            );
        }
        $students = Student::where('school_id', $this->getSchool()->id)
            ->where('user_id', '!=', $user->id)->get();

        $recipients = [];
        foreach ($students as $student) :
            $recipients[$student->user->id] = $student->user->first_name . ' ' . $student->user->last_name . ' (' . $student->user->username . ')';
        endforeach;

        return array($options, $recipients);
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function inbox()
    {
        $user_id = $this->getUser()->id;
        $messages = Message::where(function ($query) use ($user_id) {
            return $query->where('recipient', '=', $user_id)
                ->orWhere('copied_to', 'LIKE', '%~' . $user_id . '~%');
        })->where(function ($query) use ($user_id) {
            return $query->where('recipient_delete', 'NOT LIKE', '%~' . $user_id . '~%')
                ->orWhere('recipient_delete', '=', NULL);
        })->orderBy('created_at', 'DESC')->get();
        foreach ($messages as $message) {
            if ($message->original_message_id != NULL) {
                $message->original_message = Message::find($message->original_message_id);
            }
        }

        //echo $messages;exit;
        list($options, $recipients) = $this->extraOptions();

        $type = 'Inbox';
        if (request()->ajax()) {

            return $this->render('core::messages.message', compact('messages', 'options', 'recipients', 'type'));
        }
        return $this->render('core::messages.index', compact('messages', 'options', 'recipients', 'type'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sent()
    {
        $messages = Message::where('sender', $this->getUser()->id)
            ->where('sender_delete', '=', NULL)
            ->orderBy('created_at', 'DESC')->get();


        list($options, $recipients) = $this->extraOptions();
        $type = 'Sent Messages';
        return $this->render('core::messages.message', compact('messages', 'options', 'recipients', 'type'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user = $this->getUser();
        $options = array(
            '' => 'Select Category', 'student' => 'Student', 'parent' => 'Parent', 'staff' => 'School Staff'
        );
        if ($user->hasRole('parent')) {
            $options = array(
                '' => 'Select Category',  'staff' => 'School Staff'
            );
        }

        $students = Student::where('school_id', $this->getSchool()->id)
            ->where('user_id', '!=', $user->id)->get();

        $recipients = [];
        foreach ($students as $student) :
            $recipients[$student->user->id] = $student->user->username . ' (' . $student->user->first_name . ' ' . $student->user->last_name . ')';
        endforeach;

        //print_r($recipient);exit;
        return (string) $this->render('core::messages.create', compact('options', 'recipients'));
    }

    /**
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $message = new Message();
        $recipients = $request->recipients;
        $count = count($recipients);
        $recipient = $recipients[0];
        if ($recipient == "All") {
            $recipient = $recipients[1];
        } else {
            $recipient = $recipients[0];
        }
        if ($count > 1) {
            //removing the first element since this is the main recipient
            if ($recipient == "All") {
                unset($recipients[1]);
            } else {
                unset($recipients[0]);
            }


            //convert remaining recipients to string
            $copied_to = implode('~', $recipients);

            //tie it to the $message instances
            $message->copied_to = '~' . $copied_to . '~';
        }
        $message->sender = $this->getUser()->id;

        $message->recipient = $recipient;

        $message->subject = $request->subject;
        $message->message = $request->message;

        if ($message->save()) {
            return (string) 'success';
        }
        return (string) 'failed';
    }


    /**
     * Method to get recipients depending on the category option selected
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipients(Request $request)
    {
        //$request->option = "staff";
        $school_id = $this->getSchool()->id;
        $user = $this->getUser();
        $user_id = $user->id;
        switch ($request->option) {
            case 'student':
                $recipients = Student::where('school_id', $school_id)
                    ->where('user_id', '!=', $user_id)->get();
                if ($user->hasRole('teacher')) {
                    $recipients = [];
                    $teacher = $this->getStaff();

                    $student_ids = '';
                    $subject_teachers = SubjectTeacher::where(['teacher_id' => $teacher->id, 'school_id' => $school_id])->get();
                    if ($subject_teachers->isNotEmpty()) {
                        foreach ($subject_teachers as $subject_teacher) {
                            $students_in_class =  StudentsInClass::where([
                                'school_id' => $school_id,
                                'sess_id' => $this->getSession()->id,
                                'class_teacher_id' => $subject_teacher->class_teacher_id,
                            ])->first();

                            $student_ids .= '~' . $students_in_class->student_ids;
                        }
                    }


                    $class_teachers = ClassTeacher::where(['teacher_id' => $teacher->id, 'school_id' => $school_id])->get();
                    if ($class_teachers->isNotEmpty()) {
                        foreach ($class_teachers as $class_teacher) {
                            $students_in_class =  StudentsInClass::where([
                                'school_id' => $school_id,
                                'sess_id' => $this->getSession()->id,
                                'class_teacher_id' => $class_teacher->id,
                            ])->first();

                            $student_ids .= '~' . $students_in_class->student_ids;
                        }
                    }

                    $student_ids = array_unique(explode('~', substr($student_ids, 1)));

                    foreach ($student_ids as $student_id) {
                        $recipients[] = Student::find($student_id);
                    }
                }
                if ($user->hasRole('student')) {

                    $student_id = $this->getStudent()->id;
                    $recipients = [];

                    $student_in_class_obj = new StudentsInClass();

                    $student_in_class = $student_in_class_obj->fetchStudentInClass($student_id, $this->getSession()->id, $this->getTerm()->id, $school_id);
                    $student_ids = $students_in_class->student_ids;
                    $student_ids = explode('~', $student_ids);
                    foreach ($student_ids as $class_student_id) {
                        if ($student_id != $class_student_id) {
                            $recipients[] = Student::find($class_student_id);
                        }
                    }
                }

                break;
            case 'parent':
                if ($user->hasRole('admin')) {
                    $recipients = Guardian::where('school_id', $school_id)
                        ->where('guardians.user_id', '!=', $user_id)->get();
                    break;
                }
                if ($user->hasRole('student')) {

                    $student_id = $this->getStudent()->id;
                    $recipients = [];

                    $gurdian_student = GuardianStudent::where('student_id', $student_id)->first();
                    $recipients[] = $gurdian_student->guardian;
                    break;
                }
                if ($user->hasRole('teacher')) {
                    $recipients = [];
                    $teacher = $this->getStaff();
                    $student_ids = '';
                    $subject_teachers = SubjectTeacher::where(['teacher_id' => $teacher->id, 'school_id' => $school_id])->get();
                    if ($subject_teachers->isNotEmpty()) {
                        foreach ($subject_teachers as $subject_teacher) {
                            $students_in_class =  StudentsInClass::where([
                                'school_id' => $school_id,
                                'sess_id' => $this->getSession()->id,
                                'class_teacher_id' => $subject_teacher->class_teacher_id,
                            ])->first();

                            $student_ids .= '~' . $students_in_class->student_ids;
                        }
                    }
                    $class_teachers = ClassTeacher::where(['teacher_id' => $teacher->id, 'school_id' => $school_id])->get();
                    if ($class_teachers->isNotEmpty()) {
                        foreach ($class_teachers as $class_teacher) {
                            $students_in_class =  StudentsInClass::where([
                                'school_id' => $school_id,
                                'sess_id' => $this->getSession()->id,
                                'class_teacher_id' => $class_teacher->id,
                            ])->first();

                            $student_ids .= '~' . $students_in_class->student_ids;
                        }
                    }
                    $student_ids = array_unique(explode('~', substr($student_ids, 1)));

                    foreach ($student_ids as $student_id) {
                        $gurdian_student = GuardianStudent::where('student_id', $student_id)->first();
                        $recipients[] = $gurdian_student->guardian;
                    }

                    break;
                }

            case 'staff':
                $recipients = Staff::where('school_id', $school_id)
                    ->where('user_id', '!=', $user_id)->get();
                if ($user->hasRole('parent')) {

                    $recipients = [];
                    $guardian = Guardian::where(['school_id' => $school_id, 'user_id' => $user_id])->first();
                    $guardian_wards = $guardian->guardianWards;

                    foreach ($guardian_wards as $guardian_ward) {
                        $student_in_class_obj = new StudentsInClass();
                        $student_id = $guardian_ward->student_id;
                        $student_in_class = $student_in_class_obj->fetchStudentInClass($student_id, $this->getSession()->id, $this->getTerm()->id, $school_id);

                        if ($student_in_class->classTeacher) {
                            $recipients[] = Staff::find($student_in_class->classTeacher->teacher_id);
                        }
                    }
                    $recipients[] = Staff::join('staff_roles', 'staff.id', '=', 'staff_roles.staff_id')
                        ->where(['staff_roles.role' => 'admin', 'staff_roles.school_id' => $school_id])->first();
                    break;
                }
                if ($user->hasRole('student')) {

                    $student_id = $this->getStudent()->id;
                    $recipients = [];

                    $student_in_class_obj = new StudentsInClass();

                    $student_in_class = $student_in_class_obj->fetchStudentInClass($student_id, $this->getSession()->id, $this->getTerm()->id, $school_id);

                    if ($student_in_class->classTeacher) {
                        $recipients[] = Staff::find($student_in_class->classTeacher->teacher_id);

                        $stubject_teachers = SubjectTeacher::where('class_teacher_id', $student_in_class->class_teacher_id)->get();
                        foreach ($stubject_teachers as $stubject_teacher) {
                            $recipients[] = Staff::find($stubject_teacher->teacher_id);
                        }
                    }
                    $recipients[] = Staff::join('staff_roles', 'staff.id', '=', 'staff_roles.staff_id')
                        ->where(['staff_roles.role' => 'admin', 'staff_roles.school_id' => $school_id])->first();
                    break;
                }

                break;

            default:
                // $recipients = Student::where('school_id', $school_id)
                //     ->where('user_id', '!=', $user_id)->get();
                break;
        }

        foreach ($recipients as $recipient) :

            if ($recipient->user) {
                $username = ($recipient->user->email != null) ? $recipient->user->email : $recipient->user->username;
                $selected_recipients[] = array('id' => $recipient->user->id, 'name' => $recipient->user->first_name . ' ' . $recipient->user->last_name . ' (' . $username . ')');
            }

        endforeach;

        return json_encode($selected_recipients);
    }

    private function processRepliedMessages($request, $message)
    {
        $user_id = $this->getUser()->id;
        if ($message->replies == NULL || $message->replies == '') {
            $old_replies = '';
        } else {
            $old_replies = '~' . $message->replies;
        }
        $replier = $user_id;
        $reply_message = $request->message;

        $reply_details = json_encode(
            array(
                'replier' => $replier,
                'message' => $reply_message
            )
        ) . $old_replies;

        $message->replies = $reply_details;
        $message->save();
    }

    public function update(Request $request,  $id)
    {
        $message = Message::find($id);



        $school_id = $this->getSchool()->id;
        $user_id = $this->getUser()->id;

        //reply action
        if ($request->action == 'reply') {

            $this->processRepliedMessages($request, $message);
            $original_message_id = $message->id;
            if ($message->original_message_id != NULL) {
                $original_message = Message::find($message->original_message_id);
                $this->processRepliedMessages($request, $original_message);

                /*$original_message_id = $original_message->id;
                $original_message->copied_to;
                $new_reply_message->recipient = $message->sender;*/
            }

            //save it as a new message
            /*$new_reply_message = new Message();

            if($message->original_message_id != NULL){

            }
            $new_reply_message->sender = $this->getUser()->id;
            $new_reply_message->copied_to = $message->copied_to;
            $new_reply_message->recipient = $message->sender;
            $new_reply_message->original_message_id = $original_message_id;
            $new_reply_message->subject = $message->subject;
            $new_reply_message->message = $request->message;
            $new_reply_message->save();*/

            return 'true';
        }

        if ($request->action == 'forward') {
            if ($message->copied_to == NULL || $message->copied_to == '') {
                $old_copied = '';
            } else {
                $old_copied = $message->copied_to;
            }
            $recipients = $request->recipients;
            $forwarded_to = '';
            foreach ($recipients as $recipient) :
                $forwarded_to .= '~' . $recipient;
            endforeach;
            $uniq_recipients = array_unique(explode('~', $old_copied . $forwarded_to));
            $uniq_recipients_str = implode('~', $uniq_recipients);
            $message->copied_to = $uniq_recipients_str . '~';

            $message->save();
            return 'true';
        }

        if ($request->action == 'delete_inbox') {


            if ($message->recipient_delete == NULL || $message->recipient_delete == '') {
                $deleted_recipients = '';
            } else {
                $deleted_recipients = $message->recipient_delete;
            }
            $this_recipient = '~' . $user_id;

            $uniq_recipients = array_unique(explode('~', $deleted_recipients . $this_recipient));
            $uniq_recipients_str = implode('~', $uniq_recipients);
            $message->recipient_delete = $uniq_recipients_str . '~';

            $message->save();
            return 'true';
        }
        if ($request->action == 'delete_sent') {


            $this_recipient = $user_id;

            $message->sender_delete = $this_recipient;

            $message->save();
            return 'true';
        }
        return 'false';
    }
    /**
     * @param Message $message, $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function messageDetails(Message $message, $id)
    {
        $user_id = $this->getUser()->id;
        $message_details = Message::find($id);
        //record that this user has read the message;
        $read_by = $message_details->read_by;
        $read_by = addSingleElementToString($read_by, $user_id);
        $message_details->read_by = $read_by;
        $message_details->save();

        $copied_recipients = array();
        if ($message_details->copied_to != NULL) {
            $copied_to = substr(
                $message_details->copied_to,
                1,
                (strlen($message_details->copied_to) - 2)
            );
            $copied_to_array = explode('~', $copied_to);


            foreach ($copied_to_array as $user_id) :
                $recipient = User::where('id', $user_id)->select('id', 'first_name', 'last_name', 'email', 'username')->get();
                $copied_recipients[] = $recipient[0];

            endforeach;
        }
        list($options, $recipients) = $this->extraOptions();

        $message_details->copied_recipients = $copied_recipients;

        $replies = $message_details->replies;
        $message_details->replies = '[]';
        if ($replies != NULL) {
            $message_details->replies = $this->formatMessageReplies($replies);
        }
        return $this->render('core::messages.message_details', compact('message_details', 'options', 'recipients'));
    }

    public function formatMessageReplies($replies)
    {
        $replies_array = explode('~', $replies);

        foreach ($replies_array as $reply) :
            $decode_reply = json_decode($reply);

            $replier_detail = User::find($decode_reply->replier);

            $decode_reply->first_name = $replier_detail->first_name;
            $decode_reply->last_name = $replier_detail->last_name;
            $decode_reply->email = $replier_detail->email;
            $decode_reply->username = $replier_detail->username;

            $json_repy[] = $decode_reply;
        endforeach;


        return $json_repy;
    }
    /*public function forwardForm($id)
    {
        list($options, $recipients) = $this->extraOptions();

        return $this->render('core::messages.forward_message_form', compact('id','options','recipients'));
    }*/
}
