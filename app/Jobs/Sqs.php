<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SqsM;

class Sqs implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  private $className;
  private $methodName;
  /**
  * Create a new job instance.
  *
  * @return void
  */
  public function __construct($className,$methodName)
  {
    $this->className = $className;
    $this->methodName = $methodName;
  }

  /**
  * Execute the job.
  *
  * @return void
  */
  public function handle()
  {
    $class = $this->className;
    $method = $this->methodName;
    $class::$method();
  }
}
