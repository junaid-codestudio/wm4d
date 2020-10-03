<?php

namespace App\Overrides;

use Closure;
use Ejarnutowski\LaravelApiKey\Models\ApiKey;
use Ejarnutowski\LaravelApiKey\Http\Middleware\AuthorizeApiKey;
use Ejarnutowski\LaravelApiKey\Models\ApiKeyAccessEvent;
use Illuminate\Http\Request;
/**
 * 
 */
class PublicApiAuth extends AuthorizeApiKey
{
	// const AUTH_HEADER = 'X-Authorization';
	
	public function handle(Request $request, Closure $next)
	{
		$header = $request->header(self::AUTH_HEADER);
		$apiKey = ApiKey::getByKey($header);

		if ($apiKey instanceof ApiKey) {
			$this->logAccessEvent($request, $apiKey);
			return $next($request);
		}

		return response([
			'status' => 401,
			'message' => 'Unauthorized'
		], 200);
	}
  }