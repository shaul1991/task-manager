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
        Schema::table('task_groups', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('name');
            $table->index('order', 'idx_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_groups', function (Blueprint $table) {
            $table->dropIndex('idx_order');
            $table->dropColumn('order');
        });
    }
};
