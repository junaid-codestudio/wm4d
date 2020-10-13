<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SqsM extends Model
{
  static function testSqs(){
    $data = ['name'=>'test4','email'=>'test5@example.com','password'=>'test1'];
    User::create($data);
  }
}
