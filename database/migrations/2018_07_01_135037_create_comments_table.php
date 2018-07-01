<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid')->index()->default(0);
            $table->timestamps();
            $table->string('name')->nullable();
            $table->integer('authorId')->default(0);
            $table->integer('ownerId')->default(0);
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('agent')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('approved');  //approved pending spam
            $table->integer('parent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
