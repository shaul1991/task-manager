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
        Schema::create('task_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);

            // 타임스탬프 컬럼 (timezone 지원 + 자동 관리 + 인덱스)
            $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
            $table->dateTimeTz('updated_at')->useCurrent()->useCurrentOnUpdate()->index('idx_updated_at');
            $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_groups');
    }
};
