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
    	    "new users today" => User::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "new users yesterday" => User::where( DB::raw('DAY(created_at)'), '=', $yesterday) ->count(),
    	    "new users this month (".date('F').")" => User::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
    	    "new users last month (".date('F',strtotime("-1 month")).")" => User::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count(),
    	    "total notes" => DB::table('notes')->count(),
    	    "new notes today" => Note::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "new notes yesterday" => Note::where( DB::raw('DAY(created_at)'), '=', $yesterday) ->count(),
    	    "new gdocs today" => Gdoc::where( DB::raw('DAY(created_at)'), '=', $today )->count(),
    	    "new gdocs yesterday" => Gdoc::where( DB::raw('DAY(created_at)'), '=', $yesterday )->count(),
    	    "new notes this month (".date('F').")" => Note::where( DB::raw('MONTH(created_at)'), '=', $thismonth )->count(),
    	    "new notes last month (".date('F',strtotime("-1 month")).")" => Note::where( DB::raw('MONTH(created_at)'), '=', $lastmonth )->count(),
    	    "current date" => date('d F Y G:i:a'),
        );
        return View::make('stats')->with("stats",$stats);
    }    	
}
