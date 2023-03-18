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
        Schema::create('property_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('property_id')->unsigned()->index();
            $table->text('file');
            $table->string('display_name');
            $table->string('file_type')->comment('image, video, pdf');
            $table->boolean('is_active')->default(1)->comment('1=Yes');
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('property_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_files');
    }
};
