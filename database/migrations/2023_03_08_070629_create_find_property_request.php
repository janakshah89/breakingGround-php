<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('find_property_request', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email");
            $table->text("file");
            $table->string("company")->nullable();
            $table->string("phone");
            $table->boolean('is_active')->default(1)->comment('1=Yes');
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
        Schema::dropIfExists('find_property_request');
    }
};
