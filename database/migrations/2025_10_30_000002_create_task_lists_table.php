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
        Schema::create('task_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->comment('users.id (게스트는 NULL)');
            $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
            $table->dateTimeTz('updated_at')->useCurrent()->useCurrentOnUpdate()->index('idx_updated_at');
            $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');

            // 비즈니스 로직 인덱스
            $table->index('user_id', 'idx_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_lists');
    }
};
