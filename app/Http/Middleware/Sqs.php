<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\JobLog;

class Sqs
{
  /**
  * Handle an incoming request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @return mixed
  */
  public function handle(Request $request, Closure $next)
  {
    $path = \Route::getCurrentRoute()->getName();
    $currentAction = \Route::currentRouteAction();
    list($controller, $method) = explode('@', $currentAction);

    $controller = preg_replace('/.*\\\/', '', $controller);

    $app = app();

    $user = auth()->user();

    if(isset($user->id)){
      $job = JobLog::select('status','data')->where(['class_name'=>$controller,'method_name'=>$path,'client_id'=>$user->id])->orderBy('job_id','desc')->first();
      if($job){
        if($job->status == 0 || $job->status == 1){
          $response['status'] = 'in_process';
        }elseif ($job->status == 2) {
          $response['status'] = 'completed';
          $response['data'] = $job->data;
        }elseif ($job->status == 9){
          $response['status'] = 'failed';
        }else {
          $response['status'] = 'no_job_found';
        }
      }
    }
    $controller = $app->make('\App\Http\Controllers\\'. $controller );
    return $controller->callAction($path, $parameters = [$request,$response]);

    // return $next($request);
  }
}
