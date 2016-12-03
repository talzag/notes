<?php namespace App\Http\Controllers;

use Log;
use Response;
use Input;
use Note;use User;
use DB;

class AdminController extends Controller {
    // the root "note" url logic. Either it's a new note or an existing note.
	public function downloadUsers() {

			$user_type = Input::get("user_type");
			if ($user_type == "permanent") {
				$operator = '=';
				$temp_value = 0;
			} else if($user_type == "temp") {
				$operator = '=';
				$temp_value = 1;
			} else {
				$operator = '>=';
				$temp_value = 0;
			}

			$table = User::where('is_temporary',$operator,$temp_value)
				->join('notes', 'users.id', '=', 'notes.user_id')
				->select('users.email',DB::raw('count(notes.id) as notes_count'),DB::raw('(SELECT created_at FROM notes WHERE users.id = notes.user_id ORDER BY created_at DESC LIMIT 1) last_note'))
				->groupBy('users.id')
				->get();
        $output='';
        foreach ($table as $row) {
            $output.=  "".$row->email.",".$row->notes_count.",".$row->last_note;
            $output.= "\n";
        }
        $headers = array(
          'Content-Type' => 'text/csv',
          'Content-Disposition' => 'attachment; filename="Users.csv"',
        );

        return Response::make(rtrim($output, "\n"), 200, $headers);
    }

		// public function searchNote() {
		//
		// 	$query = Input::get("search");
		// 	// $notes = DB::table('notes')->where('note','LIKE', '%' . $query . '%')->get();
		// 	$notes = Note::where('note','LIKE','%guterman%')->get();
		// 	return Response::make(json_encode($notes),200);
		// }
		//
		// public function getUserInfo() {
		//
		// 	$query = Input::get("userid");
		// 	$user = User::where("id",$query)->get();
		// 	return Response::make(json_encode($user),200);
		// }
}
