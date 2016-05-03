<?php namespace App\Http\Controllers;

use Log;
use Response;
use Input;
use Note;use User;
use DB;

class AdminController extends Controller {
    // the root "note" url logic. Either it's a new note or an existing note.
	public function downloadUsers() {
        $table = User::where('is_temporary', 0)->get();
//         $table = User::all();
        $output='';
        foreach ($table as $row) {
            $output.=  $row->email;
            $output.= "\n";
        }
        $headers = array(
          'Content-Type' => 'text/csv',
          'Content-Disposition' => 'attachment; filename="Users.csv"',
        );

        return Response::make(rtrim($output, "\n"), 200, $headers);
    }

		public function searchNote() {

			$query = Input::get("search");
			// $notes = DB::table('notes')->where('note','LIKE', '%' . $query . '%')->get();
			$notes = Note::where('note','LIKE','%guterman%')->get();
			return Response::make(json_encode($notes),200);
		}

		public function getUserInfo() {

			$query = Input::get("userid");
			$user = User::where("id",$query)->get();
			return Response::make(json_encode($user),200);
		}
}
