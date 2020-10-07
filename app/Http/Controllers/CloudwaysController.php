<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CloudwaysController extends Controller
{
	private $cw_token_details;
	function __construct()
	{
		$this->getApiToken();
	}

	public function index(): string
	{
		$this->getApiToken();
		return $this->cw_token_details;
	}

	public function getServerList(): string
	{
		$this->getApiToken();
		// $this->cw_token_details['access_token'] = 'EJEcrKzyTVVqGvGCcnyJlV4XWvFmOtV1i9ArCJhw';
		$url = 'https://api.cloudways.com/api/v1/server';
		$headers = [
			'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
		];
		// dd($headers);
		$response = Http::withHeaders($headers)->get($url);
		return $response->json();
	}

	public function getServerDiscUsage($server_id = false): string
	{
		if(!$server_id){
			$response = [
				'status' => Response::HTTP_METHOD_NOT_ALLOWED,
				'message' => __('Please provide correct server id')
			];
			return response()->json($response);
		}
		$this->getApiToken();
		$url = 'https://api.cloudways.com/api/v1/server/' . $server_id . '/diskUsage';
		// return $url;
		$headers = [
			'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
		];
		// dd($headers);
		$response = Http::withHeaders($headers)->get($url);
		$operation = $response->json();
// 		return $operation;
		if(!$operation['status']){
			$response = [
				'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
				'message' => __('Please provide correct server id')
			];
			return response()->json($response);
		}
		$response_operation = $this->getOperationStatus($headers, $operation['operation_id']);
		return $response_operation;
	}

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

	private function getOperationStatus($headers, $id): string
	{
		$url = 'https://api.cloudways.com/api/v1/operation/' . $id;
		$response = Http::withHeaders($headers)->get($url);
		return $response->json();
	}

	private function getApiToken(): void
	{
		$url = 'https://api.cloudways.com/api/v1/oauth/access_token';
		$body = [
			'email' => 'root@wm4d.com',
			'api_key' => 'KzSuDaeNKJaQ8OU2IeK06Ad6R6pioZ'
		];
		$response = Http::post($url, $body);
		$this->cw_token_details = $response->json();
	}
}
