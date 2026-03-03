<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('active_warehouse_id')->unsigned()->nullable()->default(null)->after('warehouse_id');
            $table->foreign('active_warehouse_id')->references('id')->on('warehouses')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['active_warehouse_id']);
            $table->dropColumn('active_warehouse_id');
        });
    }
};
