<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Notifications\SignupActivate;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Auth;

class AuthController extends Controller
{
	public function signup(Request $request)
	{
		$validate = Validator::make($request->all(), [
    		'user_name' => 'required|string|min:3',
    		'email'     => 'required|email',
    		'password'  => 'required|min:6' 
    	]);

    	if($validate->fails()) {
    		return response()->json([
    			'success' => false,
    			'message' => $validate->errors()
    		], 422);
    	}

    	try {
	    	$user = User::create([
	    		'full_name' => trim($request->full_name),
	    		'user_name' => trim($request->user_name),
	    		'email' => trim($request->email),
	    		'password' => trim(bcrypt($request->password)),
	    		'created_at' => Carbon::now(),
	    		'activation_token' => str_random(60)
	    	]);
	    	$user->notify(new SignupActivate($user));

	    	return response()->json([
	    		'success' => true,
	    		'message' => 'Signup Completed. Please acitve your account!'
	    	], 201);
    	} catch (Exception $e) {
    		return repsonse()->json([
    			'success' => false,
    			'message' => $e->getMessage()
    		], 400);
    	}
	}

	public function signupActivate($token)
	{
		$user = User::where('activation_token', $token)->first();

		if(!$user) {
			return response()->json([
				'success' => false,
				'message' => 'This activation token is invalid'
			], 400);
		}
		$user->email_verified_at = Carbon::now();
		$user->activation_token = '';
		$user->active = true;
		$user->save();

		return new UserResource($user);
	}

    public function authenticate(Request $request)
    {
    	$validate = Validator::make($request->all(), [
    		'email'    => 'required|email',
    		'password' => 'required|min:6' 
    	]);

    	if($validate->fails()) {
    		return response()->json([
    			'success' => false,
    			'message' => $validate->errors()
    		], 422);
    	}

    	try {
    		$credentials = request(['email', 'password']);
	    	$credentials['active'] = 1;
	    	$credentials['deleted_at'] = null;

            if(!Auth::attempt($credentials)) {
	    		return response()->json([
	    			'success' => false,
	    			'message' => 'Authentication failed. Invalid credentials!'
	    		], 400);
	    	}
	    	$user = $request->user();
	    	$tokenResult = $user->createToken("Personal Access Token");
	    	$token = $tokenResult->token;
	    	if($request->remember_me)
	    		$token->expires_at = $token->expires_at->addWeeks(1);
	    	$token->save();

	    	return response()->json([
	    		'access_token' => $tokenResult->accessToken,
	    		'token_type'   => 'Bearer',
	    		'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
	    	]);
    	} catch (Exception $e) {
    		return response()->json([
    			'success' => false,
    			'message' => $e->getMessage()
    		]);
    	}
    }

    public function logout(Request $request)
    {
    	$request->user()->token()->revoke();

    	return response()->json([
    		'success' => true,
    		'message' => 'Successfully logged out!'
    	], 200);
    }
}
