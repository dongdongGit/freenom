<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->default(0)->comment('用户id');
            $table->string('name')->nullable()->index()->comment('原始名称');
            $table->string('mime')->nullable()->comment('mimeType');
            $table->string('path')->comment('路径');
            $table->integer('width')->unsigned()->default(0)->comment('图片宽');
            $table->integer('height')->unsigned()->default(0)->comment('图片高');
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
        Schema::dropIfExists('images');
    }
}
