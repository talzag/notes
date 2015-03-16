<?php

class AdminController extends BaseController {
    // the root "note" url logic. Either it's a new note or an existing note.
	public function downloadUsers() {
        $table = User::where('is_temporary', 0)->get();
//         $table = User::all();
        $output='';
        foreach ($table as $row) {
            Log::info($row->email);
            $output.=  $row->email;
            $output.= "\n";
        }
        $headers = array(
          'Content-Type' => 'text/csv',
          'Content-Disposition' => 'attachment; filename="Users.csv"',
        );
        
        return Response::make(rtrim($output, "\n"), 200, $headers);   
    }
}