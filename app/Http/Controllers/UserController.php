<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    public function users()
    {
    	return UserResource::collection(User::with('createdBy')->get());
    }

    public function showUser(User $user)
    {
    	return new UserResource($user);
    }

    public function updateUser(Request $request, User $user)
    {
    	$validate = Validator::make($request->all(), [
    		'user_name' => 'required|min:3',
    		'email'		=> 'required|email|unique:users',
    	]);

    	if($validate->fails()) {
    		return response()->json([
    			'success' => false,
    			'message' => $validate->errors()
    		], 422);
    	}

    	try {
    		$user->update($request->only(['full_name', 'user_name', 'email']));

    		return new UserResource($user);
    	} catch (Exception $e) {
    		return response()->json([
    			'success' => false,
    			'message' => $e->getMessage()
    		], 400);
    	}
    }

    public function deleteUser(User $user)
    {
    	$user->delete();

    	return response()->json(null, 204);
    }
}
