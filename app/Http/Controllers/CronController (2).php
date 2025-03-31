<?php

namespace App\Http\Controllers;
use App\User;
use DB;
use App\Models\Reminder;
use Carbon\Carbon;

class CronController extends Controller {
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
	}

	public function sendReminderNotification() {
        $user_id = 176;
        $title = 'KiloPoints';
        $message = 'Testing Notification';
        $response = send_notification(0, $user_id, $title, array('title'=>$title,'message'=>$message,'type'=>'testing','key'=>'event'));
        die($response);
        /*$date = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $reminders = Reminder::where('universal_time','<=', $date)->where('reminder_sent', 0)->get();

        if (count($reminders)) {

            foreach ($reminders as $key => $value) {
                $title = 'Reminder';
                $message = 'You have a reminder for '.$value->type;
                send_notification(1, $value->user_id, $title, array('title'=>$title,'message'=>$message,'type'=>0,'key'=>'event'));

                //update reminder sent
                $value->reminder_sent = 1;
                $value->save();
            }
            die('Success');

        } else {
        	die('Error');
        }*/
    }
}
