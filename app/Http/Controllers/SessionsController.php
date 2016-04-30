<?php namespace App\Http\Controllers;

use Auth;
use Input;
use Session;

class SessionsController extends Controller {

	public function create()
	{
		if (Auth::check()) return redirect("/");
		return View::make("sessions.create");
	}

	public function store()
	{
		if(Auth::attempt(Input::only("email","password"))) {
			return redirect("/".Input::get("url"));
		}
		return "failed";

	}

	public function destroy()
	{
		Auth::logout();
		Session::forget('upload_token');
		return redirect("");
	}
}
