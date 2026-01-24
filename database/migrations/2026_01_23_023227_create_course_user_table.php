<?php

declare(strict_types=1);

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
        Schema::create('course_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->index(); // teacher, student, assistant
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->foreignId('invitation_id')->nullable(); // FK to invitations table later
            $table->string('status')->index(); // pending, active, inactive
            $table->json('metadata')->default('[]');
            $table->timestamps();
            $table->softDeletes(); // Schema mentions deleted_at for pivot too

            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};
