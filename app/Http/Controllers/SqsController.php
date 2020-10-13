<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Jobs\Sqs;
use App\Models\SqsM;
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

class SqsController extends Controller
{

	/**
	* [getServerList description]
	* @return string [description]
	*/
	public function test()
	{
		$class = 'App\Models\SqsM';
		$method = 'testSqs';
		// $func = $class::$method;
		// $func();
		$var = Sqs::dispatch('App\Models\SqsM','testSqs');
		return $this->job->getJobId();
		$c = $var->getJobId();
    dd($c);
		return response()->json(['code'=>200]);
	}

	public function getQueueInfo(){
		$queueUrl = "https://sqs.us-east-1.amazonaws.com/136922246443/WM4D_Queues_1";
		$data = [
      "version" => "latest",
      "http" => [
        "timeout" => 60,
        "connect_timeout" => 60,
      ],
      "driver" => "sqs",
      "key" => "AKIAR7YJTVUV5CE2NHW5",
      "secret" => "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",
      "prefix" => "https://sqs.us-east-1.amazonaws.com/136922246443/WM4D_Queues_1",
      "queue" => "WM4D_Queues_1",
      "suffix" => "",
      "region" => "us-east-1"
    ];
		$client = new SqsClient($data);
		// $client = new SqsClient([
		// 	'profile' => 'default',
		// 	'region' => 'us-east-1',
		// 	'version' => 'latest'
		// ]);

		try {
			$result = $client->receiveMessage(array(
				'AttributeNames' => ['SentTimestamp'],
				'MaxNumberOfMessages' => 1,
				'MessageAttributeNames' => ['All'],
				'QueueUrl' => $queueUrl, // REQUIRED
				'WaitTimeSeconds' => 0,
			));
			if (!empty($result->get('Messages'))) {
				dd($result->get('Messages'));
				var_dump($result->get('Messages')[0]);
				$result = $client->deleteMessage([
					'QueueUrl' => $queueUrl, // REQUIRED
					'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle'] // REQUIRED
				]);
			} else {
				echo "No messages in queue. \n";
			}
		} catch (AwsException $e) {
			// output error message if fails
			error_log($e->getMessage());
		}
	}

}
