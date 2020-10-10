<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class ClientsController extends Controller
{
	function __construct() {
	}
	
	function getMarchexClientsList($account_id = false): array {

		if(!$account_id){
			$res = [
				'status' => 403,
				'message' => 'Please provide valid account_id'
			];
			return $res;
		}

		$url = 'https://api.marchex.io/api/jsonrpc/1';
		
		$body = [
			"jsonrpc" => "2.0",
			"id" => 1,
			"method" => "user.list",
			"params" => [
				$account_id
			]
		];
		
		try {
			$response = Http::withHeaders($this->getHeaders())->post($url, $body);
			$res = $response->json();
			if(!$res){
				$res = [
					'status' => 404,
					'message' => 'No data found!'
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
	
	function saveMarchexAccountsList(): array {
		$url = 'https://api.marchex.io/api/jsonrpc/1';
		
		$body = [
			"jsonrpc" => "2.0",
			"id" => 1,
			"method" => "acct.list",
			"params" => [
			]
		];
		
		try {
			$response = Http::withHeaders($this->getHeaders())->post($url, $body);
			$res = $response->json();
			if(!$res){
				$res = [
					'status' => 404,
					'response' => $response
				];
			} else {
				$isSaved = $this->saveAccountsInDb($res);
				$res = [
					'status' => 200,
					'message' => $isSaved . ' records inserted!'
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

	function saveAccountsInDb($data = false): int
	{
		// dd($data);
		if(!$data){
			return 0;
		}
		$data = $data['result'];
		try {
			$existing_ids = array();
			foreach ( $data as $k )
			{
				$data[$k] ['marchex_id'] = $data[$k] ['acct'];
				unset($data[$k]['acct']);
				if($data[$k]['status'] != 'active'){
					$data[$k]['deleted_at'] = Carbon::now();
				} else{
					$data[$k]['deleted_at'] = null;
				}
				array_push($existing_ids, $data[$k]['marchex_id']);
			}
			$existing = Clients::whereIn('marchex_id', $existing_ids)->withTrashed()->pluck('marchex_id');
			// dd($existing->toArray());
			$collect = collect($data);
			$not_existing = $collect->whereNotIn('marchex_id', $existing)->toArray();
			// dd($not_existing);
			if(count($not_existing) > 0){
				Clients::insert($not_existing);
			}
			return count($not_existing);
		} catch (\Exception $e) {
			// return $e->getMessage();
			return 0;
		}
		return 0;
	}
	
	private function getHeaders(): array {
		$authorization = 'Basic ' . env('MARCHEX_AUTH');
		
		$headers = [
			"Content-Type" => "application/json",
			"Accept" => "application/json",
			"Authorization" => $authorization
		];
		return $headers;
	}

	function getAllClients(): array
	{
		try {
			$clients = Clients::withTrashed()->select(['client_id', 'name', 'marchex_id', 'customid', 'status'])->get();
			$res = [
				'status' => 200,
				'message' => count($clients) . ' Clients Found!',
				'data' => $clients
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!'
			];
		}
		return $res;
	}

	function getActiveClients(): array
	{
		try {
			$clients = Clients::select(['client_id', 'name', 'marchex_id', 'customid', 'status'])->get();
			$res = [
				'status' => 200,
				'message' => count($clients) . ' Clients Found!',
				'data' => $clients
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!'
			];
		}
		return $res;
	}

	function getAllMarchexClients(): array
	{
		try {
			$clients = Clients::whereNotNull('marchex_id')->withTrashed()->select(['client_id', 'name', 'marchex_id', 'customid', 'status'])->get();
			$res = [
				'status' => 200,
				'message' => count($clients) . ' Clients Found!',
				'data' => $clients
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!'
			];
		}
		return $res;
	}

	function getActiveMarchexClients(): array
	{
		try {
			$clients = Clients::whereNotNull('marchex_id')->select(['client_id', 'name', 'marchex_id', 'customid', 'status'])->get();
			$res = [
				'status' => 200,
				'message' => count($clients) . ' Clients Found!',
				'data' => $clients
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!'
			];
		}
		return $res;
	}

	function store(Request $request): array
	{
		$req = $request->only('name', 'status');
		$rules = [
			'name' => 'required|min:3|max:255',
			'status' => 'required|in:active,disabled'
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
		try {
			if($req['status'] == 'disabled'){
				$req['deleted_at'] = Carbon::now();
			}
			$created = Clients::create($req);
			$res = [
				'status' => 200,
				'message' => 'Client created with id: ' . $created->client_id . ' and name: ' . $created->name,
				'data' => $created
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!',
				'error' => $e->getMessage(),
				'data' => []
			];
		}
		return $res;
	}

	function update(Request $request): array
	{
		$req = $request->only('client_id', 'name', 'status');
		$rules = [
			'client_id' => 'required|integer',
			'name' => 'required|min:3|max:255|string',
			'status' => 'required|in:active,disabled'
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
		try {
			$updated = Clients::findOrFail($req['client_id']);
		} catch (\Exception $e) {
			$res = [
				'status' => 404,
				'message' => 'No Client Found!',
				'error' => $e->getMessage(),
				'data' => []
			];
			return $res;
		}
		try {
			$updated->name = $req['name'];
			$updated->status = $req['status'];
			if($req['status'] == 'disabled'){
				$updated->deleted_at = Carbon::now();
			}
			$updated->save();
			$res = [
				'status' => 200,
				'message' => 'Client updated with id: ' . $updated->client_id . ' and name: ' . $updated->name,
				'data' => $updated
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!',
				'error' => $e->getMessage(),
				'data' => []
			];
		}
		return $res;
	}

	function delete($client_id = false): array
	{
		if(!$client_id){
			$res = [
				'status' => 404,
				'message' => 'Please provide correct client id.',
				'data' => []
			];
			return $res;
		}

		try {
			$deleted = Clients::findOrFail($client_id);
		} catch (\Exception $e) {
			$res = [
				'status' => 404,
				'message' => 'No Client Found!',
				'error' => $e->getMessage(),
				'data' => []
			];
			return $res;
		}

		try {
			$deleted->delete();
			$res = [
				'status' => 200,
				'message' => 'Client deleted!',
				'data' => $deleted
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!',
				'error' => $e->getMessage(),
				'data' => []
			];
		}
		return $res;
	}

	function restore($client_id = false): array
	{
		if(!$client_id){
			$res = [
				'status' => 404,
				'message' => 'Please provide correct client id.',
				'data' => []
			];
			return $res;
		}

		try {
			$restored = Clients::withTrashed()->findOrFail($client_id);
		} catch (\Exception $e) {
			$res = [
				'status' => 404,
				'message' => 'No Client Found!',
				'error' => $e->getMessage(),
				'data' => []
			];
			return $res;
		}

		try {
			$restored->restore();
			$res = [
				'status' => 200,
				'message' => 'Client restored!',
				'data' => $restored
			];
		} catch (\Exception $e) {
			$res = [
				'status' => 500,
				'message' => 'Some error occured. Please try again!',
				'error' => $e->getMessage(),
				'data' => []
			];
		}
		return $res;
	}

}
