<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SqsM;
use App\Models\JobLog;


class Sqs implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  private $className;
  private $methodName;
  private $data;
  private $job_id;
  /**
  * Create a new job instance.
  *
  * @return void
  */
  public function __construct($className,$methodName,$data='',$job_id='')
  {
    $this->className = $className;
    $this->methodName = $methodName;
    $this->data = $data;
    $this->job_id = $job_id;
  }

  /**
  * Execute the job.
  *
  * @return void
  */
  public function handle()
  {
    if(isset($this->job_id) && $this->job_id != ''){
      try {
        JobLog::where('job_id',$this->job_id)
        ->update(['status'=>1]);
      } catch (\Exception $e) {
        JobLog::where('job_id',$this->job_id)
        ->update(['status'=>9]);
      }
    }
    $class = $this->className;
    $method = $this->methodName;
    $class::$method($this->data,$this->job_id);
  }
}
