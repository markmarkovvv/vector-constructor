<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('illustrations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('City');
            $table->mediumText('svg_content');
            $table->string('view_box')->default('0 0 800 600');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('illustration_id')->nullable()->constrained('illustrations')->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('General');
            $table->mediumText('svg_content');
            $table->string('view_box')->default('0 0 200 200');
            $table->float('width')->default(150);
            $table->float('height')->default(150);
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('compositions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Без названия');
            $table->integer('canvas_width')->default(1200);
            $table->integer('canvas_height')->default(800);
            $table->json('canvas_data');
            $table->mediumText('svg_preview')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compositions');
        Schema::dropIfExists('components');
        Schema::dropIfExists('illustrations');
    }
};
