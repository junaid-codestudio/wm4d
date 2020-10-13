<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CloudwaysController;
use App\Http\Controllers\ServerManagementController;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SiteManagementController extends CloudwaysController
{
	function __construct()
	{
		parent::__construct();
	}

	public function getSitesList($server_id = false): array
	{
		$server = new ServerManagementController();
		$server_list = $server->getServerList($this->cw_token_details['access_token']);
		// return $server_list;

		try {
			if($server_list['status'] = 200){
				$servers = $server_list['data']['servers'];

				$sites = array();
				foreach ($servers as $server) {
					if($server_id && $server_id != $server['id']){
						continue;
					}
					$site = array();
					$site['server_id'] = $server['id'];
					$site['server_label'] = $server['label'];
					$site['server_status'] = $server['status'];
					$site['server_fqdn'] = $server['server_fqdn'];
					$site['server_public_ip'] = $server['public_ip'];

					foreach ($server['apps'] as $app) {
						$app = array_merge($site, $app);
						array_push($sites, $app);
					}
				}

				$res = [
					'status' => 200,
					'message' => 'Data Found',
					'error' => '',
					'data' => $sites
				];
				// return $res;
			} else {
				$res = [
					'status' => 500,
					'message' => 'Some error occured. Please try again!',
					'error' => $e->getMessage()
				];
				return $res;
			}
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!',
				'error' => $e->getMessage()
			];
			return $res;
		}

		return $res;
	}

	public function getApplicationAccess($server_id = false, $app_id = false): array
	{
		if(!$server_id || !$app_id){
			$res = [
				'status' => 403,
				'message' => 'Please provide correct server id and app id',
				'error' => 'Server ID Not given, App ID Not given',
				'data' => []
			];
			return $res;
		}
		self::__construct();
		$token = $this->cw_token_details['access_token'];
		// $this->cw_token_details['access_token'] = 'EJEcrKzyTVVqGvGCcnyJlV4XWvFmOtV1i9ArCJhw';
		try {
			$url = 'https://api.cloudways.com/api/v1/app/creds';
			$headers = [
				'Authorization' => 'Bearer ' . $token
			];
			$params = [
				'server_id' => $server_id,
				'app_id' => $app_id
			];
			$response = Http::withHeaders($headers)->get($url, $params);
			$res = [
				'status' => 200,
				'message' => 'Data Found',
				'error' => '',
				'data' => $response->json()
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some Error Occured. Please try again!',
				'error' => $e->getMessage(),
				'data' => []
			];
			return $res;
		}
		return $res;
	}

	public function addApp(Request $request): array
	{
		$data =  $request->only('server_id', 'application', 'app_version', 'app_label', 'project_name');
		
		$rules = [
			'server_id' => 'required|integer|digits_between:3,9',
			'target' => 'required|string|in:' . implode(',', $this->targets),
			'duration' => 'required|string|in:' . implode(',', $this->durations),
			'storage' => 'required|boolean',
			'timezone' => 'required|in:' . implode(',', DateTimeZone::listIdentifiers()),
			'output_format' => 'required|string|in:svg,json'
		];
		$validated = Validator::make($req, $rules);
		if($validated->fails()){
			$res = [
				'status' => Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
				'message' => $validated->messages(),
				'data' => []
			];
			return $res;
		}
		self::__construct();

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

	public function updateAppLabel(Request $request): string
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
