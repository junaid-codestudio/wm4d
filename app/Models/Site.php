<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Site
 * 
 * @property int $site_id
 * @property int $server_id
 * @property string $label
 * @property string|null $application
 * @property string|null $app_version
 * @property string|null $app_fqdn
 * @property string|null $app_user
 * @property string $app_password
 * @property string|null $sys_user
 * @property string|null $sys_password
 * @property string|null $cname
 * @property string|null $mysql_db_name
 * @property string|null $mysql_user
 * @property string|null $mysql_password
 * @property string|null $aliases
 * @property string|null $symlink
 * @property string|null $project_id
 * @property string|null $site_created_at
 * @property string|null $webroot
 * @property string|null $is_staging
 * @property string|null $source_app_id
 * @property string|null $is_enable
 * @property string|null $source_server_id
 * @property string|null $is_csr_available
 * @property string|null $is_csr_installed
 * @property string|null $own_ssl
 * @property string|null $lets_encrypt
 * @property string|null $wild_card
 * @property bool $is_production
 * @property string|null $backend_url
 * @property string|null $app_version_id
 * @property string|null $cms_app_id
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Server $server
 *
 * @package App\Models
 */
class Site extends Model
{
	use SoftDeletes;
	protected $table = 'sites';
	protected $primaryKey = 'site_id';

	protected $casts = [
		'server_id' => 'int',
		'is_production' => 'bool'
	];

	protected $hidden = [
		'app_password',
		'sys_password',
		'mysql_password'
	];

	protected $fillable = [
		'server_id',
		'label',
		'application',
		'app_version',
		'app_fqdn',
		'app_user',
		'app_password',
		'sys_user',
		'sys_password',
		'cname',
		'mysql_db_name',
		'mysql_user',
		'mysql_password',
		'aliases',
		'symlink',
		'project_id',
		'site_created_at',
		'webroot',
		'is_staging',
		'source_app_id',
		'is_enable',
		'source_server_id',
		'is_csr_available',
		'is_csr_installed',
		'own_ssl',
		'lets_encrypt',
		'wild_card',
		'is_production',
		'backend_url',
		'app_version_id',
		'cms_app_id',
		'status'
	];

	public function server()
	{
		return $this->belongsTo(Server::class);
	}
}
