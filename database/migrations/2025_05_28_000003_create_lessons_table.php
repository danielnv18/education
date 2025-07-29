<?php

declare(strict_types=1);

use App\Enums\LessonType;
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
        Schema::create('lessons', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('order')->default(0);
            $table->enum('type', array_column(LessonType::cases(), 'value'))->default(LessonType::Text->value);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
