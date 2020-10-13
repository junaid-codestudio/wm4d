<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->bigIncrements('site_id');
            $table->unsignedBigInteger('server_id');
            $table->foreign('server_id')->references('server_id')->on('servers');
            $table->text('label');
            $table->text('application')->nullable();
            $table->text('app_version')->nullable();
            $table->text('app_fqdn')->nullable();
            $table->text('app_user')->nullable();
            $table->text('app_password')->default(false);
            $table->text('sys_user')->nullable();
            $table->text('sys_password')->nullable();
            $table->text('cname')->nullable();
            $table->text('mysql_db_name')->nullable();
            $table->text('mysql_user')->nullable();
            $table->text('mysql_password')->nullable();
            $table->json('aliases')->nullable();
            $table->text('symlink')->nullable();
            $table->text('project_id')->nullable();
            $table->text('site_created_at')->nullable();
            $table->text('webroot')->nullable();
            $table->text('is_staging')->nullable();
            $table->text('source_app_id')->nullable();
            $table->text('is_enable')->nullable();
            $table->text('source_server_id')->nullable();
            $table->text('is_csr_available')->nullable();
            $table->text('is_csr_installed')->nullable();
            $table->text('own_ssl')->nullable();
            $table->json('lets_encrypt')->nullable();
            $table->text('wild_card')->nullable();
            $table->boolean('is_production')->default(false);
            $table->text('backend_url')->nullable();
            $table->text('app_version_id')->nullable();
            $table->text('cms_app_id')->nullable();
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
        Schema::dropIfExists('sites');
    }
}
