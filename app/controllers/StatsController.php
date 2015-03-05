<?php

class StatsController extends BaseController {

	// create a new user
	public function stats_page() {
    	$today = gmdate(date('d'));
    	$yesterday = gmdate(date('d',strtotime("-1 days")));
    	$thismonth = gmdate(date('n'));
    	$lastmonth = gmdate(date('n',strtotime("-1 month")));
    	$stats = array(
    	    "total users" => DB::table('users')->count(),
    	    "total notes" => DB::table('notes')->count(),
    	    "new users today" => User::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "new users yesterday" => User::where( DB::raw('DAY(created_at)'), '=', $yesterday) ->count(),
    	    "new users this month" => User::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
    	    "new users last month" => User::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count()
        );
        return View::make('stats')->with("stats",$stats);
    }    	
}
