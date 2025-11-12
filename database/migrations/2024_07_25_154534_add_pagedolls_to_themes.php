<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPagedollsToThemes extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('themes', function (Blueprint $table) {
            $table->boolean('has_headerdoll')->default(0);
            $table->string('extension_headerdoll', 5)->nullable()->default(null);
            $table->boolean('has_pagedoll')->default(0);
            $table->string('extension_pagedoll', 5)->nullable()->default(null);
        });
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->string('headerdoll_image_url')->default('');
            $table->string('pagedoll_image_url')->default('');
            $table->string('pagedoll_image_url')->nullable()->default(null)->change();
            $table->string('headerdoll_image_url')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('has_headerdoll');
            $table->dropColumn('extension_headerdoll');
            $table->dropColumn('has_pagedoll');
            $table->dropColumn('extension_pagedoll');
        });
        Schema::table('theme_editor', function (Blueprint $table) {
            $table->dropColumn('headerdoll_image_url');
            $table->dropColumn('pagedoll_image_url');
            $table->string('headerdoll_image_url')->default('')->change();
            $table->string('pagedoll_image_url')->default('')->change();
        });
    }
}
