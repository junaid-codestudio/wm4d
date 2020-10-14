<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Hash;

class UserController extends Controller
{
	/**
	* @OA\Post(
	* path="/api/login",
	* summary="Sign in",
	* description="Login by email, password",
	* operationId="authLogin",
	* tags={"auth"},
	* @OA\RequestBody(
	*    required=true,
	*    description="Pass user credentials",
	*    @OA\JsonContent(
	*       required={"email","password"},
	*       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
	*       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
	*    ),
	* ),
	* @OA\Response(
	*     response=200,
	*     description="Success",
	*     @OA\JsonContent(
	*        @OA\Property(property="access_token", type="string", example="jwt_token"),
	*        @OA\Property(property="token_type", type="string", example="bearer"),
	*        @OA\Property(property="expires_in", type="integer", example="3600"),
	*     )
	*  ),
	* @OA\Response(
	*    response=422,
	*    description="Wrong credentials response",
	*    @OA\JsonContent(
	*       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
	*        )
	*     )
	* )
	*/
	public function login(Request $request)
	{
		$credentials = [];
		$data =  $request->all();

		$rules = [
			'email' => 'required|max:255',
			'password' => 'required|max:255'
		];
		$validation_status = Validator::make($data, $rules);
		if($validation_status->fails()){
			$response = [
				'status' => Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
				'message' => $validation_status->messages()
			];
			return response()->json($response);
		}
		// dd($validation_status->fails());
		$credentials = [
			'email' => $data['email'],
			'password' => $data['password']
		];

		$user = User::where('email', $credentials['email'])->first();

		if (! $user || ! Hash::check($credentials['password'], $user->password)) {
			return response()->json(['message' => 'Unauthorized'], 401);
		}

		return response()->json([
			'access_token' => $user->createAccessToken($user),
			'token_type' => 'Bearer',
			'user' => $user
		]);

	}

	public function refresh()
	{
		return $this->respondWithToken(auth('api')->refresh());
	}
}
