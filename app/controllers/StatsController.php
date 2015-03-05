<?php

class StatsController extends BaseController {

	// create a new user
	public function stats_page() {
    	$stats = array(
    	    "total users" => DB::table('users')->count(),
    	    "total notes" => DB::table('notes')->count(),
    	    "new users today" => User::where( DB::raw('DAY(created_at)'), '=', date('d') )->count(),
    	    "new users yesterday" => User::where( DB::raw('DAY(created_at)'), '=', date('d') )->count(),
    	    "new users this month" => User::where( DB::raw('MONTH(created_at)'), '=', date('n') )->count()
        );
        return View::make('stats')->with("stats",$stats);
    }    	
}
