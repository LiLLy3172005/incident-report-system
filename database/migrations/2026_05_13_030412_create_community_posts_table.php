<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng bài đăng
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // Lý do từ chối
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Bảng media đính kèm cho bài đăng
        Schema::create('community_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type', 20); // image, video
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Bảng comment
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('content')->nullable();
            $table->string('image_path')->nullable(); // Comment bằng ảnh
            $table->boolean('is_anonymous')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        // Bảng like bài đăng
        Schema::create('community_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unique(['post_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_likes');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_post_media');
        Schema::dropIfExists('community_posts');
    }
};