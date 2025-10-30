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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->timestamp('completed_datetime')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
            $table->dateTimeTz('updated_at')->useCurrentOnUpdate()->index('idx_updated_at');
            $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');

            // 비즈니스 로직 인덱스
            $table->index('group_id', 'idx_group_id');
            $table->index('completed_datetime', 'idx_completed_datetime');

            // Note: group_id foreign key는 groups 테이블 생성 후 추가 예정
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
