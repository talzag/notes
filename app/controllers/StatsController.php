<?php

class StatsController extends BaseController {

	// create a new user
	public function stats_page() {
    	$today = Carbon\Carbon::toDay()->toDateTimeString();
    	$yesterday = Carbon\Carbon::toDay()->subDay()->toDateTimeString();
    	$thismonth = gmdate(date('n'));
    	$lastmonth = gmdate(date('n',strtotime("-1 month")));
    	$today = array(
            "today" => $today,
    	    "new users today" => User::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "new notes today" => Note::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "updated notes today" => Note::where( DB::raw('DAY(updated_at)'), '=', $today )->count(),
    	    "new gdocs today" => Gdoc::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "current date" => date('d F Y G:i:a'),
        );
        $yesterday = array(
            "yesterday" => $yesterday,
            "new users yesterday" => User::where( DB::raw('DAY(created_at)'), '=', $yesterday) ->count(),
    	    "new notes yesterday" => Note::where( DB::raw('DAY(created_at)'), '=', $yesterday) ->count(),
    	    "updated notes yesterday" => Note::where( DB::raw('DAY(updated_at)'), '=', $yesterday )->count(),
    	    "new gdocs yesterday" => Gdoc::where( DB::raw('DAY(created_at)'), '=', $yesterday )->count(),
        );
        $thismonth = array(
    	    "new users this month (".date('F').")" => User::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
    	    "new notes this month (".date('F').")" => Note::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
            
        );
        $lastmonth = array(
    	    "new users last month (".date('F',strtotime("-1 month")).")" => User::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count(),
    	    "new notes last month (".date('F',strtotime("-1 month")).")" => Note::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count(),
        );
        $total = array(
    	    "total users" => DB::table('users')->count(),
    	    "total notes" => DB::table('notes')->count(),
        );
        $stats = array("today" => $today,"yesterday" => $yesterday,"thismonth" => $thismonth,"lastmonth" => $lastmonth,"total" => $total);
        Log::info($stats["today"]);
        return View::make('stats')->with("stats",$stats);
    }    	
}
