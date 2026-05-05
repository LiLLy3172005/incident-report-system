// database/migrations/2025_01_05_000001_create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('phone', 15)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->string('role', 20)->default('user'); // user, admin
            $table->integer('strikes')->default(0);
            $table->boolean('is_banned')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Xóa mềm
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};