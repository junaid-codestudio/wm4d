<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\JobLog;

class SqsM extends Model
{
  static function testSqs($data='',$job_id=''){
    try {
      $user = User::create($data);
      if(isset($job_id) && $job_id != ''){
        try {
          JobLog::where('job_id',$job_id)
          ->update(['status'=>2,'data'=>json_encode($user)]);
        } catch (\Exception $e) {
          JobLog::where('job_id',$job_id)
          ->update(['status'=>9]);
          dd($e->getMessage());
        }
      }
    } catch (\Exception $e) {

    }
  }
}
