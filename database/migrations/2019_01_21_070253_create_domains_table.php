<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->default(0)->comment('用户id');
            $table->string('domain')->unique()->comment('域名');
            $table->enum('status', ['active', 'expired'])->default('active')->comment('状态, active-活动,expired-过期');
            $table->enum('type', ['free', 'paid'])->default('free')->comment('类型, free-免费,paid-收费');
            $table->date('register_time')->index()->comment('注册时间');
            $table->date('expires_time')->index()->comment('过期时间');
            $table->tinyInteger('enabled_auto_renew')->comment('是否自动续费');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
