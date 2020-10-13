<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class CloudwaysController extends Controller
{
	protected $cw_token_details;
	function __construct()
	{
		try {	
			$this->getApiToken();
			$token = $this->cw_token_details['access_token'];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some Error Occured. Please try again!',
				'error' => $e->getMessage(),
				'data' => []
			];
			return $res;
		}
	}

	/**
	* [index description]
	* @return string [description]
	*/
	public function index(): string
	{
		$this->getApiToken();
		return $this->cw_token_details;
	}

	/**
	* [checkOperationStatus description]
	* @param  boolean $operation_id [description]
	* @return string                [description]
	*/
	public function checkOperationStatus($operation_id=false): string
	{
		if(!$operation_id){
			$response = [
				'status' => Response::HTTP_METHOD_NOT_ALLOWED,
				'message' => __('Please provide correct operation id')
			];
			return response()->json($response);
		}
		$this->getApiToken();
		// return $url;
		$headers = [
			'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
		];
		$response_operation = $this->getOperationStatus($headers, $operation_id);
		return $response_operation;

		# code...4475815
	}

	/**
	* [getOperationStatus description]
	* @param  string $headers [description]
	* @param  int $id      [description]
	* @return string          [description]
	*/
	private function getOperationStatus($headers, $id): string
	{
		$url = 'https://api.cloudways.com/api/v1/operation/' . $id;
		$response = Http::withHeaders($headers)->get($url);
		return $response->json();
	}

	/**
	* [getApiToken description]
	*/
	protected function getApiToken(): void
	{
		$url = 'https://api.cloudways.com/api/v1/oauth/access_token';
		/* $body = [
		'email' => 'root@wm4d.com',
		'api_key' => 'KzSuDaeNKJaQ8OU2IeK06Ad6R6pioZ'
	]; */
	$body = [
		'email' => env('CW_API_EMAIL'),
		'api_key' => env('CW_API_TOKEN')
	];
	$response = Http::post($url, $body);
	$this->cw_token_details = $response->json();
}

public function addApp(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = 'https://api.cloudways.com/api/v1/app';
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id'],
		"application" => $data['application'],
		"app_version" => $data['app_version'],
		"app_label" => $data['app_label'],
		"project_name" => $data['project_name']
	];

	try {
		$response = Http::withHeaders($headers)->post($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}

public function cloneApp(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = 'https://api.cloudways.com/api/v1/app/clone';
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id'],
		"app_id" => $data['app_id'],
		"app_label" => $data['app_label']
	];

	try {
		$response = Http::withHeaders($headers)->post($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}

public function cloneToOtherServer(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = 'https://api.cloudways.com/api/v1/app/cloneToOtherServer';
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id'],
		"app_id" => $data['app_id'],
		"destination_server_id" => $data['destination_server_id'],
		"app_label" => $data['app_label']
	];

	try {
		$response = Http::withHeaders($headers)->post($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}

public function cloneStagingApp(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = 'https://api.cloudways.com/api/v1/staging/app/cloneApp';
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id'],
		"app_id" => $data['app_id']
	];

	try {
		$response = Http::withHeaders($headers)->post($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}

public function cloneStagingAppToOtherServer(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = 'https://api.cloudways.com/api/v1/staging/app/cloneToOtherServer';
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id'],
		"app_id" => $data['app_id'],
		"destination_server_id" => $data['destination_server_id']
	];

	try {
		$response = Http::withHeaders($headers)->post($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}

public function deleteApp(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = "https://api.cloudways.com/api/v1/app/{$data['app_id']}";
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id']
		];

	try {
		$response = Http::withHeaders($headers)->delete($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}

public function updateAppLable(Request $request): string
{
	$data =  json_decode($request->getContent(),true);
	if($data == ''){
		return response()->json([
			"Code" => 403,
			"message" => "invalid input"
		]);
	}

	$this->getApiToken();
	$url = "https://api.cloudways.com/api/v1/app/{$data['app_id']}";
	$headers = [
		'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
	];
	$body = [
		"server_id" => $data['server_id'],
		"appId" => $data['app_id'],
		"label" => $data['label']
		];

	try {
		$response = Http::withHeaders($headers)->put($url, $body);
		$res = $response->json();
		if(!$res){
			$res = [
				'status' => 404,
				'message' => 'Try again.'
			];
		}
	} catch (\Exception $e) {
		$res = [
			'status' => 404,
			'message' => $e->getMessage(),
			'response' => $response
		];
	}
	return $res;
}


}
