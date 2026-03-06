<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable()->default(null);
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->boolean('show_email_on_invoice')->default(false);
            $table->boolean('show_phone_on_invoice')->default(false);
            $table->string('address')->nullable()->default(null);
            $table->text('terms_condition')->nullable()->default(null);
            $table->text('bank_details')->nullable()->default(null);
            $table->string('signature')->nullable()->default(null);
             $table->boolean('online_store_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
}
