<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('cid');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
            $table->longText('content')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedInteger('uid')->default(0);
            $table->string('template')->nullable();
            $table->string('type')->default('post');
            $table->string('status')->default('publish');  //publish hidden password private waiting
            $table->string('password')->nullable();
            $table->unsignedInteger('commentsNum');
            $table->boolean('allowComment');
            $table->unsignedInteger('parent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
}
