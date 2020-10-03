<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudwaysApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloudways_apis', function (Blueprint $table) {
            $table->bigIncrements('cw_api_id');
            $table->text('url');
            $table->text('name');
            $table->text('key');
            $table->dateTime('created_at')->default('current_timestamp()');
            $table->dateTime('updated_at')->default('current_timestamp() on update current_timestamp()');
            $table->dateTime('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloudways_apis');
    }
}
