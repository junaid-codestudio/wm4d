<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->bigIncrements('server_id');
            $table->text('label');
            $table->text('server_status');
            $table->text('tenant_id')->nullable();
            $table->text('backup_frequency')->nullable();
            $table->text('backup_retention')->nullable();
            $table->boolean('local_backups')->default(false);
            $table->text('backup_time')->nullable();
            $table->text('is_terminated')->nullable();
            $table->text('server_created_at')->nullable();
            $table->text('server_updated_at')->nullable();
            $table->text('platform')->nullable();
            $table->text('cloud')->nullable();
            $table->text('region')->nullable();
            $table->text('zone')->nullable();
            $table->text('instance_type')->nullable();
            $table->text('db_volume_size')->nullable();
            $table->text('data_volume_size')->nullable();
            $table->text('server_fqdn')->nullable();
            $table->text('public_ip')->nullable();
            $table->text('volume_size')->nullable();
            $table->text('master_user')->nullable();
            $table->text('master_password')->nullable();
            $table->text('snapshot_frequency')->nullable();
            $table->text('memory_size')->nullable();
            $table->json('autoscale_policy')->nullable();
            $table->text('storage');
            $table->text('series');
            $table->text('cloud_plan_id');
            $table->text('status');
            $table->dateTime('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servers');
    }
}
