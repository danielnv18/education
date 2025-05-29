<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
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
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', array_column(CourseStatus::cases(), 'value'))->default(CourseStatus::DRAFT->value);
            $table->boolean('is_published')->default(false);
            $table->foreignId('teacher_id')->nullable()->constrained('users');
            $table->foreignId('thumbnail_id')->nullable()->constrained('files');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });

        // Course enrollments pivot table
        Schema::create('course_enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', array_column(EnrollmentStatus::cases(), 'value'))->default(EnrollmentStatus::ACTIVE->value);
            $table->dateTime('enrolled_at')->useCurrent();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('courses');
    }
};
