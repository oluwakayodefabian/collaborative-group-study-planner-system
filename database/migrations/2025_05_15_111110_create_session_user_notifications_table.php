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
        Schema::create('session_user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\StudySession::class, 'study_session_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('notified_at')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_user_notifications');
    }
};
