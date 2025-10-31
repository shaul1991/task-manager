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
        Schema::table('task_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('task_group_id')
                ->nullable()
                ->after('description')
                ->comment('task_groups.id');

            $table->index('task_group_id', 'idx_task_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_lists', function (Blueprint $table) {
            $table->dropIndex('idx_task_group_id');
            $table->dropColumn('task_group_id');
        });
    }
};
