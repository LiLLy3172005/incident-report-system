// database/migrations/2025_01_05_000004_create_report_status_histories_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('old_status', 20);
            $table->string('new_status', 20);
            $table->text('note')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_status_histories');
    }
};