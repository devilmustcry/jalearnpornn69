<?php

namespace App\Http\Controllers\Officer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Calendar;  
use App\Event;  
use App\Officer as officer;

class DashboardController extends Controller
{
    //
    public function __construct(){
        
        //$this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if(empty(Auth::user())) return redirect('/');
            if(Auth::user()->user_status!="superuser"){
                return redirect('/');
            }  
            return $next($request);
        });
        date_default_timezone_set("Asia/Bangkok");
        
    }
    public function index(){ 
         $events = [];  
         $colors = officer::colorEvents();
         $booking = DB::table('booking')
                   ->leftjoin('detail_booking','booking.booking_ID','=','detail_booking.booking_ID')
                   ->join('meeting_room','meeting_room.meeting_ID','=','detail_booking.meeting_ID')
                   ->orderBy('checkin','desc')
                   ->get();
         
         if(isset($booking)){  
            foreach ($booking as $key => $value) {  
                $events[] = Calendar::event(  
                "[".$value->meeting_name."] ".$value->detail_topic,  
                false,  
                new \DateTime($value->detail_timestart),  
                new \DateTime($value->detail_timeout),
                $key,
                [
                    'backgroundColor' =>$colors[$value->meeting_ID-1],
                    'textColor' => '#fff',
                    'description' => "Event Description",
                ]
            );  
            }  
        } 
        //dd($events) ;
        $calendar = Calendar::addEvents($events)->setOptions([
            'timeFormat'=> 'H:mm',
            'lang'=> 'th',
        ]); 
        $data = array(
            'calendar' => $calendar
        );
        return view('officer/index',$data);
    }
}