<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

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
}
