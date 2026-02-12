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
        Schema::table('devices', function (Blueprint $table) {
            // Eliminar índice primero
            $table->dropIndex(['parent_id']);
            // Eliminar columna parent_id
            $table->dropColumn('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Recrear parent_id para rollback (como varchar según estructura actual)
            $table->string('parent_id')->nullable()->after('ip_address');
            $table->index('parent_id');
        });
    }
};
