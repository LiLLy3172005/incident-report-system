// database/migrations/2025_01_05_000003_create_reports_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('category_id')->constrained('incident_categories')->onDelete('restrict');
            $table->text('audio_url');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address_text', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('ai_label', 20)->default('UNTESTED'); // UNTESTED, REAL, FAKE
            $table->float('ai_confidence')->nullable();
            $table->string('status', 20)->default('pending'); // pending, processing, resolved, rejected
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};