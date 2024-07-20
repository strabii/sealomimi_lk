<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLikesToComments extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('site_pages', function (Blueprint $table) {
            $table->integer('allow_dislikes')->default(0);
        });

        /*Schema::create('comment_likes', function (Blueprint $table) {
            //
            $table->integer('user_id')->unsigned();
            $table->integer('comment_id')->unsigned();
            $table->boolean('is_like')->default(true);
        });*/

        Schema::create('comment_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedBigInteger('comment_id');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_like')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('site_pages', function (Blueprint $table) {
            $table->dropColumn('allow_dislikes');
        });

        Schema::dropIfExists('comment_likes');
    }
}
