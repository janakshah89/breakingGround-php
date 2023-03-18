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
        Schema::create('property_master', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->boolean('buyorlease')->default('0')->comment("1=Buy");
            $table->unsignedInteger('type');
            $table->unsignedInteger('availability');
            $table->unsignedInteger('location');
            $table->unsignedInteger('micromarket');
            $table->text('description')->nullable();
            $table->double('sqft');
            $table->double('rate')->nullable()->comment('actual price before discount applied');
            $table->double('discount')->nullable();
            $table->double('price');
            $table->string('address');
            $table->string('address1')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->string('lat')->nullable();
            $table->string('lan')->nullable();
            $table->boolean('is_premium')->default(0)->comment("1=Yes");
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_master');
    }
};
