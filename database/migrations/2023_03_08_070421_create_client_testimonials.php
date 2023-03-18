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
        Schema::create('client_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("position")->nullable();
            $table->text("comment");
            $table->text("file");
            $table->unsignedInteger("stars");
            $table->unsignedInteger("orders")->nullable();
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
        Schema::dropIfExists('client_testimonials');
    }
};
