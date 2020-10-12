<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CloudwaysController;
use App\Models\Server;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ServerManagementController extends CloudwaysController
{
	private $targets = [
		"Idle CPU",
		"Free Disk",
		"Reads per second",
		"Writes per second",
		"Free memory",
		"Incoming network traffic",
		"Outgoing network traffic",
		"APC Fill Ratio",
		"APC Hit rate",
		"Monthly Bandwidth",
		"Memcached Fill Ratio",
		"Memcached Hit Rate",
		"Varnish Hit Rate",
		"Varnish Nuked",
		"Auto-healing Restarts",
		"MySQL Connections"
	];

	private $durations = [
		"1 Hour",
		"12 Hours",
		"1 Day",
		"7 Days",
		"1 Month",
		"6 Months"
	];

	function __construct()
	{
		parent::__construct();
	}

	/**
	* [getServerList description]
	* @return string [description]
	*/
	public function getServerList($token = false): array
	{
		if(!$token){
			self::__construct();
			$token = $this->cw_token_details['access_token'];
		}
		// $this->cw_token_details['access_token'] = 'EJEcrKzyTVVqGvGCcnyJlV4XWvFmOtV1i9ArCJhw';
		try {
			$url = 'https://api.cloudways.com/api/v1/server';
			$headers = [
				'Authorization' => 'Bearer ' . $token
			];
			$response = Http::withHeaders($headers)->get($url);
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

	public function saveServerList($data_servers=false): int
	{
		// dd($data);
		if(!$data_servers){
			return 0;
		}
		try {
			$sites = array();
			$servers = array();
			$server_ids = array();
			foreach ( $data_servers as $server )
			{
				$sites[] = $server['apps'];
				unset($server['apps']);
				array_push($servers, $server);
				array_push($server_ids, $server['id']);
			}
			$existing = Server::whereIn('server_id', $server_ids)->withTrashed()->pluck('server_id');
			// dd($existing->toArray());
			$collect = collect($servers);
			$not_existing = $collect->whereNotIn('server_id', $existing)->toArray();
			// dd($not_existing);
			if(count($not_existing) > 0){
				Server::insert($not_existing);
			}
			return count($not_existing);
		} catch (\Exception $e) {
			// dd($e->getMessage());
			return 0;
		}
		return 0;
	}

	/**
	* [getServerList description]
	* @return string [description]
	*/
	public function getMonitoringGraph(Request $request): array
	{
		// return $request->all();
		$req = $request->only('server_id', 'target', 'duration', 'storage', 'timezone', 'output_format');
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
		// $this->cw_token_details['access_token'] = 'EJEcrKzyTVVqGvGCcnyJlV4XWvFmOtV1i9ArCJhw';
		try {
			$url = 'https://api.cloudways.com/api/v1/server/monitor/detail';
			$headers = [
				'Authorization' => 'Bearer ' . $this->cw_token_details['access_token']
			];

			$params = [
				'server_id' => $req['server_id'],
				'target' => $req['target'],
				'duration' => $req['duration'],
				'storage' => $req['storage'],
				'timezone' => $req['timezone'],
				'output_format' => $req['output_format']
			];
		// dd($headers);
			$response = Http::withHeaders($headers)->get($url, $params);
			// dd($response);
			$res = [
				'status' => 200,
				'message' => 'Data Found',
				'error' => '',
				'data' => $req['output_format'] == 'svg' ? $response->json() : json_decode($response->json()['content'])
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
}
