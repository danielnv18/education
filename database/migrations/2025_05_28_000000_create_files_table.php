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
        Schema::create('files', function (Blueprint $table): void {
            $table->id();
            $table->morphs('fileable');
            $table->string('name');
            $table->string('path');
            $table->string('mime_type');
            $table->string('extension');
            $table->unsignedBigInteger('size');
            $table->string('disk')->default('local');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
