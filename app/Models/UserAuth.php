<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
// Please implement JWTSubject interface
class UserAuth extends Authenticatable implements JWTSubject
{

	use Notifiable;

	protected $fillable = [
		'email',
		'password',
	];
	protected $table = 'users';

  /**
 * [$primaryKey description]
 * @var string
 */
  protected $primaryKey = 'id';
  // BODY OF THIS CLASS

  // Please ADD this two methods at the end of the class
  public function getJWTIdentifier()
  {
  	return $this->getKey();
  }
  public function getJWTCustomClaims()
  {
  	return [
  		'id' => $this->id
  	];
  }
}
