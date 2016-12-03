<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Socialite;
use Log;
use Auth;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function authorizeProvider($provider = 'google')
	{
	    return Socialite::with($provider)->redirect();
	}

	public function login($provider = 'google')
	{
		try {
				$user = Socialite::driver('google')->user();
		} catch (Exception $e) {
				return Redirect::to('google/authorize');
		}

		$authUser = $this->findOrCreateUser($user);

		Auth::login($authUser, true);

		return Redirect::to('/');
	}

	private function findOrCreateUser($githubUser)
	{
		Log::info("FIND OR CREATE USER!");
			// if ($authUser = User::where('github_id', $githubUser->id)->first()) {
			// 		return $authUser;
			// }
			//
			// return User::create([
			// 		'name' => $githubUser->name,
			// 		'email' => $githubUser->email,
			// 		'github_id' => $githubUser->id,
			// 		'avatar' => $githubUser->avatar
			// ]);
	}

}
