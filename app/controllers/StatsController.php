<?php

class StatsController extends BaseController {

	// create a new user
	public function stats_page() {
    	$now = Carbon\Carbon::now();
    	$today = Carbon\Carbon::toDay();
    	$yesterday = Carbon\Carbon::toDay()->subDay();
    	$thismonth = gmdate(date('n'));
    	$lastmonth = gmdate(date('n',strtotime("-1 month")));
    	$today_stats = array(
    	    "new users today" => User::whereBetween("created_at", array( $today,$now))->count(),
    	    "new notes today" => Note::whereBetween("created_at", array( $today,$now))->count(),
    	    "updated notes today" => Note::whereBetween("updated_at", array( $today,$now))->count(),
    	    "new gdocs today" => Gdoc::whereBetween("created_at", array( $today,$now))->count(),
        );
        $yesterday_stats = array(
    	    "new users yesterday" => User::whereBetween("created_at", array( $yesterday,$today))->count(),
    	    "new notes yesterday" => Note::whereBetween("created_at", array( $yesterday,$today))->count(),
    	    "updated notes yesterday" => Note::whereBetween("updated_at", array( $yesterday,$today))->count(),
    	    "new gdocs yesterday" => Gdoc::whereBetween("created_at", array( $yesterday,$today))->count(),
        );
        $thismonth_stats = array(
    	    "new users this month (".date('F').")" => User::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
    	    "new notes this month (".date('F').")" => Note::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
            
        );
        $lastmonth_stats = array(
    	    "new users last month (".date('F',strtotime("-1 month")).")" => User::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count(),
    	    "new notes last month (".date('F',strtotime("-1 month")).")" => Note::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count(),
        );
        $total = array(
    	    "total users" => DB::table('users')->count(),
    	    "total notes" => DB::table('notes')->count(),
        );
        $stats = array("today" => $today_stats,"yesterday" => $yesterday_stats,"thismonth" => $thismonth_stats,"lastmonth" => $lastmonth_stats,"total" => $total);
        Log::info($stats["today"]);
        return View::make('stats')->with("stats",$stats);
    }    	
}
