<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Student self-assessment on coding logs
        Schema::create('self_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coding_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('understanding')->default(3); // 1-5
            $table->unsignedTinyInteger('confidence')->default(3);    // 1-5
            $table->unsignedTinyInteger('effort')->default(3);        // 1-5
            $table->text('reflection')->nullable();
            $table->text('next_steps')->nullable();
            $table->timestamps();
            $table->unique(['coding_log_id', 'user_id']);
        });

        // Peer/instructor comments on logs
        Schema::create('log_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coding_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // commenter
            $table->text('body');
            $table->timestamps();
        });

        // AI study suggestions log
        Schema::create('study_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('suggestion');
            $table->string('model_used')->default('claude-sonnet-4-6');
            $table->integer('tokens_used')->default(0);
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_suggestions');
        Schema::dropIfExists('log_comments');
        Schema::dropIfExists('self_assessments');
    }
};
