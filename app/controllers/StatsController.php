<?php

class StatsController extends BaseController {

	// create a new user
	public function stats_page() {
        $stats = array("Users"=>count(Users::all()));
        return View::make('stats')->with('stats', $stats);
    }    	
}
