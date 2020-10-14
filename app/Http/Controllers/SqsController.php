<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Jobs\Sqs;
use App\Models\SqsM;
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;
use App\Models\JobLog;

class SqsController extends Controller
{

	/**
	* [getServerList description]
	* @return string [description]
	*/
	public function test(Request $request)
	{
		$user = auth()->user();
		$class = 'App\Models\SqsM';
		$method = 'testSqs';

		$current_class = __CLASS__;
		$current_method = __METHOD__;
		$current_class = explode('\\', $current_class);
		$current_class = end($current_class);
		$current_method = explode('::', $current_method);
		$current_method = end($current_method);

		// $update['current_class'] = $current_class;
		// $update['current_method'] = $current_method;

		$params=[
		'client_id'=>$user->id,
		'class_name'=>$current_class,
		'method_name'=>$current_method,
		'status'=>0,
		'data'=>Null,
	];
	$created_job = JobLog::create($params);

	$data['name'] = 'test33';
	$data['email'] = 'test33@example.com';
	$data['password'] = 'test3';

	$var = Sqs::dispatch('App\Models\SqsM','testSqs',$data,$created_job->job_id);
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
