<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Server
 * 
 * @property int $server_id
 * @property string $label
 * @property string $server_status
 * @property string|null $tenant_id
 * @property string|null $backup_frequency
 * @property string|null $backup_retention
 * @property bool $local_backups
 * @property string|null $backup_time
 * @property string|null $is_terminated
 * @property string|null $server_created_at
 * @property string|null $server_updated_at
 * @property string|null $platform
 * @property string|null $cloud
 * @property string|null $region
 * @property string|null $zone
 * @property string|null $instance_type
 * @property string|null $db_volume_size
 * @property string|null $data_volume_size
 * @property string|null $server_fqdn
 * @property string|null $public_ip
 * @property string|null $volume_size
 * @property string|null $master_user
 * @property string|null $master_password
 * @property string|null $snapshot_frequency
 * @property string|null $memory_size
 * @property string|null $autoscale_policy
 * @property string $storage
 * @property string $series
 * @property string $cloud_plan_id
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Site[] $sites
 *
 * @package App\Models
 */
class Server extends Model
{
	use SoftDeletes;
	protected $table = 'servers';
	protected $primaryKey = 'server_id';

	protected $casts = [
		'local_backups' => 'bool'
	];

	protected $hidden = [
		'master_password'
	];

	protected $fillable = [
		'label',
		'server_status',
		'tenant_id',
		'backup_frequency',
		'backup_retention',
		'local_backups',
		'backup_time',
		'is_terminated',
		'server_created_at',
		'server_updated_at',
		'platform',
		'cloud',
		'region',
		'zone',
		'instance_type',
		'db_volume_size',
		'data_volume_size',
		'server_fqdn',
		'public_ip',
		'volume_size',
		'master_user',
		'master_password',
		'snapshot_frequency',
		'memory_size',
		'autoscale_policy',
		'storage',
		'series',
		'cloud_plan_id',
		'status'
	];

	public function sites()
	{
		return $this->hasMany(Site::class);
	}
}
