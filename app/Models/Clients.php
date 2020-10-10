<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clients extends Model
{
	use SoftDeletes;
	use HasFactory;

	protected $fillable = [
		'name',
		'customid',
		'marchex_id',
		'status'
	];
	protected $table = 'clients';

  /**
 * [$primaryKey description]
 * @var string
 */
  protected $primaryKey = 'client_id';
}
