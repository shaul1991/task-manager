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
            $table->timestamps();
            $table->softDeletes();

            // 비즈니스 로직 인덱스
            $table->index('group_id', 'idx_group_id');
            $table->index('completed_datetime', 'idx_completed_datetime');

            // 필수 타임스탬프 인덱스
            $table->index('created_at', 'idx_created_at');
            $table->index('updated_at', 'idx_updated_at');
            $table->index('deleted_at', 'idx_deleted_at');

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
