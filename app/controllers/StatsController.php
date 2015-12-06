<?php

use Carbon\Carbon;

class StatsController extends BaseController {

	// create a new user
		public function stats_page() {
	  	$now = Carbon::now();
	  	$today = Carbon::toDay();
	  	$yesterday = Carbon::toDay()->subDay();
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
	    return View::make('stats')->with("stats",$stats);
		}

		public function models() {
			$models = $this->getModelNames();
			return $models;
		}

		public function model_stats() {
			$models = Input::get("models");
			$start = Input::get("date_range_start");
			$end = Input::get("date_range_end");
			$time_type = "".Input::get("time_type")."_at";
			$return = $this->getModelCounts($models,$time_type,$start,$end);
			return $return;

			// array of tables in the database - OLD
			// $all_tables = DB::select('SHOW TABLES');
			// $tables = array();
			// foreach ($all_tables as $key => $value) {
			// 	array_push($tables,reset($value));
			// }
		}

		private function getModelCounts($model,$time_type,$start,$end) {
			// get list of Model names
			$return = array();

			// if we got models, do things. Else, kill, eventually try again
			$count = $this->countModelsByTime($time_type,$model,$start,$end);
			$return[$model] = $count;
			return $return;

			// old logic for doing every model which is bad
			// if($models) {
			// 	foreach ($models as $model) {
			// 		$count = $this->countModelsByTime($time_type,$model);
			// 		$return[$model] = $count;
			// 	}
			// 	return $return;
			// } else {
			// 	Log::info("sorry");
			// 	return "sorry";
			// }
		}

		// get list of Model names
		private function getModelNames() {
			$path = app_path() . "/models";
			$out=array();
			if(file_exists($path)) {
				$results = scandir($path);
				foreach($results as $result) {
					if($result === '.' or $result === '..') continue;
					$filename = $result;
					if(is_dir($filename)) {
						$out = array_merge($out, getModelNames($fileName));
					} else {
						$out[] = substr($filename,0,-4);
					}
				}
				return $out;
			} else {
				return false;
			}
		}

		// get count by day based on time attribute such as "created_at" or "updated_at"
		private function countModelsByTime($attr,$model,$first,$last) {
			date_default_timezone_set('America/New_York');
			$today = date('Y-m-d');
			Log::info($first);
			// Get the information requested
			// AFAIK you have to write an IF statement to figure out if we want created or updated at, because the string $attr can't be used as a constant
			if($attr == "created_at") {
				$days_fetch = $model::select($attr)
						->whereBetween('created_at', array(new DateTime($first), new DateTime($last)))
				    ->get();
				$days_fetch_grouped = $days_fetch->groupBy(function($date) {
					return Carbon::parse($date->created_at)->format('m/d/y'); // grouping by years
				});
				$today_fetch = $model::select($attr)
					->whereRaw('date(created_at) = ?', [Carbon::now()->format('Y-m-d')] )
					->get();
			} else if($attr == "updated_at") {
				$days_fetch = $model::select($attr)
						->whereBetween('updated_at', array(new DateTime($first), new DateTime($last)))
				    ->get();
				$days_fetch_grouped = $days_fetch->groupBy(function($date) {
					return Carbon::parse($date->created_at)->format('m/d/y'); // grouping by years
				});
				$today_fetch = $model::select($attr)
					->whereRaw('date(updated_at) = ?', [Carbon::now()->format('Y-m-d')] )
					->get();
			}
			$days = array();
			foreach ($days_fetch_grouped as $key => $value) {
				$days["days"][$key] = count($days_fetch_grouped[$key]);
				$days["total"] = count($days_fetch);
				$days["today"] = count($today_fetch);
			}
		// Also get the total in the range and today
			 return $days;
		}
}
