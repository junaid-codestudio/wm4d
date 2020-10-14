<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
	use HasFactory;

	protected $fillable = [
		'client_id',
		'class_name',
		'method_name',
		'data',
		'status'
	];
	protected $table = 'job_logs';

  /**
 * [$primaryKey description]
 * @var string
 */
  protected $primaryKey = 'job_id';

	protected $attributes = ['status'=>0];
}
