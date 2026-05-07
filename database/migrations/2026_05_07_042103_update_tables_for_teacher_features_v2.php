<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('banner_image')->nullable();
            $table->string('code')->nullable()->unique();
            $table->boolean('is_leaderboard_enabled')->default(true);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->string('format')->default('document'); // document, video, link, text
            $table->string('youtube_url')->nullable();
            $table->longText('text_content')->nullable();
            $table->dateTime('publish_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('requires_previous')->default(false);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('attachment')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('max_score')->default(100);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dateTime('graded_at')->nullable();
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'is_locked']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('graded_at');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'is_published', 'max_score']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['format', 'youtube_url', 'text_content', 'publish_at', 'is_published', 'requires_previous']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'code', 'is_leaderboard_enabled']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio']);
        });
    }
};
